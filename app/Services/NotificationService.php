<?php

namespace App\Services;

use App\Models\User;
use App\Models\EmailPreference;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send a notification to a user if they have enabled it in their preferences.
     */
    public static function sendToUser(User $user, $notification, string $preferenceType = null): bool
    {
        try {
            // Get user's email preferences
            $preferences = EmailPreference::getForUser($user);
            
            // Check if user wants this type of notification
            if ($preferenceType && !$preferences->wantsNotification($preferenceType)) {
                Log::info("User {$user->id} has disabled {$preferenceType} notifications");
                return false;
            }
            
            // Send the notification
            $user->notify($notification);
            
            Log::info("Notification sent to user {$user->id}: " . get_class($notification));
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send notification to user {$user->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send property approval notification.
     */
    public static function sendPropertyApprovedNotification(User $user, $property): bool
    {
        $notification = new \App\Notifications\PropertyApprovedNotification($property);
        return self::sendToUser($user, $notification, 'property_approved');
    }

    /**
     * Send property rejection notification.
     */
    public static function sendPropertyRejectedNotification(User $user, $property, string $reason = null): bool
    {
        $notification = new \App\Notifications\PropertyRejectedNotification($property, $reason);
        return self::sendToUser($user, $notification, 'property_rejected');
    }

    /**
     * Send notification to multiple users.
     */
    public static function sendToUsers(array $users, $notification, string $preferenceType = null): array
    {
        $results = [];
        
        foreach ($users as $user) {
            $results[$user->id] = self::sendToUser($user, $notification, $preferenceType);
        }
        
        return $results;
    }

    /**
     * Send bulk notification to all users of a specific role.
     */
    public static function sendToRole(string $role, $notification, string $preferenceType = null): array
    {
        $users = User::where('role', $role)->where('is_active', true)->get();
        return self::sendToUsers($users->toArray(), $notification, $preferenceType);
    }

    /**
     * Get notification statistics for a user.
     */
    public static function getNotificationStats(User $user): array
    {
        $preferences = EmailPreference::getForUser($user);
        
        return [
            'enabled_notifications' => [
                'property_approved' => $preferences->property_approved,
                'property_rejected' => $preferences->property_rejected,
                'review_approved' => $preferences->review_approved,
                'review_rejected' => $preferences->review_rejected,
                'new_review_received' => $preferences->new_review_received,
                'system_updates' => $preferences->system_updates,
                'account_security' => $preferences->account_security,
            ],
            'frequency' => $preferences->frequency,
            'total_enabled' => collect([
                $preferences->property_approved,
                $preferences->property_rejected,
                $preferences->review_approved,
                $preferences->review_rejected,
                $preferences->new_review_received,
                $preferences->system_updates,
                $preferences->account_security,
            ])->filter()->count(),
        ];
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushNotificationToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Get user's notifications
     */
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $notifications->items(),
                'unread_count' => $request->user()->unreadNotifications()->count(),
                'pagination' => [
                    'total' => $notifications->total(),
                    'per_page' => $notifications->perPage(),
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Get unread notifications
     */
    public function unread(Request $request)
    {
        $notifications = $request->user()
            ->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $notifications->items(),
                'unread_count' => $request->user()->unreadNotifications()->count(),
                'pagination' => [
                    'total' => $notifications->total(),
                    'per_page' => $notifications->perPage(),
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'data' => [
                'notification' => $notification
            ]
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }

    /**
     * Register push notification token
     */
    public function registerToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'platform' => 'required|in:fcm,apns',
            'device_name' => 'nullable|string|max:255',
            'device_id' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Deactivate old tokens for this device
        if ($request->has('device_id')) {
            PushNotificationToken::where('user_id', $request->user()->id)
                ->where('device_id', $request->device_id)
                ->update(['is_active' => false]);
        }

        // Create or update token
        $token = PushNotificationToken::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'token' => $request->token,
            ],
            [
                'platform' => $request->platform,
                'device_name' => $request->device_name,
                'device_id' => $request->device_id,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Push notification token registered successfully',
            'data' => [
                'token' => $token
            ]
        ]);
    }

    /**
     * Unregister push notification token
     */
    public function unregisterToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        PushNotificationToken::where('user_id', $request->user()->id)
            ->where('token', $request->token)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Push notification token unregistered successfully'
        ]);
    }

    /**
     * Get notification settings
     */
    public function settings(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'settings' => [
                    'email_notifications' => $user->email_notifications ?? true,
                    'push_notifications' => $user->push_notifications ?? true,
                    'property_updates' => $user->notify_property_updates ?? true,
                    'message_notifications' => $user->notify_messages ?? true,
                    'review_notifications' => $user->notify_reviews ?? true,
                    'marketing_emails' => $user->marketing_emails ?? false,
                ]
            ]
        ]);
    }

    /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_notifications' => 'sometimes|boolean',
            'push_notifications' => 'sometimes|boolean',
            'property_updates' => 'sometimes|boolean',
            'message_notifications' => 'sometimes|boolean',
            'review_notifications' => 'sometimes|boolean',
            'marketing_emails' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $user->update([
            'email_notifications' => $request->get('email_notifications', $user->email_notifications),
            'push_notifications' => $request->get('push_notifications', $user->push_notifications),
            'notify_property_updates' => $request->get('property_updates', $user->notify_property_updates),
            'notify_messages' => $request->get('message_notifications', $user->notify_messages),
            'notify_reviews' => $request->get('review_notifications', $user->notify_reviews),
            'marketing_emails' => $request->get('marketing_emails', $user->marketing_emails),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated successfully',
            'data' => [
                'settings' => [
                    'email_notifications' => $user->email_notifications,
                    'push_notifications' => $user->push_notifications,
                    'property_updates' => $user->notify_property_updates,
                    'message_notifications' => $user->notify_messages,
                    'review_notifications' => $user->notify_reviews,
                    'marketing_emails' => $user->marketing_emails,
                ]
            ]
        ]);
    }
}

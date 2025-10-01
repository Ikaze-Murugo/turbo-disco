<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEmailPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'receive_announcements',
        'receive_promotions',
        'receive_system_emails',
        'receive_newsletters',
        'receive_property_updates',
        'receive_message_notifications',
        'frequency',
    ];

    protected function casts(): array
    {
        return [
            'receive_announcements' => 'boolean',
            'receive_promotions' => 'boolean',
            'receive_system_emails' => 'boolean',
            'receive_newsletters' => 'boolean',
            'receive_property_updates' => 'boolean',
            'receive_message_notifications' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user should receive emails of a specific category
     */
    public function shouldReceiveEmail(string $category): bool
    {
        return match($category) {
            'announcement' => $this->receive_announcements,
            'promotional' => $this->receive_promotions,
            'system' => $this->receive_system_emails,
            'newsletter' => $this->receive_newsletters,
            default => true,
        };
    }

    /**
     * Get frequency options
     */
    public static function getFrequencyOptions(): array
    {
        return [
            'immediate' => 'Immediate',
            'daily' => 'Daily Digest',
            'weekly' => 'Weekly Digest',
            'monthly' => 'Monthly Digest',
        ];
    }

    /**
     * Create default preferences for a user
     */
    public static function createDefaults(User $user): self
    {
        return self::create([
            'user_id' => $user->id,
            'receive_announcements' => true,
            'receive_promotions' => true,
            'receive_system_emails' => true,
            'receive_newsletters' => true,
            'receive_property_updates' => true,
            'receive_message_notifications' => true,
            'frequency' => 'immediate',
        ]);
    }
}
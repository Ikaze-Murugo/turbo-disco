<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_approved',
        'property_rejected',
        'review_approved',
        'review_rejected',
        'new_review_received',
        'system_updates',
        'account_security',
        'frequency',
    ];

    protected $casts = [
        'property_approved' => 'boolean',
        'property_rejected' => 'boolean',
        'review_approved' => 'boolean',
        'review_rejected' => 'boolean',
        'new_review_received' => 'boolean',
        'system_updates' => 'boolean',
        'account_security' => 'boolean',
    ];

    /**
     * Get the user that owns the email preferences.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user wants to receive a specific type of notification.
     */
    public function wantsNotification(string $type): bool
    {
        return $this->$type ?? true;
    }

    /**
     * Get or create email preferences for a user.
     */
    public static function getForUser(User $user): self
    {
        return static::firstOrCreate(
            ['user_id' => $user->id],
            [
                'property_approved' => true,
                'property_rejected' => true,
                'review_approved' => true,
                'review_rejected' => true,
                'new_review_received' => true,
                'system_updates' => true,
                'account_security' => true,
                'frequency' => 'immediate',
            ]
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportNotification extends Model
{
    use HasFactory;

    protected $table = 'report_notifications';

    protected $fillable = [
        'report_id',
        'user_id',
        'type',
        'title',
        'message',
        'is_read',
        'read_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /**
     * Set the metadata attribute.
     * Ensure it's properly converted to JSON for SQLite compatibility.
     */
    public function setMetadataAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['metadata'] = json_encode($value);
        } else {
            $this->attributes['metadata'] = $value;
        }
    }

    /**
     * Get the metadata attribute.
     * Ensure it's properly converted from JSON string to array.
     */
    public function getMetadataAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($value) ? $value : [];
    }

    // Relationships
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function getFormattedMessageAttribute(): string
    {
        return nl2br(e($this->message));
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'status_change' => '🔄',
            'admin_response' => '💬',
            'resolution' => '✅',
            'follow_up_request' => '📝',
            default => '📢',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'status_change' => 'blue',
            'admin_response' => 'green',
            'resolution' => 'green',
            'follow_up_request' => 'yellow',
            default => 'gray',
        };
    }

    // Static methods
    public static function createForUser($report, $user, $type, $title, $message, $metadata = null): self
    {
        return static::create([
            'report_id' => $report->id,
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'metadata' => $metadata,
        ]);
    }

    public static function createForReporter($report, $type, $title, $message, $metadata = null): self
    {
        return static::createForUser($report, $report->reporter, $type, $title, $message, $metadata);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageReportNotification extends Model
{
    use HasFactory;

    protected $table = 'message_report_notifications';

    protected $fillable = [
        'message_report_id',
        'user_id',
        'type',
        'title',
        'message',
        'metadata',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Relationships
    public function messageReport(): BelongsTo
    {
        return $this->belongsTo(MessageReport::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors and Mutators
    public function setMetadataAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['metadata'] = json_encode($value);
        } else {
            $this->attributes['metadata'] = $value;
        }
    }

    public function getMetadataAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($value) ? $value : [];
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

    // Static methods
    public static function createForReporter($messageReport, $type, $title, $message, $metadata = [])
    {
        return static::create([
            'message_report_id' => $messageReport->id,
            'user_id' => $messageReport->sender_id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'metadata' => $metadata,
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'event_type',
        'page_url',
        'element_id',
        'element_class',
        'scroll_depth',
        'time_on_page',
        'event_data',
        'ip_address',
        'user_agent',
        'referrer',
        'device_type',
        'browser',
        'os',
    ];

    protected $casts = [
        'event_data' => 'array',
        'scroll_depth' => 'integer',
        'time_on_page' => 'integer',
    ];

    /**
     * Get the user that owns the event.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get events for a specific session.
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope to get events of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope to get recent events.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}

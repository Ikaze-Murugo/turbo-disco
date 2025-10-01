<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'resource_type',
        'resource_id',
        'details',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
        ];
    }

    /**
     * Set the details attribute.
     * Ensure it's properly converted to JSON for SQLite compatibility.
     */
    public function setDetailsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['details'] = json_encode($value);
        } else {
            $this->attributes['details'] = $value;
        }
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByResource($query, $type, $id)
    {
        return $query->where('resource_type', $type)->where('resource_id', $id);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Static method to log activity
    public static function log($action, $userId = null, $resourceType = null, $resourceId = null, $details = null)
    {
        return static::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'details' => is_array($details) ? json_encode($details) : $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // Helper methods
    public function getFormattedActionAttribute()
    {
        return str_replace('_', ' ', ucwords($this->action, '_'));
    }

    public function getResourceNameAttribute()
    {
        if (!$this->resource_type || !$this->resource_id) {
            return null;
        }

        $model = match($this->resource_type) {
            'property' => Property::find($this->resource_id),
            'user' => User::find($this->resource_id),
            'message' => Message::find($this->resource_id),
            'report' => Report::find($this->resource_id),
            default => null
        };

        return $model ? $model->title ?? $model->name ?? "ID: {$this->resource_id}" : null;
    }
}
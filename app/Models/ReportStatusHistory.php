<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'report_status_history';

    protected $fillable = [
        'report_id',
        'changed_by',
        'old_status',
        'new_status',
        'old_priority',
        'new_priority',
        'reason',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
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

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Scopes
    public function scopeStatusChanges($query)
    {
        return $query->whereNotNull('old_status');
    }

    public function scopePriorityChanges($query)
    {
        return $query->whereNotNull('old_priority');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('changed_by', $userId);
    }

    // Helper methods
    public function getStatusChangeDescriptionAttribute(): string
    {
        if ($this->old_status && $this->new_status) {
            return "Status changed from {$this->old_status} to {$this->new_status}";
        }
        
        if ($this->old_priority && $this->new_priority) {
            return "Priority changed from {$this->old_priority} to {$this->new_priority}";
        }
        
        return "Status updated to {$this->new_status}";
    }

    public function getFormattedReasonAttribute(): string
    {
        return $this->reason ? nl2br(e($this->reason)) : 'No reason provided';
    }

    public function isStatusChange(): bool
    {
        return !is_null($this->old_status) && !is_null($this->new_status);
    }

    public function isPriorityChange(): bool
    {
        return !is_null($this->old_priority) && !is_null($this->new_priority);
    }
}
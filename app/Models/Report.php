<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'reported_user_id',
        'reported_property_id',
        'reported_message_id',
        'report_type',
        'category',
        'title',
        'description',
        'evidence_urls',
        'status',
        'priority',
        'admin_notes',
        'resolved_by',
        'resolved_at',
        'resolution_actions',
    ];

    protected function casts(): array
    {
        return [
            'evidence_urls' => 'array',
            'resolution_actions' => 'array',
            'resolved_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the evidence_urls attribute.
     * Ensure it's properly converted from JSON string to array.
     */
    public function getEvidenceUrlsAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($value) ? $value : [];
    }

    /**
     * Get the resolution_actions attribute.
     * Ensure it's properly converted from JSON string to array.
     */
    public function getResolutionActionsAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($value) ? $value : [];
    }

    /**
     * Get the resolved_at attribute.
     * Ensure it's always returned as a Carbon instance.
     */
    public function getResolvedAtAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }
        
        if ($value instanceof \Carbon\Carbon) {
            return $value;
        }
        
        try {
            return \Carbon\Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Set the evidence_urls attribute.
     * Ensure it's properly converted to JSON for SQLite compatibility.
     */
    public function setEvidenceUrlsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['evidence_urls'] = json_encode($value);
        } else {
            $this->attributes['evidence_urls'] = $value;
        }
    }

    /**
     * Set the resolution_actions attribute.
     * Ensure it's properly converted to JSON for SQLite compatibility.
     */
    public function setResolutionActionsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['resolution_actions'] = json_encode($value);
        } else {
            $this->attributes['resolution_actions'] = $value;
        }
    }

    // Relationships
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function reportedProperty(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'reported_property_id');
    }

    // Alias for backward compatibility
    public function property(): BelongsTo
    {
        return $this->reportedProperty();
    }

    public function reportedMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reported_message_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // New relationships for enhanced reporting system
    public function comments()
    {
        return $this->hasMany(ReportComment::class);
    }

    public function publicComments()
    {
        return $this->hasMany(ReportComment::class)->public();
    }

    public function internalComments()
    {
        return $this->hasMany(ReportComment::class)->internal();
    }

    public function statusHistory()
    {
        return $this->hasMany(ReportStatusHistory::class);
    }

    public function notifications()
    {
        return $this->hasMany(ReportNotification::class);
    }

    public function messageReport()
    {
        return $this->hasOne(MessageReport::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInvestigating($query)
    {
        return $query->where('status', 'investigating');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    // Accessor methods
    public function getFormattedStatusAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending Review',
            'investigating' => 'Under Investigation',
            'resolved' => 'Resolved',
            'dismissed' => 'Dismissed',
            default => ucfirst($this->status)
        };
    }

    public function getFormattedPriorityAttribute()
    {
        return match($this->priority) {
            'low' => 'Low Priority',
            'medium' => 'Medium Priority',
            'high' => 'High Priority',
            'urgent' => 'Urgent',
            default => ucfirst($this->priority)
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'investigating' => 'blue',
            'resolved' => 'green',
            'dismissed' => 'gray',
            default => 'gray'
        };
    }

    // Enhanced helper methods for ticketing system
    public function addComment($comment, $isInternal = false, $isAdminComment = false)
    {
        return $this->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $comment,
            'is_internal' => $isInternal,
            'is_admin_comment' => $isAdminComment,
        ]);
    }

    public function updateStatus($newStatus, $newPriority = null, $reason = null, $metadata = null)
    {
        $oldStatus = $this->status;
        $oldPriority = $this->priority;

        // Update the report
        $updateData = ['status' => $newStatus];
        if ($newPriority) {
            $updateData['priority'] = $newPriority;
        }
        if ($newStatus === 'resolved') {
            $updateData['resolved_by'] = auth()->id();
            $updateData['resolved_at'] = now();
        }

        $this->update($updateData);

        // Record status change in history
        $this->statusHistory()->create([
            'changed_by' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'old_priority' => $oldPriority,
            'new_priority' => $newPriority,
            'reason' => $reason,
            'metadata' => $metadata,
        ]);

        // Create notification for reporter
        if ($oldStatus !== $newStatus) {
            $this->notifyReporterOfStatusChange($newStatus, $reason);
        }

        return $this;
    }

    public function notifyReporterOfStatusChange($newStatus, $reason = null)
    {
        $title = "Report Status Updated";
        $message = "Your report has been updated to: " . ucfirst($newStatus);
        if ($reason) {
            $message .= "\n\nReason: " . $reason;
        }

        ReportNotification::createForReporter($this, 'status_change', $title, $message, [
            'old_status' => $this->status,
            'new_status' => $newStatus,
        ]);
    }

    public function getLatestComment()
    {
        return $this->comments()->latest()->first();
    }

    public function getPublicComments()
    {
        return $this->comments()->public()->orderBy('created_at')->get();
    }

    public function getInternalComments()
    {
        return $this->comments()->internal()->orderBy('created_at')->get();
    }

    public function hasUnreadNotifications()
    {
        return $this->notifications()->unread()->exists();
    }

    public function getUnreadNotificationsCount()
    {
        return $this->notifications()->unread()->count();
    }

    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    public function isDismissed()
    {
        return $this->status === 'dismissed';
    }

    public function isActive()
    {
        return in_array($this->status, ['pending', 'investigating']);
    }

    public function getDaysSinceCreated()
    {
        return $this->created_at->diffInDays(now());
    }

    public function getDaysSinceLastUpdate()
    {
        $lastUpdate = $this->updated_at;
        if ($this->comments()->exists()) {
            $lastComment = $this->comments()->latest()->first();
            if ($lastComment && $lastComment->created_at > $lastUpdate) {
                $lastUpdate = $lastComment->created_at;
            }
        }
        return $lastUpdate->diffInDays(now());
    }

    // Helper methods

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isHighPriority()
    {
        return in_array($this->priority, ['high', 'urgent']);
    }

    public function getReportedResource()
    {
        return match($this->report_type) {
            'property' => $this->reportedProperty,
            'user' => $this->reportedUser,
            'message' => $this->reportedMessage,
            default => null
        };
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MessageReport extends Model
{
    use HasFactory;

    protected $table = 'message_reports';

    protected $fillable = [
        'report_id',
        'message_id',
        'conversation_id',
        'message_content',
        'sender_id',
        'recipient_id',
        'report_type',
        'category',
        'title',
        'description',
        'evidence_urls',
        'priority',
        'status',
        'assigned_to',
        'resolved_at',
        'resolved_by',
        'resolution_actions',
        'resolution_notes',
    ];

    protected $casts = [
        'evidence_urls' => 'array',
        'resolution_actions' => 'array',
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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

    // Relationships
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(MessageReportComment::class);
    }

    public function publicComments(): HasMany
    {
        return $this->hasMany(MessageReportComment::class)->where('is_internal', false);
    }

    public function internalComments(): HasMany
    {
        return $this->hasMany(MessageReportComment::class)->where('is_internal', true);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(MessageReportStatusHistory::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(MessageReportNotification::class);
    }

    // Accessors and Mutators
    public function setEvidenceUrlsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['evidence_urls'] = json_encode($value);
        } else {
            $this->attributes['evidence_urls'] = $value;
        }
    }

    public function getEvidenceUrlsAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($value) ? $value : [];
    }

    public function setResolutionActionsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['resolution_actions'] = json_encode($value);
        } else {
            $this->attributes['resolution_actions'] = $value;
        }
    }

    public function getResolutionActionsAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($value) ? $value : [];
    }

    // Helper Methods
    public function addComment($comment, $userId, $isInternal = false, $isAdminComment = false)
    {
        return $this->comments()->create([
            'user_id' => $userId,
            'comment' => $comment,
            'is_internal' => $isInternal,
            'is_admin_comment' => $isAdminComment,
        ]);
    }

    public function updateStatus($newStatus, $newPriority = null, $reason = null, $changedBy = null, $metadata = [])
    {
        $oldStatus = $this->status;
        $oldPriority = $this->priority;

        $this->status = $newStatus;
        if ($newPriority) {
            $this->priority = $newPriority;
        }
        $this->save();

        // Record status change
        $this->statusHistory()->create([
            'changed_by' => $changedBy ?? auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'old_priority' => $oldPriority,
            'new_priority' => $newPriority ?? $oldPriority,
            'reason' => $reason,
            'metadata' => $metadata,
        ]);

        // Notify reporter
        $this->notifyReporterOfStatusChange($newStatus, $reason);
    }

    public function notifyReporterOfStatusChange($status, $reason = null)
    {
        $statusMessages = [
            'pending' => 'Your message report is pending review.',
            'investigating' => 'We are investigating your message report.',
            'resolved' => 'Your message report has been resolved.',
            'dismissed' => 'Your message report has been dismissed.',
        ];

        $this->notifications()->create([
            'user_id' => $this->sender_id,
            'type' => 'status_change',
            'title' => 'Message Report Status Updated',
            'message' => $statusMessages[$status] . ($reason ? " Reason: {$reason}" : ''),
            'metadata' => [
                'status' => $status,
                'reason' => $reason,
            ],
        ]);
    }

    public function getLatestComment()
    {
        return $this->comments()->latest()->first();
    }

    public function getPublicComments()
    {
        return $this->publicComments()->with('user')->orderBy('created_at', 'asc')->get();
    }

    public function getInternalComments()
    {
        return $this->internalComments()->with('user')->orderBy('created_at', 'asc')->get();
    }

    public function hasUnreadNotifications()
    {
        return $this->notifications()->where('is_read', false)->exists();
    }

    public function getUnreadNotificationsCount()
    {
        return $this->notifications()->where('is_read', false)->count();
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
        return $this->updated_at->diffInDays(now());
    }

    public function isPending()
    {
        return $this->status === 'pending';
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

    public function scopeDismissed($query)
    {
        return $query->where('status', 'dismissed');
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }
}
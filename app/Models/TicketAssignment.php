<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'priority',
        'notes',
        'status',
        'completed_at'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the report that this assignment belongs to.
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get the user this ticket is assigned to.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who assigned this ticket.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Scope to get active assignments.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['assigned', 'in_progress']);
    }

    /**
     * Scope to get assignments by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get assignments by priority.
     */
    public function scopeByPriority($query, int $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to get assignments for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Mark assignment as completed.
     */
    public function markCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Reassign ticket to another user.
     */
    public function reassign(int $newUserId, int $assignedBy, string $notes = null): void
    {
        $this->update([
            'assigned_to' => $newUserId,
            'assigned_by' => $assignedBy,
            'assigned_at' => now(),
            'status' => 'assigned',
            'notes' => $notes,
        ]);
    }
}

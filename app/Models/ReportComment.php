<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportComment extends Model
{
    use HasFactory;

    protected $table = 'report_comments';

    protected $fillable = [
        'report_id',
        'user_id',
        'comment',
        'is_internal',
        'is_admin_comment',
    ];

    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
            'is_admin_comment' => 'boolean',
        ];
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
    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    public function scopeAdminComments($query)
    {
        return $query->where('is_admin_comment', true);
    }

    public function scopeUserComments($query)
    {
        return $query->where('is_admin_comment', false);
    }

    // Helper methods
    public function isFromAdmin(): bool
    {
        return $this->user->isAdmin();
    }

    public function isFromReporter(): bool
    {
        return $this->user_id === $this->report->reporter_id;
    }

    public function getFormattedCommentAttribute(): string
    {
        return nl2br(e($this->comment));
    }
}
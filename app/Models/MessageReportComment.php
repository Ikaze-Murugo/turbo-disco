<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageReportComment extends Model
{
    use HasFactory;

    protected $table = 'message_report_comments';

    protected $fillable = [
        'message_report_id',
        'user_id',
        'comment',
        'is_internal',
        'is_admin_comment',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'is_admin_comment' => 'boolean',
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
}

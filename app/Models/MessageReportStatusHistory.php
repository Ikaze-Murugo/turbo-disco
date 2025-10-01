<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageReportStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'message_report_status_history';

    protected $fillable = [
        'message_report_id',
        'changed_by',
        'old_status',
        'new_status',
        'old_priority',
        'new_priority',
        'reason',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Relationships
    public function messageReport(): BelongsTo
    {
        return $this->belongsTo(MessageReport::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'template_id',
        'subject',
        'content',
        'target_audience',
        'target_criteria',
        'status',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'delivered_count',
        'opened_count',
        'clicked_count',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'target_criteria' => 'array',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'template_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(EmailRecipient::class, 'campaign_id');
    }

    /**
     * Get campaign statistics
     */
    public function getStats(): array
    {
        return [
            'total_recipients' => $this->total_recipients,
            'delivered_count' => $this->delivered_count,
            'opened_count' => $this->opened_count,
            'clicked_count' => $this->clicked_count,
            'delivery_rate' => $this->total_recipients > 0 ? round(($this->delivered_count / $this->total_recipients) * 100, 2) : 0,
            'open_rate' => $this->delivered_count > 0 ? round(($this->opened_count / $this->delivered_count) * 100, 2) : 0,
            'click_rate' => $this->delivered_count > 0 ? round(($this->clicked_count / $this->delivered_count) * 100, 2) : 0,
        ];
    }

    /**
     * Check if campaign can be sent
     */
    public function canBeSent(): bool
    {
        return $this->status === 'draft' || $this->status === 'scheduled';
    }

    /**
     * Get status badge color
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'scheduled' => 'yellow',
            'sending' => 'blue',
            'sent' => 'green',
            'failed' => 'red',
            default => 'gray',
        };
    }
}
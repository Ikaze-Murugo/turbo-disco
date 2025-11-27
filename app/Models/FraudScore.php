<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FraudScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'scoreable_type',
        'scoreable_id',
        'fraud_score',
        'risk_level',
        'risk_factors',
        'score_breakdown',
        'model_version',
        'is_flagged',
        'admin_reviewed',
        'reviewed_by',
        'reviewed_at',
        'admin_notes',
    ];

    protected $casts = [
        'fraud_score' => 'integer',
        'risk_factors' => 'array',
        'score_breakdown' => 'array',
        'is_flagged' => 'boolean',
        'admin_reviewed' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the owning scoreable model (User or Property).
     */
    public function scoreable()
    {
        return $this->morphTo();
    }

    /**
     * Get the admin who reviewed this score.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get risk level color for UI.
     */
    public function getRiskLevelColorAttribute()
    {
        return match($this->risk_level) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray',
        };
    }

    /**
     * Scope to get flagged scores.
     */
    public function scopeFlagged($query)
    {
        return $query->where('is_flagged', true);
    }

    /**
     * Scope to get unreviewed scores.
     */
    public function scopeUnreviewed($query)
    {
        return $query->where('admin_reviewed', false);
    }

    /**
     * Scope to get high-risk scores.
     */
    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', ['high', 'critical']);
    }

    /**
     * Scope to get scores for users.
     */
    public function scopeForUsers($query)
    {
        return $query->where('scoreable_type', 'App\\Models\\User');
    }

    /**
     * Scope to get scores for properties.
     */
    public function scopeForProperties($query)
    {
        return $query->where('scoreable_type', 'App\\Models\\Property');
    }

    /**
     * Mark as reviewed by admin.
     */
    public function markAsReviewed($adminId, $notes = null)
    {
        $this->update([
            'admin_reviewed' => true,
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);
    }
}

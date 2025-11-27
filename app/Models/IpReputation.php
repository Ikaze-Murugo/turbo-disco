<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpReputation extends Model
{
    use HasFactory;

    protected $table = 'ip_reputation';

    protected $fillable = [
        'ip_address',
        'risk_score',
        'country_code',
        'isp',
        'is_proxy',
        'is_vpn',
        'is_tor',
        'is_datacenter',
        'abuse_confidence_score',
        'additional_data',
        'last_checked_at',
    ];

    protected $casts = [
        'risk_score' => 'integer',
        'is_proxy' => 'boolean',
        'is_vpn' => 'boolean',
        'is_tor' => 'boolean',
        'is_datacenter' => 'boolean',
        'abuse_confidence_score' => 'integer',
        'additional_data' => 'array',
        'last_checked_at' => 'datetime',
    ];

    /**
     * Check if IP is considered risky.
     */
    public function isRisky($threshold = 50)
    {
        return $this->risk_score >= $threshold;
    }

    /**
     * Check if IP is from anonymizing service.
     */
    public function isAnonymous()
    {
        return $this->is_proxy || $this->is_vpn || $this->is_tor;
    }

    /**
     * Scope to get high-risk IPs.
     */
    public function scopeHighRisk($query, $threshold = 70)
    {
        return $query->where('risk_score', '>=', $threshold);
    }

    /**
     * Scope to get anonymous IPs.
     */
    public function scopeAnonymous($query)
    {
        return $query->where(function ($q) {
            $q->where('is_proxy', true)
              ->orWhere('is_vpn', true)
              ->orWhere('is_tor', true);
        });
    }

    /**
     * Scope to get stale records that need updating.
     */
    public function scopeStale($query, $days = 30)
    {
        return $query->where(function ($q) use ($days) {
            $q->whereNull('last_checked_at')
              ->orWhere('last_checked_at', '<', now()->subDays($days));
        });
    }
}

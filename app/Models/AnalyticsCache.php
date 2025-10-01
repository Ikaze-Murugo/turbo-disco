<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AnalyticsCache extends Model
{
    use HasFactory;

    protected $fillable = [
        'cache_key',
        'data',
        'expires_at'
    ];

    protected $casts = [
        'data' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * Get cached data by key.
     */
    public static function getCached(string $key)
    {
        $cache = static::where('cache_key', $key)
                      ->where('expires_at', '>', now())
                      ->first();

        return $cache ? $cache->data : null;
    }

    /**
     * Store data in cache.
     */
    public static function store(string $key, $data, int $minutes = 60): void
    {
        static::updateOrCreate(
            ['cache_key' => $key],
            [
                'data' => $data,
                'expires_at' => now()->addMinutes($minutes),
            ]
        );
    }

    /**
     * Clear expired cache entries.
     */
    public static function clearExpired(): int
    {
        return static::where('expires_at', '<', now())->delete();
    }

    /**
     * Clear all cache entries.
     */
    public static function clearAll(): int
    {
        return static::truncate();
    }

    /**
     * Scope to get expired entries.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope to get valid entries.
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now());
    }
}

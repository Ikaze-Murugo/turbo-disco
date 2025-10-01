<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'search_query',
        'filters',
        'results_count',
        'session_id',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get popular search terms
     */
    public static function getPopularSearches($limit = 10)
    {
        return static::whereNotNull('search_query')
            ->where('search_query', '!=', '')
            ->selectRaw('search_query, COUNT(*) as count')
            ->groupBy('search_query')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent searches for a user
     */
    public static function getRecentSearches($userId, $limit = 10)
    {
        return static::where('user_id', $userId)
            ->whereNotNull('search_query')
            ->where('search_query', '!=', '')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get popular filters
     */
    public static function getPopularFilters($limit = 10)
    {
        return static::whereNotNull('filters')
            ->get()
            ->pluck('filters')
            ->flatten()
            ->countBy()
            ->sortDesc()
            ->take($limit);
    }
}
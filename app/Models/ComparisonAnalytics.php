<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class ComparisonAnalytics extends Model
{
    protected $fillable = [
        'user_id',
        'property_ids',
        'session_id',
        'ip_address',
        'user_agent',
        'comparison_duration',
        'properties_viewed',
        'comparison_completed',
        'conversion_type',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'property_ids' => 'array',
        'comparison_completed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship with User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get comparison analytics with caching
     */
    public static function getAnalytics(int $days = 30): array
    {
        $cacheKey = "comparison_analytics_{$days}";
        
        return Cache::remember($cacheKey, 3600, function () use ($days) {
            $startDate = now()->subDays($days);
            
            $analytics = self::where('created_at', '>=', $startDate)
                ->selectRaw('
                    COUNT(*) as total_comparisons,
                    COUNT(DISTINCT user_id) as unique_users,
                    COUNT(DISTINCT session_id) as unique_sessions,
                    AVG(comparison_duration) as avg_duration,
                    COUNT(CASE WHEN comparison_completed = 1 THEN 1 END) as completed_comparisons,
                    COUNT(CASE WHEN conversion_type IS NOT NULL THEN 1 END) as conversions
                ')
                ->first();

            $popularProperties = self::where('created_at', '>=', $startDate)
                ->get()
                ->pluck('property_ids')
                ->flatten()
                ->countBy()
                ->sortDesc()
                ->take(10);

            $dailyComparisons = self::where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return [
                'total_comparisons' => $analytics->total_comparisons ?? 0,
                'unique_users' => $analytics->unique_users ?? 0,
                'unique_sessions' => $analytics->unique_sessions ?? 0,
                'avg_duration' => round($analytics->avg_duration ?? 0, 2),
                'completion_rate' => $analytics->total_comparisons > 0 
                    ? round(($analytics->completed_comparisons / $analytics->total_comparisons) * 100, 2)
                    : 0,
                'conversion_rate' => $analytics->total_comparisons > 0
                    ? round(($analytics->conversions / $analytics->total_comparisons) * 100, 2)
                    : 0,
                'popular_properties' => $popularProperties,
                'daily_comparisons' => $dailyComparisons
            ];
        });
    }

    /**
     * Track comparison start
     */
    public static function trackComparisonStart(array $propertyIds, ?int $userId = null, ?string $sessionId = null): self
    {
        return self::create([
            'user_id' => $userId,
            'property_ids' => $propertyIds,
            'session_id' => $sessionId ?? session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'comparison_duration' => 0,
            'properties_viewed' => count($propertyIds),
            'comparison_completed' => false
        ]);
    }

    /**
     * Track comparison completion
     */
    public function trackCompletion(string $conversionType = null): void
    {
        $this->update([
            'comparison_completed' => true,
            'conversion_type' => $conversionType,
            'comparison_duration' => $this->created_at->diffInSeconds(now())
        ]);

        // Clear analytics cache
        Cache::forget('comparison_analytics_30');
        Cache::forget('comparison_analytics_7');
    }

    /**
     * Get property comparison insights
     */
    public static function getPropertyInsights(int $propertyId, int $days = 30): array
    {
        $cacheKey = "property_comparison_insights_{$propertyId}_{$days}";
        
        return Cache::remember($cacheKey, 1800, function () use ($propertyId, $days) {
            $startDate = now()->subDays($days);
            
            $comparisons = self::where('created_at', '>=', $startDate)
                ->whereJsonContains('property_ids', $propertyId)
                ->get();

            $totalComparisons = $comparisons->count();
            $completedComparisons = $comparisons->where('comparison_completed', true)->count();
            $conversions = $comparisons->whereNotNull('conversion_type')->count();

            // Find commonly compared properties
            $commonlyCompared = $comparisons
                ->pluck('property_ids')
                ->flatten()
                ->reject(fn($id) => $id == $propertyId)
                ->countBy()
                ->sortDesc()
                ->take(5);

            return [
                'total_comparisons' => $totalComparisons,
                'completion_rate' => $totalComparisons > 0 
                    ? round(($completedComparisons / $totalComparisons) * 100, 2)
                    : 0,
                'conversion_rate' => $totalComparisons > 0
                    ? round(($conversions / $totalComparisons) * 100, 2)
                    : 0,
                'commonly_compared_with' => $commonlyCompared,
                'avg_duration' => $comparisons->avg('comparison_duration') ?? 0
            ];
        });
    }

    /**
     * Get user comparison behavior
     */
    public static function getUserBehavior(int $userId, int $days = 30): array
    {
        $cacheKey = "user_comparison_behavior_{$userId}_{$days}";
        
        return Cache::remember($cacheKey, 1800, function () use ($userId, $days) {
            $startDate = now()->subDays($days);
            
            $userComparisons = self::where('user_id', $userId)
                ->where('created_at', '>=', $startDate)
                ->get();

            $totalComparisons = $userComparisons->count();
            $completedComparisons = $userComparisons->where('comparison_completed', true)->count();
            $conversions = $userComparisons->whereNotNull('conversion_type')->count();

            $favoritePropertyTypes = $userComparisons
                ->pluck('property_ids')
                ->flatten()
                ->map(fn($id) => Property::find($id)?->type)
                ->filter()
                ->countBy()
                ->sortDesc();

            return [
                'total_comparisons' => $totalComparisons,
                'completion_rate' => $totalComparisons > 0 
                    ? round(($completedComparisons / $totalComparisons) * 100, 2)
                    : 0,
                'conversion_rate' => $totalComparisons > 0
                    ? round(($conversions / $totalComparisons) * 100, 2)
                    : 0,
                'favorite_property_types' => $favoritePropertyTypes,
                'avg_comparison_duration' => $userComparisons->avg('comparison_duration') ?? 0,
                'most_compared_properties' => $userComparisons
                    ->pluck('property_ids')
                    ->flatten()
                    ->countBy()
                    ->sortDesc()
                    ->take(10)
            ];
        });
    }
}

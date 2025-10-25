<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class PropertySearchService
{
    /**
     * Cache duration in minutes
     */
    const CACHE_DURATION = 60;
    
    /**
     * Search properties with advanced filtering and caching
     */
    public function search(Request $request, int $perPage = 12): LengthAwarePaginator
    {
        $cacheKey = $this->generateCacheKey($request);
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($request, $perPage) {
            return $this->performSearch($request, $perPage);
        });
    }
    
    /**
     * Perform the actual search without caching
     */
    private function performSearch(Request $request, int $perPage): LengthAwarePaginator
    {
        $query = Property::with(['images', 'landlord', 'nearbyAmenities'])
            ->where('status', 'active')
            ->where('is_available', true);
        
        // Apply filters
        $query = $this->applyFilters($query, $request);
        
        // Apply sorting
        $query = $this->applySorting($query, $request);
        
        return $query->paginate($perPage);
    }
    
    /**
     * Apply all filters to the query
     */
    private function applyFilters($query, Request $request)
    {
        // Full-text search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                if (DB::getDriverName() === 'pgsql') {
                    // Use PostgreSQL full-text search
                    $q->whereRaw("to_tsvector('english', title) @@ plainto_tsquery('english', ?)", [$search])
                      ->orWhereRaw("to_tsvector('english', description) @@ plainto_tsquery('english', ?)", [$search])
                      ->orWhereRaw("to_tsvector('english', address) @@ plainto_tsquery('english', ?)", [$search])
                      ->orWhereRaw("to_tsvector('english', neighborhood) @@ plainto_tsquery('english', ?)", [$search]);
                } else {
                    // Fallback to LIKE queries for other databases
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%")
                      ->orWhere('neighborhood', 'like', "%{$search}%");
                }
            });
        }
        
        // Location filters
        if ($request->filled('location')) {
            $query->where('neighborhood', 'like', "%{$request->location}%");
        }
        
        if ($request->filled('city')) {
            $query->where('address', 'like', "%{$request->city}%");
        }
        
        // Property type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Bedrooms
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }
        
        // Bathrooms
        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', '>=', $request->bathrooms);
        }
        
        // Area range
        if ($request->filled('min_area')) {
            $query->where('area', '>=', $request->min_area);
        }
        
        if ($request->filled('max_area')) {
            $query->where('area', '<=', $request->max_area);
        }
        
        // Furnishing status
        if ($request->filled('furnishing_status')) {
            $query->where('furnishing_status', $request->furnishing_status);
        }
        
        // Parking spaces
        if ($request->filled('parking_spaces')) {
            $query->where('parking_spaces', '>=', $request->parking_spaces);
        }
        
        // Property amenities (boolean flags)
        $amenityFlags = [
            'has_balcony', 'has_garden', 'has_pool', 'has_gym', 'has_security',
            'has_elevator', 'has_air_conditioning', 'has_heating', 'has_internet', 'has_cable_tv'
        ];
        
        foreach ($amenityFlags as $flag) {
            if ($request->filled($flag)) {
                $query->where($flag, true);
            }
        }
        
        // Policy filters
        if ($request->filled('pets_allowed')) {
            $query->where('pets_allowed', true);
        }
        
        if ($request->filled('smoking_allowed')) {
            $query->where('smoking_allowed', true);
        }
        
        // Nearby amenities
        if ($request->filled('nearby_amenities')) {
            $amenities = is_array($request->nearby_amenities) ? $request->nearby_amenities : [$request->nearby_amenities];
            $query->whereHas('nearbyAmenities', function($q) use ($amenities) {
                $q->whereIn('amenities.id', $amenities);
            });
        }
        
        // Distance from amenities
        if ($request->filled('max_distance')) {
            $query->whereHas('nearbyAmenities', function($q) use ($request) {
                $q->where('distance_km', '<=', $request->max_distance);
            });
        }
        
        // Featured properties only
        if ($request->filled('featured_only')) {
            $query->where('is_featured', true);
        }
        
        // New properties only (created within last 7 days)
        if ($request->filled('new_only')) {
            $query->where('created_at', '>=', now()->subDays(7));
        }
        
        return $query;
    }
    
    /**
     * Apply sorting to the query
     */
    private function applySorting($query, Request $request)
    {
        $sortBy = $request->get('sort', 'priority');
        $sortOrder = $request->get('order', 'desc');
        
        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $sortOrder);
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'bedrooms':
                $query->orderBy('bedrooms', $sortOrder);
                break;
            case 'bathrooms':
                $query->orderBy('bathrooms', $sortOrder);
                break;
            case 'area':
                $query->orderBy('area', $sortOrder);
                break;
            case 'views':
                $query->orderBy('view_count', $sortOrder);
                break;
            case 'priority':
            default:
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('priority', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
        }
        
        return $query;
    }
    
    /**
     * Get filter options with caching
     */
    public function getFilterOptions(): array
    {
        return Cache::remember('property_filter_options', self::CACHE_DURATION, function () {
            return [
                'types' => Property::where('status', 'active')
                    ->distinct()
                    ->pluck('type')
                    ->filter()
                    ->values(),
                
                'furnishing_statuses' => Property::where('status', 'active')
                    ->distinct()
                    ->pluck('furnishing_status')
                    ->filter()
                    ->values(),
                
                'neighborhoods' => Property::where('status', 'active')
                    ->distinct()
                    ->pluck('neighborhood')
                    ->filter()
                    ->values(),
                
                'amenities' => Amenity::where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name', 'type']),
                
                'price_range' => [
                    'min' => Property::where('status', 'active')->min('price') ?? 0,
                    'max' => Property::where('status', 'active')->max('price') ?? 10000000,
                ],
                
                'area_range' => [
                    'min' => Property::where('status', 'active')->min('area') ?? 0,
                    'max' => Property::where('status', 'active')->max('area') ?? 1000,
                ],
                
                'bedrooms_range' => [
                    'min' => Property::where('status', 'active')->min('bedrooms') ?? 0,
                    'max' => Property::where('status', 'active')->max('bedrooms') ?? 10,
                ],
                
                'bathrooms_range' => [
                    'min' => Property::where('status', 'active')->min('bathrooms') ?? 0,
                    'max' => Property::where('status', 'active')->max('bathrooms') ?? 10,
                ],
            ];
        });
    }
    
    /**
     * Get search suggestions with caching
     */
    public function getSearchSuggestions(string $query = null): array
    {
        if (empty($query)) {
            return [];
        }
        
        $cacheKey = "search_suggestions_" . md5($query);
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($query) {
            $suggestions = [];
            
            // Location suggestions
            $locations = Property::where('status', 'active')
                ->where(function($q) use ($query) {
                    $q->where('neighborhood', 'like', "%{$query}%")
                      ->orWhere('address', 'like', "%{$query}%")
                      ->orWhere('location', 'like', "%{$query}%");
                })
                ->distinct()
                ->pluck('neighborhood')
                ->take(5);
            
            foreach ($locations as $location) {
                $suggestions[] = [
                    'type' => 'location',
                    'text' => $location,
                    'value' => $location,
                ];
            }
            
            // Property type suggestions
            $types = Property::where('status', 'active')
                ->where('type', 'like', "%{$query}%")
                ->distinct()
                ->pluck('type')
                ->take(3);
            
            foreach ($types as $type) {
                $suggestions[] = [
                    'type' => 'property_type',
                    'text' => ucfirst($type),
                    'value' => $type,
                ];
            }
            
            return $suggestions;
        });
    }
    
    /**
     * Generate cache key for search results
     */
    private function generateCacheKey(Request $request): string
    {
        $params = $request->except(['page']);
        ksort($params);
        return 'property_search_' . md5(serialize($params));
    }
    
    /**
     * Clear search cache
     */
    public function clearCache(): void
    {
        Cache::forget('property_filter_options');
        
        // Clear search result cache (this is more complex in production)
        // In production, you might want to use cache tags
        $keys = Cache::getRedis()->keys('property_search_*');
        if (!empty($keys)) {
            Cache::getRedis()->del($keys);
        }
    }
    
    /**
     * Get search analytics
     */
    public function getSearchAnalytics(): array
    {
        return Cache::remember('search_analytics', 300, function () {
            return [
                'total_properties' => Property::where('status', 'active')->count(),
                'featured_properties' => Property::where('status', 'active')->where('is_featured', true)->count(),
                'new_properties' => Property::where('status', 'active')->where('created_at', '>=', now()->subDays(7))->count(),
                'average_price' => Property::where('status', 'active')->avg('price'),
                'price_range' => [
                    'min' => Property::where('status', 'active')->min('price'),
                    'max' => Property::where('status', 'active')->max('price'),
                ],
            ];
        });
    }
}

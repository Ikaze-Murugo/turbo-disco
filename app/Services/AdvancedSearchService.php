<?php

namespace App\Services;

use App\Models\Property;
use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdvancedSearchService
{
    protected $cachePrefix = 'advanced_search_';
    protected $cacheTtl = 3600; // 1 hour

    /**
     * Perform advanced property search with multiple criteria
     */
    public function search(Request $request, $limit = 20)
    {
        $cacheKey = $this->generateCacheKey($request);
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($request, $limit) {
            return $this->performSearch($request, $limit);
        });
    }

    /**
     * Perform the actual search without caching
     */
    protected function performSearch(Request $request, $limit)
    {
        $query = Property::query()
            ->with(['images', 'landlord', 'amenities'])
            ->where('status', 'active');

        // Apply search criteria
        $this->applySearchCriteria($query, $request);
        
        // Apply filters
        $this->applyFilters($query, $request);
        
        // Apply sorting
        $this->applySorting($query, $request);
        
        // Apply pagination
        $properties = $query->paginate($limit);
        
        // Log search for analytics
        $this->logSearch($request, $properties->total());
        
        return $properties;
    }

    /**
     * Apply search criteria (text search, location, etc.)
     */
    protected function applySearchCriteria($query, Request $request)
    {
        // Text search
        if ($request->filled('q')) {
            $searchTerm = $request->get('q');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('location', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('neighborhood', 'ILIKE', "%{$searchTerm}%");
            });
        }

        // Location-based search
        if ($request->filled('lat') && $request->filled('lng')) {
            $lat = $request->get('lat');
            $lng = $request->get('lng');
            $radius = $request->get('radius', 10); // Default 10km radius
            
            $query->whereRaw("
                ST_DWithin(
                    location_point,
                    ST_SetSRID(ST_MakePoint(?, ?), 4326),
                    ? * 1000
                )
            ", [$lng, $lat, $radius]);
        }

        // Polygon search
        if ($request->filled('polygon')) {
            $polygon = json_decode($request->get('polygon'), true);
            if (is_array($polygon) && count($polygon) >= 3) {
                $polygonWkt = $this->createPolygonWKT($polygon);
                $query->whereRaw("
                    ST_Within(
                        location_point,
                        ST_SetSRID(ST_GeomFromText(?), 4326)
                    )
                ", [$polygonWkt]);
            }
        }

        // Bounding box search
        if ($request->filled('bounds')) {
            $bounds = json_decode($request->get('bounds'), true);
            if (is_array($bounds) && count($bounds) === 4) {
                $query->whereBetween('latitude', [$bounds['south'], $bounds['north']])
                      ->whereBetween('longitude', [$bounds['west'], $bounds['east']]);
            }
        }
    }

    /**
     * Apply property filters
     */
    protected function applyFilters($query, Request $request)
    {
        // Property type
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->get('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->get('max_price'));
        }

        // Bedrooms
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->get('bedrooms'));
        }

        // Bathrooms
        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', '>=', $request->get('bathrooms'));
        }

        // Area range
        if ($request->filled('min_area')) {
            $query->where('area', '>=', $request->get('min_area'));
        }
        if ($request->filled('max_area')) {
            $query->where('area', '<=', $request->get('max_area'));
        }

        // Furnishing status
        if ($request->filled('furnishing_status')) {
            $query->where('furnishing_status', $request->get('furnishing_status'));
        }

        // Featured properties
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Amenities
        if ($request->filled('amenities')) {
            $amenities = is_array($request->get('amenities')) 
                ? $request->get('amenities') 
                : explode(',', $request->get('amenities'));
            
            $query->whereHas('amenities', function ($q) use ($amenities) {
                $q->whereIn('name', $amenities);
            });
        }

        // Landlord
        if ($request->filled('landlord_id')) {
            $query->where('landlord_id', $request->get('landlord_id'));
        }

        // Date filters
        if ($request->filled('created_after')) {
            $query->where('created_at', '>=', $request->get('created_after'));
        }
        if ($request->filled('created_before')) {
            $query->where('created_at', '<=', $request->get('created_before'));
        }
    }

    /**
     * Apply sorting
     */
    protected function applySorting($query, Request $request)
    {
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');

        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $sortOrder);
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'area':
                $query->orderBy('area', $sortOrder);
                break;
            case 'bedrooms':
                $query->orderBy('bedrooms', $sortOrder);
                break;
            case 'distance':
                if ($request->filled('lat') && $request->filled('lng')) {
                    $lat = $request->get('lat');
                    $lng = $request->get('lng');
                    $query->orderByRaw("
                        ST_Distance(
                            location_point,
                            ST_SetSRID(ST_MakePoint(?, ?), 4326)
                        )
                    ", [$lng, $lat]);
                }
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
    }

    /**
     * Get search suggestions
     */
    public function getSuggestions(Request $request)
    {
        $query = $request->get('q', '');
        if (strlen($query) < 2) {
            return collect();
        }

        $cacheKey = $this->cachePrefix . 'suggestions_' . md5($query);
        
        return Cache::remember($cacheKey, 300, function () use ($query) {
            $suggestions = collect();

            // Location suggestions
            $locations = Property::select('location', 'neighborhood')
                ->where(function ($q) use ($query) {
                    $q->where('location', 'ILIKE', "%{$query}%")
                      ->orWhere('neighborhood', 'ILIKE', "%{$query}%");
                })
                ->distinct()
                ->limit(5)
                ->get();

            foreach ($locations as $location) {
                if ($location->location) {
                    $suggestions->push([
                        'type' => 'location',
                        'text' => $location->location,
                        'category' => 'Location'
                    ]);
                }
                if ($location->neighborhood) {
                    $suggestions->push([
                        'type' => 'neighborhood',
                        'text' => $location->neighborhood,
                        'category' => 'Neighborhood'
                    ]);
                }
            }

            // Property type suggestions
            $types = Property::select('type')
                ->where('type', 'ILIKE', "%{$query}%")
                ->distinct()
                ->limit(3)
                ->get();

            foreach ($types as $type) {
                $suggestions->push([
                    'type' => 'property_type',
                    'text' => ucfirst($type->type),
                    'category' => 'Property Type'
                ]);
            }

            return $suggestions->unique('text')->take(10);
        });
    }

    /**
     * Get search filters
     */
    public function getSearchFilters()
    {
        $cacheKey = $this->cachePrefix . 'filters';
        
        return Cache::remember($cacheKey, 1800, function () {
            return [
                'property_types' => Property::select('type')
                    ->distinct()
                    ->orderBy('type')
                    ->pluck('type'),
                
                'furnishing_statuses' => Property::select('furnishing_status')
                    ->distinct()
                    ->orderBy('furnishing_status')
                    ->pluck('furnishing_status'),
                
                'price_ranges' => [
                    '0-50000' => 'Under 50,000 RWF',
                    '50000-100000' => '50,000 - 100,000 RWF',
                    '100000-200000' => '100,000 - 200,000 RWF',
                    '200000-500000' => '200,000 - 500,000 RWF',
                    '500000-1000000' => '500,000 - 1,000,000 RWF',
                    '1000000+' => 'Over 1,000,000 RWF'
                ],
                
                'bedroom_counts' => Property::select('bedrooms')
                    ->distinct()
                    ->orderBy('bedrooms')
                    ->pluck('bedrooms'),
                
                'bathroom_counts' => Property::select('bathrooms')
                    ->distinct()
                    ->orderBy('bathrooms')
                    ->pluck('bathrooms'),
                
                'amenities' => DB::table('amenities')
                    ->select('name')
                    ->distinct()
                    ->orderBy('name')
                    ->pluck('name')
            ];
        });
    }

    /**
     * Get search analytics
     */
    public function getSearchAnalytics($days = 30)
    {
        $cacheKey = $this->cachePrefix . 'analytics_' . $days;
        
        return Cache::remember($cacheKey, 3600, function () use ($days) {
            $startDate = now()->subDays($days);
            
            return [
                'total_searches' => SearchHistory::where('created_at', '>=', $startDate)->count(),
                'unique_searchers' => SearchHistory::where('created_at', '>=', $startDate)
                    ->distinct('user_id')
                    ->count(),
                'popular_searches' => SearchHistory::where('created_at', '>=', $startDate)
                    ->select('search_term', DB::raw('count(*) as count'))
                    ->groupBy('search_term')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
                'search_results' => SearchHistory::where('created_at', '>=', $startDate)
                    ->select(DB::raw('avg(results_count) as avg_results'))
                    ->first()
            ];
        });
    }

    /**
     * Create polygon WKT from coordinates
     */
    protected function createPolygonWKT($coordinates)
    {
        $points = array_map(function ($coord) {
            return $coord[0] . ' ' . $coord[1];
        }, $coordinates);
        
        // Close the polygon
        $points[] = $points[0];
        
        return 'POLYGON((' . implode(', ', $points) . '))';
    }

    /**
     * Generate cache key for search
     */
    protected function generateCacheKey(Request $request)
    {
        $params = $request->except(['page']);
        ksort($params);
        return $this->cachePrefix . md5(serialize($params));
    }

    /**
     * Log search for analytics
     */
    protected function logSearch(Request $request, $resultsCount)
    {
        try {
            SearchHistory::create([
                'user_id' => auth()->id(),
                'search_term' => $request->get('q', ''),
                'filters' => $request->except(['q', 'page']),
                'results_count' => $resultsCount,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log search: ' . $e->getMessage());
        }
    }

    /**
     * Clear search cache
     */
    public function clearCache()
    {
        $keys = Cache::getRedis()->keys($this->cachePrefix . '*');
        if (!empty($keys)) {
            Cache::getRedis()->del($keys);
        }
    }

    /**
     * Get search recommendations based on user history
     */
    public function getRecommendations($userId = null)
    {
        if (!$userId) {
            return collect();
        }

        $cacheKey = $this->cachePrefix . 'recommendations_' . $userId;
        
        return Cache::remember($cacheKey, 1800, function () use ($userId) {
            // Get user's search history
            $userSearches = SearchHistory::where('user_id', $userId)
                ->where('created_at', '>=', now()->subDays(30))
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            if ($userSearches->isEmpty()) {
                return collect();
            }

            // Find similar searches from other users
            $recommendations = collect();
            
            foreach ($userSearches as $search) {
                $similarSearches = SearchHistory::where('user_id', '!=', $userId)
                    ->where('search_term', 'ILIKE', "%{$search->search_term}%")
                    ->where('created_at', '>=', now()->subDays(7))
                    ->limit(3)
                    ->get();

                foreach ($similarSearches as $similar) {
                    $recommendations->push([
                        'search_term' => $similar->search_term,
                        'filters' => $similar->filters,
                        'results_count' => $similar->results_count,
                        'created_at' => $similar->created_at
                    ]);
                }
            }

            return $recommendations->unique('search_term')->take(5);
        });
    }
}

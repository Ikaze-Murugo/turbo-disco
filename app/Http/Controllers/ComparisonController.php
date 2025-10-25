<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\ComparisonAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class ComparisonController extends Controller
{
    const MAX_COMPARISON_ITEMS = 4;
    const CACHE_DURATION = 3600; // 1 hour

    /**
     * Display the comparison page with optimized property loading and caching
     */
    public function index(Request $request)
    {
        $propertyIds = $this->getComparisonIds($request);
        
        if (empty($propertyIds)) {
            return view('comparison.index', [
                'properties' => collect(),
                'comparisonData' => [],
                'isEmpty' => true
            ]);
        }

        // Create cache key for comparison data
        $cacheKey = 'comparison_data_' . md5(implode(',', $propertyIds));
        
        // Try to get from cache first
        $cachedData = Cache::get($cacheKey);
        if ($cachedData && !$request->has('refresh')) {
            return view('comparison.index', [
                'properties' => $cachedData['properties'],
                'comparisonData' => $cachedData['comparisonData'],
                'isEmpty' => false
            ]);
        }

        // Optimized query with eager loading and specific fields
        $properties = Property::with([
            'images' => function($query) {
                $query->where('is_primary', true)->select('id', 'property_id', 'path', 'is_primary');
            },
            'landlord:id,name,email',
            'nearbyAmenities' => function($query) {
                $query->select('amenities.id', 'amenities.name', 'amenities.type')
                      ->where('amenities.is_active', true);
            }
        ])
        ->select([
            'id', 'title', 'price', 'type', 'bedrooms', 'bathrooms', 'area',
            'furnishing_status', 'address', 'neighborhood', 'description',
            'has_balcony', 'has_garden', 'has_pool', 'has_gym', 'has_security',
            'has_elevator', 'has_air_conditioning', 'has_heating', 'has_internet',
            'has_cable_tv', 'pets_allowed', 'smoking_allowed', 'parking_spaces',
            'created_at', 'updated_at', 'landlord_id'
        ])
        ->whereIn('id', $propertyIds)
        ->where('status', 'active')
        ->get()
        ->keyBy('id');

        // Reorder properties according to comparison list order
        $orderedProperties = collect();
        foreach ($propertyIds as $id) {
            if ($properties->has($id)) {
                $orderedProperties->push($properties->get($id));
            }
        }

        // Generate comparison data for table
        $comparisonData = $this->generateComparisonData($orderedProperties);

        // Track comparison analytics
        $this->trackComparisonAnalytics($propertyIds);

        // Cache the results for 30 minutes
        $cacheData = [
            'properties' => $orderedProperties,
            'comparisonData' => $comparisonData
        ];
        
        Cache::put($cacheKey, $cacheData, 1800); // 30 minutes

        return view('comparison.index', [
            'properties' => $orderedProperties,
            'comparisonData' => $comparisonData,
            'isEmpty' => false
        ]);
    }

    /**
     * Add property to comparison via AJAX
     */
    public function add(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id'
        ]);

        $propertyId = $request->property_id;
        $comparisonIds = $this->getComparisonIds($request);

        // Check if already in comparison
        if (in_array($propertyId, $comparisonIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Property already in comparison list',
                'count' => count($comparisonIds)
            ]);
        }

        // Check maximum limit
        if (count($comparisonIds) >= self::MAX_COMPARISON_ITEMS) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum ' . self::MAX_COMPARISON_ITEMS . ' properties can be compared',
                'count' => count($comparisonIds)
            ]);
        }

        // Add to comparison
        $comparisonIds[] = $propertyId;
        $this->saveComparisonIds($comparisonIds, $request);

        // Clear related caches
        $this->clearComparisonCaches($comparisonIds);

        // Log comparison activity
        Log::info('Property added to comparison', [
            'property_id' => $propertyId,
            'user_id' => Auth::id(),
            'comparison_count' => count($comparisonIds)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Property added to comparison',
            'count' => count($comparisonIds),
            'property_id' => $propertyId
        ]);
    }

    /**
     * Remove property from comparison via AJAX
     */
    public function remove(Request $request, $propertyId)
    {
        $comparisonIds = $this->getComparisonIds($request);
        $comparisonIds = array_values(array_filter($comparisonIds, fn($id) => $id != $propertyId));
        
        $this->saveComparisonIds($comparisonIds, $request);

        // Clear related caches
        $this->clearComparisonCaches($comparisonIds);

        Log::info('Property removed from comparison', [
            'property_id' => $propertyId,
            'user_id' => Auth::id(),
            'comparison_count' => count($comparisonIds)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Property removed from comparison',
            'count' => count($comparisonIds),
            'property_id' => $propertyId
        ]);
    }

    /**
     * Clear all properties from comparison
     */
    public function clear(Request $request)
    {
        $this->saveComparisonIds([], $request);

        // Clear all comparison caches
        $this->clearAllComparisonCaches();

        Log::info('Comparison list cleared', [
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comparison list cleared',
            'count' => 0
        ]);
    }

    /**
     * Get comparison count for navigation
     */
    public function count(Request $request)
    {
        $comparisonIds = $this->getComparisonIds($request);
        
        return response()->json([
            'count' => count($comparisonIds)
        ]);
    }

    /**
     * Get comparison IDs from session or localStorage
     */
    private function getComparisonIds(Request $request): array
    {
        // Try to get from request (AJAX)
        if ($request->has('comparison_ids')) {
            return array_filter($request->comparison_ids, 'is_numeric');
        }

        // Try to get from session (works for both authenticated and guest users)
        return session('comparison_properties', []);
    }

    /**
     * Save comparison IDs to session or localStorage
     */
    private function saveComparisonIds(array $ids, Request $request): void
    {
        // Save to session for both authenticated and guest users
        session(['comparison_properties' => $ids]);
    }

    /**
     * Generate structured comparison data for the table
     */
    private function generateComparisonData($properties): array
    {
        $data = [];

        // Basic Information
        $data['basic'] = [
            'title' => 'Basic Information',
            'fields' => [
                'price' => ['label' => 'Price', 'type' => 'currency'],
                'type' => ['label' => 'Property Type', 'type' => 'text'],
                'bedrooms' => ['label' => 'Bedrooms', 'type' => 'number'],
                'bathrooms' => ['label' => 'Bathrooms', 'type' => 'number'],
                'area' => ['label' => 'Area (m²)', 'type' => 'number'],
                'furnishing_status' => ['label' => 'Furnishing', 'type' => 'text'],
                'neighborhood' => ['label' => 'Neighborhood', 'type' => 'text'],
                'address' => ['label' => 'Address', 'type' => 'text']
            ]
        ];

        // Amenities & Features
        $data['amenities'] = [
            'title' => 'Amenities & Features',
            'fields' => [
                'has_balcony' => ['label' => 'Balcony', 'type' => 'boolean'],
                'has_garden' => ['label' => 'Garden', 'type' => 'boolean'],
                'has_pool' => ['label' => 'Pool', 'type' => 'boolean'],
                'has_gym' => ['label' => 'Gym', 'type' => 'boolean'],
                'has_security' => ['label' => 'Security', 'type' => 'boolean'],
                'has_elevator' => ['label' => 'Elevator', 'type' => 'boolean'],
                'has_air_conditioning' => ['label' => 'Air Conditioning', 'type' => 'boolean'],
                'has_heating' => ['label' => 'Heating', 'type' => 'boolean'],
                'has_internet' => ['label' => 'Internet', 'type' => 'boolean'],
                'has_cable_tv' => ['label' => 'Cable TV', 'type' => 'boolean'],
                'parking_spaces' => ['label' => 'Parking', 'type' => 'boolean']
            ]
        ];

        // Policies
        $data['policies'] = [
            'title' => 'Policies',
            'fields' => [
                'pets_allowed' => ['label' => 'Pets Allowed', 'type' => 'boolean'],
                'smoking_allowed' => ['label' => 'Smoking Allowed', 'type' => 'boolean']
            ]
        ];

        // Populate values for each property
        foreach ($data as $sectionKey => $section) {
            foreach ($section['fields'] as $fieldKey => $field) {
                foreach ($properties as $property) {
                    $value = $property->{$fieldKey};
                    
                    // Format value based on type
                    switch ($field['type']) {
                        case 'currency':
                            $data[$sectionKey]['values'][$property->id][$fieldKey] = 'RWF ' . number_format($value);
                            break;
                        case 'boolean':
                            $data[$sectionKey]['values'][$property->id][$fieldKey] = $value ? 'Yes' : 'No';
                            break;
                        case 'text':
                            $data[$sectionKey]['values'][$property->id][$fieldKey] = ucfirst(str_replace('_', ' ', $value ?? 'N/A'));
                            break;
                        default:
                            $data[$sectionKey]['values'][$property->id][$fieldKey] = $value ?? 'N/A';
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Clear comparison-related caches
     */
    private function clearComparisonCaches(array $propertyIds): void
    {
        $cacheKeys = [
            'comparison_data_' . md5(implode(',', $propertyIds)),
            'property_filter_options',
            'featured_properties'
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear Redis cache if available
        try {
            if (config('cache.default') === 'redis') {
                Redis::del($cacheKeys);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to clear Redis cache', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Clear all comparison-related caches
     */
    private function clearAllComparisonCaches(): void
    {
        // Clear all comparison data caches
        $pattern = 'comparison_data_*';
        
        try {
            if (config('cache.default') === 'redis') {
                $keys = Redis::keys($pattern);
                if (!empty($keys)) {
                    Redis::del($keys);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to clear Redis comparison caches', ['error' => $e->getMessage()]);
        }

        // Clear other related caches
        Cache::forget('property_filter_options');
        Cache::forget('featured_properties');
    }

    /**
     * Get comparison count with caching
     */
    public function getComparisonCount(Request $request): int
    {
        $cacheKey = 'comparison_count_' . (Auth::id() ?? 'guest');
        
        return Cache::remember($cacheKey, 300, function () use ($request) {
            $comparisonIds = $this->getComparisonIds($request);
            return count($comparisonIds);
        });
    }

    /**
     * Track comparison analytics
     */
    private function trackComparisonAnalytics(array $propertyIds): void
    {
        try {
            // Check if we already have an active comparison session
            $existingAnalytics = ComparisonAnalytics::where('session_id', session()->getId())
                ->where('comparison_completed', false)
                ->where('created_at', '>', now()->subHours(1))
                ->first();

            if (!$existingAnalytics) {
                // Create new analytics entry
                ComparisonAnalytics::trackComparisonStart(
                    $propertyIds,
                    Auth::id(),
                    session()->getId()
                );
            } else {
                // Update existing entry
                $existingAnalytics->update([
                    'property_ids' => $propertyIds,
                    'properties_viewed' => count($propertyIds)
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to track comparison analytics', [
                'error' => $e->getMessage(),
                'property_ids' => $propertyIds
            ]);
        }
    }

    /**
     * Track comparison completion
     */
    public function trackCompletion(Request $request)
    {
        $request->validate([
            'conversion_type' => 'nullable|string|in:inquiry,favorite,share,contact,view_details'
        ]);

        try {
            $analytics = ComparisonAnalytics::where('session_id', session()->getId())
                ->where('comparison_completed', false)
                ->where('created_at', '>', now()->subHours(1))
                ->first();

            if ($analytics) {
                $analytics->trackCompletion($request->conversion_type);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::warning('Failed to track comparison completion', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Get comparison analytics for admin
     */
    public function analytics(Request $request)
    {
        $days = $request->get('days', 30);
        $analytics = ComparisonAnalytics::getAnalytics($days);
        
        return response()->json($analytics);
    }
}

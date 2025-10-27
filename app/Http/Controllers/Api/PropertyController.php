<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    /**
     * Get properties as GeoJSON for map display
     */
    public function geojson(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'bounds' => 'sometimes|string',
            'lat' => 'sometimes|numeric|between:-2.9,0.0',
            'lng' => 'sometimes|numeric|between:28.8,30.9',
            'radius' => 'sometimes|numeric|min:0.1|max:100',
            'polygon' => 'sometimes|array',
            'polygon.*' => 'array|min:2|max:2',
            'polygon.*.*' => 'numeric',
            'type' => 'sometimes|string|in:apartment,house,villa,commercial',
            'min_price' => 'sometimes|numeric|min:0',
            'max_price' => 'sometimes|numeric|min:0',
            'bedrooms' => 'sometimes|integer|min:0',
            'bathrooms' => 'sometimes|integer|min:0',
            'limit' => 'sometimes|integer|min:1|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $query = Property::where('status', 'active');

        // Viewport-based loading (bounds)
        if ($request->filled('bounds')) {
            $bounds = explode(',', $request->bounds);
            if (count($bounds) === 4) {
                $query->withinBounds($bounds[0], $bounds[1], $bounds[2], $bounds[3]);
            }
        }

        // Radius search
        if ($request->filled(['lat', 'lng', 'radius'])) {
            $query->withinRadius($request->lat, $request->lng, $request->radius);
        }

        // Polygon search
        if ($request->filled('polygon')) {
            $query->withinPolygon($request->polygon);
        }

        // Distance-based sorting
        if ($request->filled(['lat', 'lng'])) {
            $query->orderByDistance($request->lat, $request->lng);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', '>=', $request->bathrooms);
        }

        // Limit results
        $limit = $request->get('limit', 500);
        $properties = $query->limit($limit)->get();

        // Filter out properties without valid coordinates
        $properties = $properties->filter(function ($property) {
            return $property->hasValidCoordinates();
        });

        return response()->json(Property::getAsGeoJSON($properties));
    }

    /**
     * Search properties by radius
     */
    public function searchByRadius(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required|numeric|between:-2.9,0.0',
            'lng' => 'required|numeric|between:28.8,30.9',
            'radius' => 'required|numeric|min:0.1|max:100',
            'type' => 'sometimes|string|in:apartment,house,villa,commercial',
            'min_price' => 'sometimes|numeric|min:0',
            'max_price' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $query = Property::where('status', 'active')
            ->withinRadius($request->lat, $request->lng, $request->radius)
            ->orderByDistance($request->lat, $request->lng);

        // Apply additional filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $properties = $query->get();

        return response()->json([
            'properties' => Property::getAsGeoJSON($properties),
            'search_center' => [
                'lat' => $request->lat,
                'lng' => $request->lng,
                'radius' => $request->radius
            ],
            'count' => $properties->count()
        ]);
    }

    /**
     * Search properties by area (polygon)
     */
    public function searchByArea(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'polygon' => 'required|array|min:3',
            'polygon.*' => 'array|min:2|max:2',
            'polygon.*.*' => 'numeric',
            'type' => 'sometimes|string|in:apartment,house,villa,commercial',
            'min_price' => 'sometimes|numeric|min:0',
            'max_price' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $query = Property::where('status', 'active')
            ->withinPolygon($request->polygon);

        // Apply additional filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $properties = $query->get();

        return response()->json([
            'properties' => Property::getAsGeoJSON($properties),
            'search_area' => $request->polygon,
            'count' => $properties->count()
        ]);
    }

    /**
     * Get nearby properties for a specific property
     */
    public function nearby(Request $request, Property $property): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'radius' => 'sometimes|numeric|min:0.1|max:50',
            'limit' => 'sometimes|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        if (!$property->hasValidCoordinates()) {
            return response()->json(['error' => 'Property does not have valid coordinates'], 400);
        }

        $radius = $request->get('radius', 5);
        $limit = $request->get('limit', 10);

        $nearbyProperties = $property->getNearbyProperties($radius, $limit);

        return response()->json([
            'property' => $property,
            'nearby' => Property::getAsGeoJSON($nearbyProperties),
            'radius' => $radius,
            'count' => $nearbyProperties->count()
        ]);
    }

    /**
     * Geocode an address to get coordinates
     */
    public function geocode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $result = Property::geocodeAddress($request->address);

        if (!$result) {
            return response()->json(['error' => 'Address not found'], 404);
        }

        return response()->json($result);
    }

    /**
     * Reverse geocode coordinates to get address
     */
    public function reverseGeocode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required|numeric|between:-2.9,0.0',
            'lng' => 'required|numeric|between:28.8,30.9',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $result = Property::reverseGeocode($request->lat, $request->lng);

        if (!$result) {
            return response()->json(['error' => 'Address not found for these coordinates'], 404);
        }

        return response()->json($result);
    }

    /**
     * Get property statistics for map
     */
    public function statistics(Request $request): JsonResponse
    {
        $cacheKey = 'property_map_statistics';
        
        $stats = Cache::remember($cacheKey, 3600, function () { // Cache for 1 hour
            return [
                'total_properties' => Property::where('status', 'active')->count(),
                'featured_properties' => Property::where('status', 'active')->where('is_featured', true)->count(),
                'properties_with_coordinates' => Property::where('status', 'active')
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->count(),
                'average_price' => Property::where('status', 'active')->avg('price'),
                'price_range' => [
                    'min' => Property::where('status', 'active')->min('price'),
                    'max' => Property::where('status', 'active')->max('price'),
                ],
                'property_types' => Property::where('status', 'active')
                    ->selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->pluck('count', 'type'),
                'locations' => Property::where('status', 'active')
                    ->selectRaw('location, COUNT(*) as count')
                    ->groupBy('location')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->pluck('count', 'location'),
            ];
        });

        return response()->json($stats);
    }

    /**
     * Get map clusters for performance
     */
    public function clusters(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'bounds' => 'required|string',
            'zoom' => 'required|integer|min:1|max:18',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $bounds = explode(',', $request->bounds);
        $zoom = $request->zoom;

        // Determine cluster size based on zoom level
        $clusterSize = $zoom < 10 ? 50 : ($zoom < 14 ? 20 : 5);

        $query = Property::where('status', 'active')
            ->withinBounds($bounds[0], $bounds[1], $bounds[2], $bounds[3]);

        $properties = $query->get();

        // Simple clustering algorithm
        $clusters = $this->createClusters($properties, $clusterSize);

        return response()->json([
            'clusters' => $clusters,
            'zoom' => $zoom,
            'cluster_size' => $clusterSize
        ]);
    }

    /**
     * Simple clustering algorithm
     */
    private function createClusters($properties, $clusterSize)
    {
        $clusters = [];
        $used = [];

        foreach ($properties as $index => $property) {
            if (in_array($index, $used)) {
                continue;
            }

            $cluster = [
                'lat' => $property->latitude,
                'lng' => $property->longitude,
                'count' => 1,
                'properties' => [$property]
            ];

            // Find nearby properties to cluster
            foreach ($properties as $otherIndex => $otherProperty) {
                if ($otherIndex === $index || in_array($otherIndex, $used)) {
                    continue;
                }

                $distance = $this->calculateDistance(
                    $property->latitude, $property->longitude,
                    $otherProperty->latitude, $otherProperty->longitude
                );

                if ($distance < $clusterSize) {
                    $cluster['count']++;
                    $cluster['properties'][] = $otherProperty;
                    $used[] = $otherIndex;
                }
            }

            $used[] = $index;
            $clusters[] = $cluster;
        }

        return $clusters;
    }

    /**
     * Calculate distance between two points in kilometers
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicPropertiesController extends Controller
{
    /**
     * Display the main properties listing page
     */
    public function index(Request $request)
    {
        $query = Property::with(['images', 'nearbyAmenities', 'landlord'])
            ->where('status', 'active')
            ->where('is_available', true);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        // Apply sorting
        $query = $this->applySorting($query, $request);

        // Get paginated results
        $properties = $query->paginate(12);

        // Get filter options
        $filterOptions = $this->getFilterOptions();

        // Get search suggestions
        $searchSuggestions = $this->getSearchSuggestions($request->get('search'));

        return view('properties-public.index', compact(
            'properties',
            'filterOptions',
            'searchSuggestions'
        ));
    }

    /**
     * Display search results with advanced filtering
     */
    public function search(Request $request)
    {
        $query = Property::with(['images', 'nearbyAmenities', 'landlord'])
            ->where('status', 'active')
            ->where('is_available', true);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        // Apply sorting
        $query = $this->applySorting($query, $request);

        // Get paginated results
        $properties = $query->paginate(12);

        // Get filter options
        $filterOptions = $this->getFilterOptions();

        // Get search suggestions
        $searchSuggestions = $this->getSearchSuggestions($request->get('search'));

        return view('properties-public.search', compact(
            'properties',
            'filterOptions',
            'searchSuggestions'
        ));
    }

    /**
     * Display individual property details
     */
    public function show($id)
    {
        $property = Property::with(['images', 'nearbyAmenities', 'landlord', 'reviews'])
            ->where('status', 'active')
            ->findOrFail($id);

        // Increment view count
        $property->increment('views_count');

        // Get related properties
        $relatedProperties = Property::with(['images'])
            ->where('status', 'active')
            ->where('is_available', true)
            ->where('id', '!=', $property->id)
            ->where(function($q) use ($property) {
                $q->where('type', $property->type)
                  ->orWhere('neighborhood', $property->neighborhood)
                  ->orWhere('bedrooms', $property->bedrooms);
            })
            ->limit(4)
            ->get();

        return view('properties-public.show', compact('property', 'relatedProperties'));
    }

    /**
     * Display map view of properties
     */
    public function map(Request $request)
    {
        try {
            $query = Property::where('status', 'active')
                ->where('is_available', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude');

            $properties = $query->get();

            return view('properties-public.map', compact('properties'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get search suggestions
     */
    public function getSuggestions(Request $request)
    {
        $search = $request->get('q', '');
        
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $suggestions = [];

        // Location suggestions
        $locations = Property::where('status', 'active')
            ->where(function($q) use ($search) {
                $q->where('address', 'like', "%{$search}%")
                  ->orWhere('neighborhood', 'like', "%{$search}%");
            })
            ->select('address', 'neighborhood')
            ->distinct()
            ->limit(5)
            ->get();

        foreach ($locations as $location) {
            if ($location->neighborhood) {
                $suggestions[] = [
                    'type' => 'location',
                    'text' => $location->neighborhood,
                    'value' => $location->neighborhood
                ];
            }
        }

        // Property type suggestions
        $types = Property::where('status', 'active')
            ->where('type', 'like', "%{$search}%")
            ->select('type')
            ->distinct()
            ->limit(3)
            ->get();

        foreach ($types as $type) {
            $suggestions[] = [
                'type' => 'type',
                'text' => ucfirst($type->type),
                'value' => $type->type
            ];
        }

        return response()->json($suggestions);
    }

    /**
     * Apply filters to the query
     */
    private function applyFilters($query, Request $request)
    {
        // Search query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('neighborhood', 'like', "%{$search}%");
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

        // Furnishing status
        if ($request->filled('furnishing_status')) {
            $query->where('furnishing_status', $request->furnishing_status);
        }

        // Amenities
        if ($request->filled('amenities')) {
            $amenities = is_array($request->amenities) ? $request->amenities : [$request->amenities];
            $query->whereHas('nearbyAmenities', function($q) use ($amenities) {
                $q->whereIn('amenities.id', $amenities);
            });
        }

        // Pet policy
        if ($request->filled('pets_allowed')) {
            $query->where('pets_allowed', $request->pets_allowed);
        }

        return $query;
    }

    /**
     * Apply sorting to the query
     */
    private function applySorting($query, Request $request)
    {
        $sort = $request->get('sort', 'newest');

        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'size_large':
                $query->orderBy('area', 'desc');
                break;
            case 'size_small':
                $query->orderBy('area', 'asc');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query;
    }

    /**
     * Get filter options for the UI
     */
    private function getFilterOptions()
    {
        return [
            'types' => Property::where('status', 'active')
                ->select('type')
                ->distinct()
                ->orderBy('type')
                ->pluck('type'),
            
            'locations' => Property::where('status', 'active')
                ->select('neighborhood')
                ->distinct()
                ->whereNotNull('neighborhood')
                ->orderBy('neighborhood')
                ->pluck('neighborhood'),
            
            'amenities' => collect([]), // Temporarily disabled
            
            'furnishing_statuses' => ['furnished', 'semi-furnished', 'unfurnished'],
            
            'price_ranges' => [
                '0-50000' => 'Under RWF 50,000',
                '50000-100000' => 'RWF 50,000 - 100,000',
                '100000-200000' => 'RWF 100,000 - 200,000',
                '200000-500000' => 'RWF 200,000 - 500,000',
                '500000+' => 'RWF 500,000+'
            ]
        ];
    }

    /**
     * Get search suggestions
     */
    private function getSearchSuggestions($search = null)
    {
        if (!$search || strlen($search) < 2) {
            return [];
        }

        $suggestions = [];

        // Location suggestions
        $locations = Property::where('status', 'active')
            ->where(function($q) use ($search) {
                $q->where('address', 'like', "%{$search}%")
                  ->orWhere('neighborhood', 'like', "%{$search}%");
            })
            ->select('address', 'neighborhood')
            ->distinct()
            ->limit(5)
            ->get();

        foreach ($locations as $location) {
            if ($location->neighborhood) {
                $suggestions[] = [
                    'type' => 'location',
                    'text' => $location->neighborhood,
                    'value' => $location->neighborhood
                ];
            }
        }

        return $suggestions;
    }
}

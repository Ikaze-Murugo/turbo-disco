<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\SearchHistory;
use App\Models\SavedSearch;
use App\Models\PropertyComparison;
use App\Services\PropertySearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(PropertySearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Display map-based property search.
     */
    public function searchMap(Request $request)
    {
        // Use the optimized search service for map view
        $properties = $this->searchService->search($request, 50); // More properties for map view

        return view('properties.search-map', compact('properties'));
    }

    /**
     * Display search form and results for both public and authenticated users.
     */
    public function index(Request $request)
    {
        // Use the optimized search service
        $properties = $this->searchService->search($request, 12);

        // Get filter options
        $filterOptions = $this->searchService->getFilterOptions();

        // Save search history
        $this->saveSearchHistory($request, $properties->total());

        // Get comparison data
        $comparison = $this->getComparisonData();

        // Get saved searches for authenticated users
        $savedSearches = Auth::check() ? Auth::user()->savedSearches()->where('is_active', true)->get() : collect();

        // Determine which view to return based on authentication
        if (Auth::check()) {
            return view('properties.search', compact('properties', 'filterOptions', 'savedSearches', 'comparison'));
        } else {
            return view('public.search', compact('properties', 'filterOptions', 'comparison'));
        }
    }

    /**
     * Apply advanced filters to the query
     */
    private function applyAdvancedFilters($query, Request $request)
    {
        // Search query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Property type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Purpose filter (rent/sale)
        if ($request->filled('purpose')) {
            if ($request->purpose === 'rent') {
                $query->where('price', '<=', 1000000);
            } elseif ($request->purpose === 'sale') {
                $query->where('price', '>', 1000000);
            }
        }

        // Price range filter
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Location filter
        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        // Bedrooms filter
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        // Bathrooms filter
        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', '>=', $request->bathrooms);
        }

        // Area filter
        if ($request->filled('area_min')) {
            $query->where('area', '>=', $request->area_min);
        }
        if ($request->filled('area_max')) {
            $query->where('area', '<=', $request->area_max);
        }

        // Furnishing status filter
        if ($request->filled('furnishing_status')) {
            $query->where('furnishing_status', $request->furnishing_status);
        }

        // Parking spaces filter
        if ($request->filled('parking_spaces')) {
            $query->where('parking_spaces', '>=', $request->parking_spaces);
        }

        // Amenities filters
        $amenities = [
            'has_balcony', 'has_garden', 'has_pool', 'has_gym', 'has_security',
            'has_elevator', 'has_air_conditioning', 'has_heating', 'has_internet',
            'has_cable_tv', 'pets_allowed', 'smoking_allowed'
        ];

        foreach ($amenities as $amenity) {
            if ($request->filled($amenity) && $request->$amenity) {
                $query->where($amenity, true);
            }
        }

        // Multiple amenities filter
        if ($request->filled('amenities') && is_array($request->amenities)) {
            foreach ($request->amenities as $amenity) {
                if (in_array($amenity, $amenities)) {
                    $query->where($amenity, true);
                }
            }
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
        
        if ($sortBy === 'priority') {
            $query->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 END")
                  ->orderBy('created_at', 'desc');
        } elseif (in_array($sortBy, ['price', 'created_at', 'bedrooms', 'bathrooms', 'area', 'view_count'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    /**
     * Get filter options for the sidebar
     */
    private function getFilterOptions()
    {
        $baseQuery = Property::where('status', 'active');

        return [
            'types' => $baseQuery->distinct()->pluck('type')->filter()->sort(),
            'locations' => $baseQuery->distinct()->pluck('location')->filter()->sort(),
            'bedrooms' => $baseQuery->distinct()->pluck('bedrooms')->filter()->sort(),
            'bathrooms' => $baseQuery->distinct()->pluck('bathrooms')->filter()->sort(),
            'furnishing_statuses' => $baseQuery->distinct()->pluck('furnishing_status')->filter()->sort(),
            'price_range' => [
                'min' => $baseQuery->min('price') ?? 0,
                'max' => $baseQuery->max('price') ?? 10000000,
            ],
            'area_range' => [
                'min' => $baseQuery->min('area') ?? 0,
                'max' => $baseQuery->max('area') ?? 1000,
            ],
        ];
    }

    /**
     * Save search history
     */
    private function saveSearchHistory(Request $request, $resultsCount)
    {
        $filters = $request->except(['_token', 'page', 'sort', 'order']);
        
        // Remove empty values to keep the filters clean
        $filters = array_filter($filters, function($value) {
            return !empty($value) && $value !== '';
        });
        
        // Convert filters array to JSON string for SQLite compatibility
        $filtersJson = json_encode($filters);
        
        SearchHistory::create([
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'search_query' => $request->search,
            'filters' => $filtersJson,
            'results_count' => $resultsCount,
            'session_id' => Session::getId(),
        ]);
    }

    /**
     * Get comparison data
     */
    private function getComparisonData()
    {
        if (Auth::check()) {
            return PropertyComparison::getOrCreate(Auth::id());
        } else {
            return PropertyComparison::getOrCreate(null, Session::getId());
        }
    }

    /**
     * Save a search for authenticated users
     */
    public function saveSearch(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'search_query' => 'nullable|string|max:255',
            'filters' => 'required|array',
        ]);

        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $savedSearch = Auth::user()->savedSearches()->create([
            'name' => $request->name,
            'search_query' => $request->search_query,
            'filters' => $request->filters,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Search saved successfully',
            'saved_search' => $savedSearch
        ]);
    }

    /**
     * Load a saved search
     */
    public function loadSavedSearch(SavedSearch $savedSearch)
    {
        if (!Auth::check() || $savedSearch->user_id !== Auth::id()) {
            abort(403);
        }

        $savedSearch->incrementSearchCount();

        $query = Property::where('status', 'active');
        
        if (Auth::user()->isRenter()) {
            $query->where('is_available', true);
        }

        $query = $this->applyAdvancedFilters($query, new Request($savedSearch->filters));
        $query = $this->applySorting($query, new Request(['sort' => 'priority', 'order' => 'desc']));

        $properties = $query->with(['landlord', 'images'])->paginate(12);
        $filterOptions = $this->getFilterOptions();
        $comparison = $this->getComparisonData();
        $savedSearches = Auth::user()->savedSearches()->where('is_active', true)->get();

        return view('properties.search', compact('properties', 'filterOptions', 'comparison', 'savedSearches'))
            ->with('savedSearch', $savedSearch);
    }

    /**
     * Delete a saved search
     */
    public function deleteSavedSearch(SavedSearch $savedSearch)
    {
        if (!Auth::check() || $savedSearch->user_id !== Auth::id()) {
            abort(403);
        }

        $savedSearch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Saved search deleted successfully'
        ]);
    }

    /**
     * Add property to comparison
     */
    public function addToComparison(Request $request, Property $property)
    {
        $comparison = $this->getComparisonData();

        if ($comparison->isFull()) {
            return response()->json([
                'success' => false,
                'message' => 'Comparison list is full (maximum 4 properties)'
            ]);
        }

        $comparison->addProperty($property->id);

        return response()->json([
            'success' => true,
            'message' => 'Property added to comparison',
            'comparison_count' => $comparison->getCount()
        ]);
    }

    /**
     * Remove property from comparison
     */
    public function removeFromComparison(Request $request, Property $property)
    {
        $comparison = $this->getComparisonData();
        $comparison->removeProperty($property->id);

        return response()->json([
            'success' => true,
            'message' => 'Property removed from comparison',
            'comparison_count' => $comparison->getCount()
        ]);
    }

    /**
     * Show comparison page
     */
    public function showComparison()
    {
        $comparison = $this->getComparisonData();
        $properties = $comparison->properties();

        return view('properties.comparison', compact('properties', 'comparison'));
    }

    /**
     * Get search suggestions
     */
    public function getSuggestions(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Get popular search terms
        $popularSearches = SearchHistory::getPopularSearches(5);
        
        // Get location suggestions
        $locations = Property::where('status', 'active')
            ->where('location', 'like', "%{$query}%")
            ->distinct()
            ->pluck('location')
            ->take(5);

        // Get property type suggestions
        $types = Property::where('status', 'active')
            ->where('type', 'like', "%{$query}%")
            ->distinct()
            ->pluck('type')
            ->take(3);

        return response()->json([
            'popular' => $popularSearches->pluck('search_query'),
            'locations' => $locations,
            'types' => $types,
        ]);
    }
}
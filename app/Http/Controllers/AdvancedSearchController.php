<?php

namespace App\Http\Controllers;

use App\Services\AdvancedSearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdvancedSearchController extends Controller
{
    protected $searchService;

    public function __construct(AdvancedSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Perform advanced property search
     */
    public function search(Request $request)
    {
        try {
            $properties = $this->searchService->search($request, 20);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'properties' => $properties->items(),
                    'pagination' => [
                        'current_page' => $properties->currentPage(),
                        'last_page' => $properties->lastPage(),
                        'per_page' => $properties->perPage(),
                        'total' => $properties->total(),
                        'has_more' => $properties->hasMorePages()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get search suggestions
     */
    public function suggestions(Request $request): JsonResponse
    {
        try {
            $suggestions = $this->searchService->getSuggestions($request);
            
            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get suggestions',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get search filters
     */
    public function filters(): JsonResponse
    {
        try {
            $filters = $this->searchService->getSearchFilters();
            
            return response()->json([
                'success' => true,
                'data' => $filters
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get filters',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get search analytics
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $days = $request->get('days', 30);
            $analytics = $this->searchService->getSearchAnalytics($days);
            
            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get analytics',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get search recommendations
     */
    public function recommendations(): JsonResponse
    {
        try {
            $userId = auth()->id();
            $recommendations = $this->searchService->getRecommendations($userId);
            
            return response()->json([
                'success' => true,
                'data' => $recommendations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recommendations',
                'data' => []
            ], 500);
        }
    }

    /**
     * Save search as favorite
     */
    public function saveSearch(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'search_term' => 'nullable|string|max:255',
            'filters' => 'nullable|array'
        ]);

        try {
            $savedSearch = auth()->user()->savedSearches()->create([
                'name' => $request->name,
                'search_term' => $request->search_term,
                'filters' => $request->filters ?? [],
                'is_active' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Search saved successfully',
                'data' => $savedSearch
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save search'
            ], 500);
        }
    }

    /**
     * Get saved searches
     */
    public function savedSearches(): JsonResponse
    {
        try {
            $savedSearches = auth()->user()->savedSearches()
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $savedSearches
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get saved searches',
                'data' => []
            ], 500);
        }
    }

    /**
     * Delete saved search
     */
    public function deleteSavedSearch($id): JsonResponse
    {
        try {
            $savedSearch = auth()->user()->savedSearches()->findOrFail($id);
            $savedSearch->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Search deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete search'
            ], 500);
        }
    }

    /**
     * Clear search cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            $this->searchService->clearCache();
            
            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache'
            ], 500);
        }
    }

    /**
     * Get search trends
     */
    public function trends(Request $request): JsonResponse
    {
        try {
            $days = $request->get('days', 7);
            $trends = $this->getSearchTrends($days);
            
            return response()->json([
                'success' => true,
                'data' => $trends
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get search trends',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get search trends data
     */
    protected function getSearchTrends($days)
    {
        $startDate = now()->subDays($days);
        
        $trends = \App\Models\SearchHistory::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as searches')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $propertyTypes = \App\Models\SearchHistory::where('created_at', '>=', $startDate)
            ->whereNotNull('filters->type')
            ->selectRaw("filters->>'type' as type, COUNT(*) as count")
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        $locations = \App\Models\SearchHistory::where('created_at', '>=', $startDate)
            ->whereNotNull('search_term')
            ->where('search_term', '!=', '')
            ->selectRaw('search_term, COUNT(*) as count')
            ->groupBy('search_term')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return [
            'daily_searches' => $trends,
            'popular_types' => $propertyTypes,
            'popular_locations' => $locations,
            'total_searches' => $trends->sum('searches'),
            'average_daily' => $trends->avg('searches')
        ];
    }
}

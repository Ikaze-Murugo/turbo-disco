<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Services\PropertySearchService;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    protected $searchService;

    public function __construct(PropertySearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Display the public homepage with featured properties.
     */
    public function index()
    {
        // Get featured properties
        $featuredProperties = Property::where('status', 'active')
            ->featured()
            ->with(['images', 'landlord'])
            ->byPriority()
            ->limit(6)
            ->get();

        // Get recent properties (excluding featured ones)
        $recentProperties = Property::where('status', 'active')
            ->where('is_featured', false)
            ->with(['images', 'landlord'])
            ->byPriority()
            ->limit(8)
            ->get();

        // Get property statistics
        $stats = [
            'total_properties' => Property::where('status', 'active')->count(),
            'total_landlords' => Property::where('status', 'active')->distinct('landlord_id')->count(),
        ];

        return view('public.index', compact('featuredProperties', 'recentProperties', 'stats'));
    }

    /**
     * Display all active properties for public viewing.
     */
    public function properties(Request $request)
    {
        // Use the optimized search service
        $properties = $this->searchService->search($request, 12);

        // Get filter options
        $filterOptions = $this->searchService->getFilterOptions();

        return view('public.properties', compact('properties', 'filterOptions'));
    }

    /**
     * Display a single property for public viewing.
     */
    public function show(Property $property)
    {
        // Only show active properties to public
        if ($property->status !== 'active') {
            abort(404);
        }

        // Increment view count
        $property->incrementViewCount();

        $property->load(['images', 'landlord', 'reviews' => function($query) {
            $query->where('is_approved', true);
        }]);

        // Get related properties
        $relatedProperties = Property::where('status', 'active')
            ->where('id', '!=', $property->id)
            ->where(function($q) use ($property) {
                $q->where('type', $property->type)
                  ->orWhere('location', 'like', '%' . explode(',', $property->location)[0] . '%');
            })
            ->with(['images', 'landlord'])
            ->limit(4)
            ->get();

        return view('public.property', compact('property', 'relatedProperties'));
    }
}

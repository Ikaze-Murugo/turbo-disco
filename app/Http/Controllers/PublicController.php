<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PublicController extends Controller
{
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
        $query = Property::where('status', 'active')
            ->with(['images', 'landlord']);

        // Apply filters if provided
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('purpose')) {
            // We'll add purpose field later, for now filter by price range
            if ($request->purpose === 'rent') {
                $query->where('price', '<=', 1000000); // Assuming rent properties are under 1M
            } elseif ($request->purpose === 'sale') {
                $query->where('price', '>', 1000000); // Assuming sale properties are over 1M
            }
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', '>=', $request->bathrooms);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'priority');
        $sortOrder = $request->get('order', 'desc');
        
        if ($sortBy === 'priority') {
            $query->byPriority();
        } elseif (in_array($sortBy, ['price', 'created_at', 'bedrooms', 'bathrooms', 'view_count'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->byPriority();
        }

        $properties = $query->paginate(12);

        // Get filter options for the sidebar
        $filterOptions = [
            'types' => Property::where('status', 'active')->distinct()->pluck('type')->filter(),
            'locations' => Property::where('status', 'active')->distinct()->pluck('location')->filter(),
            'bedrooms' => Property::where('status', 'active')->distinct()->pluck('bedrooms')->filter()->sort(),
            'bathrooms' => Property::where('status', 'active')->distinct()->pluck('bathrooms')->filter()->sort(),
        ];

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

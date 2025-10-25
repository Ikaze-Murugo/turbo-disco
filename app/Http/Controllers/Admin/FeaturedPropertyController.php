<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FeaturedPropertyController extends Controller
{
    /**
     * Display a listing of properties for featured management
     */
    public function index(Request $request)
    {
        $query = Property::with(['landlord', 'images'])
            ->where('status', 'active');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            \Log::info('Search query received:', ['search' => $search]);
            
            // Debug: Check if any landlords match the search
            $matchingLandlords = \App\Models\User::where('name', 'like', '%' . $search . '%')->get(['id', 'name']);
            \Log::info('Matching landlords:', $matchingLandlords->toArray());
            
            // Debug: Check total active properties
            $totalActive = Property::where('status', 'active')->count();
            \Log::info('Total active properties:', ['count' => $totalActive]);
            
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%')
                  ->orWhereHas('landlord', function ($landlordQuery) use ($search) {
                      $landlordQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by featured status
        if ($request->filled('featured_status')) {
            if ($request->featured_status === 'featured') {
                $query->where('is_featured', true);
            } elseif ($request->featured_status === 'not_featured') {
                $query->where('is_featured', false);
            }
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Sort by featured status, then by creation date
        $query->orderBy('is_featured', 'desc')
              ->orderBy('created_at', 'desc');

        $properties = $query->paginate(20);
        
        // Debug: Log the final results
        \Log::info('Final query results:', [
            'total_results' => $properties->total(),
            'current_page_results' => $properties->count(),
            'search_term' => $request->get('search', 'none')
        ]);

        // Get statistics
        $stats = [
            'total_properties' => Property::where('status', 'active')->count(),
            'featured_properties' => Property::where('status', 'active')->where('is_featured', true)->count(),
            'expiring_soon' => Property::where('status', 'active')
                ->where('is_featured', true)
                ->where('featured_until', '<=', now()->addDays(3))
                ->count(),
        ];

        return view('admin.featured-properties.index', compact('properties', 'stats'));
    }

    /**
     * Feature a property with duration and priority
     */
    public function feature(Request $request, Property $property)
    {
        $request->validate([
            'duration' => 'required|in:7,14,30',
            'priority' => 'required|in:low,medium,high',
        ]);

        $property->update([
            'is_featured' => true,
            'featured_until' => now()->addDays($request->duration),
            'priority' => $request->priority,
        ]);

        // Clear featured properties cache
        Cache::forget('featured_properties');

        return back()->with('success', "Property '{$property->title}' has been featured for {$request->duration} days with {$request->priority} priority.");
    }

    /**
     * Unfeature a property
     */
    public function unfeature(Property $property)
    {
        $property->update([
            'is_featured' => false,
            'featured_until' => null,
            'priority' => 'low',
        ]);

        // Clear featured properties cache
        Cache::forget('featured_properties');

        return back()->with('success', "Property '{$property->title}' has been unfeatured.");
    }

    /**
     * Bulk feature properties
     */
    public function bulkFeature(Request $request)
    {
        $request->validate([
            'property_ids' => 'required|array|min:1',
            'property_ids.*' => 'exists:properties,id',
            'duration' => 'required|in:7,14,30',
            'priority' => 'required|in:low,medium,high',
        ]);

        $count = Property::whereIn('id', $request->property_ids)
            ->where('status', 'active')
            ->update([
                'is_featured' => true,
                'featured_until' => now()->addDays($request->duration),
                'priority' => $request->priority,
            ]);

        // Clear featured properties cache
        Cache::forget('featured_properties');

        return back()->with('success', "Successfully featured {$count} properties for {$request->duration} days with {$request->priority} priority.");
    }

    /**
     * Bulk unfeature properties
     */
    public function bulkUnfeature(Request $request)
    {
        $request->validate([
            'property_ids' => 'required|array|min:1',
            'property_ids.*' => 'exists:properties,id',
        ]);

        $count = Property::whereIn('id', $request->property_ids)
            ->update([
                'is_featured' => false,
                'featured_until' => null,
                'priority' => 'low',
            ]);

        // Clear featured properties cache
        Cache::forget('featured_properties');

        return back()->with('success', "Successfully unfeatured {$count} properties.");
    }

    /**
     * Get analytics data for featured properties
     */
    public function analytics()
    {
        $analytics = [
            'featured_count' => Property::where('is_featured', true)->count(),
            'expiring_this_week' => Property::where('is_featured', true)
                ->where('featured_until', '<=', now()->addWeek())
                ->count(),
            'by_priority' => Property::where('is_featured', true)
                ->selectRaw('priority, count(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority'),
            'recently_featured' => Property::where('is_featured', true)
                ->where('updated_at', '>=', now()->subWeek())
                ->count(),
        ];

        return response()->json($analytics);
    }
}

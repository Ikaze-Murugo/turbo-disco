<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\User;
use App\Models\Review;
use App\Models\Report;
use App\Models\MessageReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomepageController extends Controller
{
    /**
     * Display the comprehensive homepage
     */
    public function index()
    {
        // Get featured properties (we'll implement this later)
        $featuredProperties = $this->getFeaturedProperties();
        
        // Get market statistics
        $marketStats = $this->getMarketStatistics();
        
        // Get recent properties
        $recentProperties = $this->getRecentProperties();
        
        // Get success stories/testimonials
        $testimonials = $this->getTestimonials();
        
        // Get blog posts (if we implement this later)
        $blogPosts = $this->getBlogPosts();

        return view('homepage.index', compact(
            'featuredProperties',
            'marketStats', 
            'recentProperties',
            'testimonials',
            'blogPosts'
        ));
    }

    /**
     * Get featured properties for homepage
     */
    private function getFeaturedProperties()
    {
        return Cache::remember('featured_properties', 300, function () {
            return Property::where('status', 'active')
                ->where('is_available', true)
                ->where('is_featured', true)  // Only get actually featured properties
                ->with(['landlord', 'images'])
                ->orderBy('priority', 'desc')  // Order by priority (high, medium, low)
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
        });
    }

    /**
     * Get market statistics
     */
    private function getMarketStatistics()
    {
        return Cache::remember('market_statistics', 600, function () {
            return [
                'total_properties' => Property::where('status', 'active')->count(),
                'total_landlords' => User::where('role', 'landlord')->count(),
                'total_renters' => User::where('role', 'renter')->count(),
                'average_rent' => Property::where('status', 'active')->avg('price') ?? 0,
                'properties_this_month' => Property::where('status', 'active')
                    ->whereMonth('created_at', now()->month)
                    ->count(),
            ];
        });
    }

    /**
     * Get recent properties
     */
    private function getRecentProperties()
    {
        return Property::where('status', 'active')
            ->where('is_available', true)
            ->with(['landlord', 'images'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();
    }

    /**
     * Get testimonials/success stories
     */
    private function getTestimonials()
    {
        // For now, return some sample testimonials
        // Later we can create a testimonials table
        return [
            [
                'name' => 'Jean Baptiste',
                'role' => 'Property Owner',
                'location' => 'Kigali',
                'content' => 'Murugo helped me find the perfect tenant for my apartment. The platform is easy to use and the support team is excellent.',
                'rating' => 5,
            ],
            [
                'name' => 'Marie Claire',
                'role' => 'Renter',
                'location' => 'Kigali',
                'content' => 'I found my dream home through Murugo. The search filters made it easy to find exactly what I was looking for.',
                'rating' => 5,
            ],
            [
                'name' => 'Paul Mugenzi',
                'role' => 'Property Owner',
                'location' => 'Kigali',
                'content' => 'The analytics dashboard helps me understand my property performance. Great platform for landlords.',
                'rating' => 5,
            ],
        ];
    }

    /**
     * Get blog posts (placeholder for future implementation)
     */
    private function getBlogPosts()
    {
        // Placeholder for future blog implementation
        return [];
    }

    /**
     * Get search suggestions for autocomplete
     */
    public function getSearchSuggestions(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = Cache::remember("search_suggestions_{$query}", 300, function () use ($query) {
            $properties = Property::where('status', 'active')
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('location', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                })
                ->limit(5)
                ->get(['id', 'title', 'location', 'price']);

            return $properties->map(function ($property) {
                return [
                    'id' => $property->id,
                    'title' => $property->title,
                    'location' => $property->location,
                    'price' => number_format($property->price),
                    'url' => route('properties.show', $property),
                ];
            });
        });

        return response()->json($suggestions);
    }

    /**
     * Handle homepage search
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $location = $request->get('location', '');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $propertyType = $request->get('property_type');

        $properties = Property::where('status', 'active')
            ->where('is_available', true)
            ->with(['landlord', 'images']);

        if ($query) {
            $properties->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });
        }

        if ($location) {
            $properties->where('location', 'like', "%{$location}%");
        }

        if ($minPrice) {
            $properties->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $properties->where('price', '<=', $maxPrice);
        }

        if ($propertyType) {
            $properties->where('type', $propertyType);
        }

        $properties = $properties->paginate(12);

        return view('homepage.search-results', compact('properties', 'query', 'location', 'minPrice', 'maxPrice', 'propertyType'));
    }
}

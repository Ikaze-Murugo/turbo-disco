<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Get user's favorites
     */
    public function index(Request $request)
    {
        $favorites = Favorite::with(['property.images', 'property.landlord:id,name,business_name'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => [
                'favorites' => $favorites->items(),
                'pagination' => [
                    'total' => $favorites->total(),
                    'per_page' => $favorites->perPage(),
                    'current_page' => $favorites->currentPage(),
                    'last_page' => $favorites->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Add property to favorites
     */
    public function store(Request $request, $propertyId)
    {
        $property = Property::findOrFail($propertyId);

        $favorite = Favorite::firstOrCreate([
            'user_id' => $request->user()->id,
            'property_id' => $property->id,
        ]);

        $wasRecentlyCreated = $favorite->wasRecentlyCreated;

        return response()->json([
            'success' => true,
            'message' => $wasRecentlyCreated ? 'Property added to favorites' : 'Property already in favorites',
            'data' => [
                'favorite' => $favorite->load('property.images')
            ]
        ], $wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Remove property from favorites
     */
    public function destroy(Request $request, $propertyId)
    {
        $deleted = Favorite::where('user_id', $request->user()->id)
            ->where('property_id', $propertyId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted ? 'Property removed from favorites' : 'Property not in favorites'
        ]);
    }

    /**
     * Check if property is favorited
     */
    public function check(Request $request, $propertyId)
    {
        $isFavorited = Favorite::where('user_id', $request->user()->id)
            ->where('property_id', $propertyId)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'is_favorited' => $isFavorited
            ]
        ]);
    }
}

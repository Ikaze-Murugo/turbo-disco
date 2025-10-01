<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Display user's favorites
     */
    public function index(Request $request)
    {
        $listName = $request->get('list', 'default');
        $wishlists = Favorite::getUserWishlists(Auth::id());
        $favorites = Favorite::getWishlistProperties(Auth::id(), $listName);

        return view('favorites.index', compact('favorites', 'wishlists', 'listName'));
    }

    /**
     * Add property to favorites
     */
    public function store(Request $request, Property $property)
    {
        // Only renters can favorite properties
        if (!Auth::user()->isRenter()) {
            abort(403);
        }

        $request->validate([
            'list_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if already favorited
        if ($property->isFavoritedBy(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Property already in your favorites!'
            ]);
        }

        Favorite::create([
            'user_id' => Auth::id(),
            'property_id' => $property->id,
            'list_name' => $request->list_name ?? 'default',
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Property added to favorites!'
        ]);
    }

    /**
     * Remove property from favorites
     */
    public function destroy(Property $property)
    {
        $favorite = $property->getFavoriteForUser(Auth::id());
        
        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found in favorites!'
            ]);
        }

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Property removed from favorites!'
        ]);
    }

    /**
     * Update favorite (change list or notes)
     */
    public function update(Request $request, Property $property)
    {
        $favorite = $property->getFavoriteForUser(Auth::id());
        
        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found in favorites!'
            ]);
        }

        $request->validate([
            'list_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $favorite->update([
            'list_name' => $request->list_name ?? $favorite->list_name,
            'notes' => $request->notes ?? $favorite->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Favorite updated successfully!'
        ]);
    }

    /**
     * Create a new wishlist
     */
    public function createWishlist(Request $request)
    {
        $request->validate([
            'list_name' => 'required|string|max:255|unique:favorites,list_name,NULL,id,user_id,' . Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Wishlist created successfully!'
        ]);
    }

    /**
     * Get wishlist properties via AJAX
     */
    public function getWishlist(Request $request)
    {
        $listName = $request->get('list', 'default');
        $favorites = Favorite::getWishlistProperties(Auth::id(), $listName);

        return response()->json([
            'success' => true,
            'favorites' => $favorites
        ]);
    }
}
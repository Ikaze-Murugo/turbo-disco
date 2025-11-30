<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MobilePropertyController extends Controller
{
    /**
     * Get all properties with filters
     */
    public function index(Request $request)
    {
        $query = Property::with(['landlord:id,name,email,phone_number,business_name', 'images'])
            ->where('status', 'active')
            ->where('is_available', true);

        // Apply filters
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        if ($request->has('bathrooms')) {
            $query->where('bathrooms', '>=', $request->bathrooms);
        }

        if ($request->has('furnishing_status')) {
            $query->where('furnishing_status', $request->furnishing_status);
        }

        if ($request->has('neighborhood')) {
            $query->where('neighborhood', 'ILIKE', '%' . $request->neighborhood . '%');
        }

        if ($request->has('location')) {
            $query->where('location', 'ILIKE', '%' . $request->location . '%');
        }

        // Amenities filters
        $amenities = ['has_balcony', 'has_garden', 'has_pool', 'has_gym', 'has_security', 
                      'has_elevator', 'has_air_conditioning', 'has_heating', 'has_internet', 
                      'has_cable_tv', 'pets_allowed', 'smoking_allowed'];
        
        foreach ($amenities as $amenity) {
            if ($request->has($amenity) && $request->$amenity == 'true') {
                $query->where($amenity, true);
            }
        }

        // Location-based search (radius)
        if ($request->has('latitude') && $request->has('longitude') && $request->has('radius')) {
            $lat = $request->latitude;
            $lng = $request->longitude;
            $radius = $request->radius; // in kilometers

            $query->whereRaw("
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
                sin(radians(latitude)))) <= ?
            ", [$lat, $lng, $lat, $radius]);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['price', 'created_at', 'bedrooms', 'bathrooms', 'area'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $properties = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'properties' => $properties->items(),
                'pagination' => [
                    'total' => $properties->total(),
                    'per_page' => $properties->perPage(),
                    'current_page' => $properties->currentPage(),
                    'last_page' => $properties->lastPage(),
                    'from' => $properties->firstItem(),
                    'to' => $properties->lastItem(),
                ]
            ]
        ]);
    }

    /**
     * Get single property
     */
    public function show($id)
    {
        $property = Property::with(['landlord:id,name,email,phone_number,business_name,bio,location,website', 'images', 'reviews.user:id,name,profile_picture'])
            ->findOrFail($id);

        // Increment views count
        $property->increment('views_count');

        return response()->json([
            'success' => true,
            'data' => [
                'property' => $property
            ]
        ]);
    }

    /**
     * Create property (landlord only)
     */
    public function store(Request $request)
    {
        if ($request->user()->role !== 'landlord') {
            return response()->json([
                'success' => false,
                'message' => 'Only landlords can create properties'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:apartment,house,studio,villa,commercial,land',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'area' => 'nullable|numeric|min:0',
            'location' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'furnishing_status' => 'required|in:furnished,unfurnished,semi-furnished',
            'parking_spaces' => 'nullable|integer|min:0',
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $property = Property::create([
            'landlord_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'type' => $request->type,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
            'area' => $request->area,
            'location' => $request->location,
            'address' => $request->address,
            'neighborhood' => $request->neighborhood,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'coordinates' => json_encode(['lat' => $request->latitude, 'lng' => $request->longitude]),
            'furnishing_status' => $request->furnishing_status,
            'parking_spaces' => $request->parking_spaces ?? 0,
            'status' => 'pending',
            'is_available' => true,
            // Amenities
            'has_balcony' => $request->boolean('has_balcony'),
            'has_garden' => $request->boolean('has_garden'),
            'has_pool' => $request->boolean('has_pool'),
            'has_gym' => $request->boolean('has_gym'),
            'has_security' => $request->boolean('has_security'),
            'has_elevator' => $request->boolean('has_elevator'),
            'has_air_conditioning' => $request->boolean('has_air_conditioning'),
            'has_heating' => $request->boolean('has_heating'),
            'has_internet' => $request->boolean('has_internet'),
            'has_cable_tv' => $request->boolean('has_cable_tv'),
            'pets_allowed' => $request->boolean('pets_allowed'),
            'smoking_allowed' => $request->boolean('smoking_allowed'),
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties', 'public');
                
                Image::create([
                    'property_id' => $property->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'order' => $index,
                ]);
            }
        }

        $property->load('images');

        return response()->json([
            'success' => true,
            'message' => 'Property created successfully and pending approval',
            'data' => [
                'property' => $property
            ]
        ], 201);
    }

    /**
     * Update property (landlord only)
     */
    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        if ($property->landlord_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'type' => 'sometimes|in:apartment,house,studio,villa,commercial,land',
            'bedrooms' => 'sometimes|integer|min:0',
            'bathrooms' => 'sometimes|integer|min:0',
            'area' => 'nullable|numeric|min:0',
            'location' => 'sometimes|string|max:255',
            'address' => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'furnishing_status' => 'sometimes|in:furnished,unfurnished,semi-furnished',
            'parking_spaces' => 'nullable|integer|min:0',
            'is_available' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $property->update($request->only([
            'title', 'description', 'price', 'type', 'bedrooms', 'bathrooms', 
            'area', 'location', 'address', 'neighborhood', 'latitude', 'longitude',
            'furnishing_status', 'parking_spaces', 'is_available',
            'has_balcony', 'has_garden', 'has_pool', 'has_gym', 'has_security',
            'has_elevator', 'has_air_conditioning', 'has_heating', 'has_internet',
            'has_cable_tv', 'pets_allowed', 'smoking_allowed'
        ]));

        // Update coordinates if latitude/longitude changed
        if ($request->has('latitude') && $request->has('longitude')) {
            $property->update([
                'coordinates' => json_encode(['lat' => $request->latitude, 'lng' => $request->longitude])
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Property updated successfully',
            'data' => [
                'property' => $property->fresh(['images'])
            ]
        ]);
    }

    /**
     * Delete property (landlord only)
     */
    public function destroy(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        if ($property->landlord_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Delete images from storage
        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $property->delete();

        return response()->json([
            'success' => true,
            'message' => 'Property deleted successfully'
        ]);
    }

    /**
     * Get user's properties (landlord)
     */
    public function myProperties(Request $request)
    {
        $properties = Property::with('images')
            ->where('landlord_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => [
                'properties' => $properties->items(),
                'pagination' => [
                    'total' => $properties->total(),
                    'per_page' => $properties->perPage(),
                    'current_page' => $properties->currentPage(),
                    'last_page' => $properties->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Get featured properties
     */
    public function featured()
    {
        $properties = Property::with(['landlord:id,name,business_name', 'images'])
            ->where('status', 'active')
            ->where('is_available', true)
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'properties' => $properties
            ]
        ]);
    }
}

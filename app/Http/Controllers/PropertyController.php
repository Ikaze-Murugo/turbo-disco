<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Image;
use App\Services\LocationService;
use App\Services\GeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user && $user->isAdmin()) {
            // Admin sees all properties including pending updates
            $properties = Property::with(['landlord', 'images', 'pendingUpdates', 'latestApprovedVersion'])
                                ->orderBy('created_at', 'desc')
                                ->paginate(12);
        } elseif ($user && $user->isLandlord()) {
            // Landlord sees only their properties (original versions)
            $properties = Property::where('landlord_id', $user->id)
                                ->where('version_status', 'original')
                                ->with(['landlord', 'images', 'pendingUpdates'])
                                ->orderBy('created_at', 'desc')
                                ->paginate(12);
        } else {
            // Everyone else (renters, guests) sees approved properties only
            $properties = Property::where('status', 'active')
                                ->where('is_available', true)
                                ->where(function($query) {
                                    $query->where('version_status', 'original')
                                          ->orWhere('version_status', 'approved_update');
                                })
                                ->with(['landlord', 'images'])
                                ->orderBy('created_at', 'desc')
                                ->paginate(12);
        }

        return view('properties.index', compact('properties'));
    }

    public function show(Property $property)
    {
        $property->load(['landlord', 'images', 'propertyAmenities.amenity']);
        
        // Get nearby amenities if property has coordinates
        $nearbyAmenities = collect();
        if ($property->hasCoordinates()) {
            $locationService = new LocationService();
            $nearbyAmenities = $locationService->getCachedPropertyAmenities($property);
        }
        
        return view('properties.show', compact('property', 'nearbyAmenities'));
    }

    public function create()
    {
        // Only landlords can create properties
        if (!Auth::user()->isLandlord()) {
            abort(403);
        }

        return view('properties.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isLandlord()) {
            abort(403);
        }

        // Debug: Log all request data
        \Log::info('Property store request received', [
            'has_images' => $request->hasFile('images'),
            'files' => $request->allFiles(),
            'all_inputs' => $request->except(['password', '_token'])
        ]);

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'type' => 'required|string|in:house,apartment,studio,condo,villa,townhouse,commercial',
                'address' => 'required|string|max:500',
                'neighborhood' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'bedrooms' => 'required|integer|min:0',
                'bathrooms' => 'required|integer|min:0',
                'area' => 'nullable|numeric|min:0',
                'parking_spaces' => 'nullable|integer|min:0',
                'furnishing_status' => 'nullable|string|in:furnished,semi_furnished,unfurnished',
                'has_balcony' => 'boolean',
                'has_garden' => 'boolean',
                'has_pool' => 'boolean',
                'has_gym' => 'boolean',
                'has_security' => 'boolean',
                'has_elevator' => 'boolean',
                'has_air_conditioning' => 'boolean',
                'has_heating' => 'boolean',
                'has_internet' => 'boolean',
                'has_cable_tv' => 'boolean',
                'pets_allowed' => 'boolean',
                'smoking_allowed' => 'boolean',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'blueprints.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'image_types.*' => 'nullable|string|in:exterior,interior,kitchen,bathroom,bedroom,living_room,garden,parking',
            ]);

            // Geocode the address if coordinates are not provided
            if (!$validated['latitude'] || !$validated['longitude']) {
                try {
                    $geocodingService = new GeocodingService();
                    $coordinates = $geocodingService->geocodeWithFallback(
                        $validated['address'],
                        $validated['latitude'],
                        $validated['longitude']
                    );
                    
                    if ($coordinates) {
                        $validated['latitude'] = $coordinates['latitude'];
                        $validated['longitude'] = $coordinates['longitude'];
                        if (isset($coordinates['formatted_address'])) {
                            $validated['address'] = $coordinates['formatted_address'];
                        }
                    } else {
                        // If geocoding fails, use default coordinates for Kigali
                        $validated['latitude'] = -1.9441;
                        $validated['longitude'] = 30.0619;
                    }
                } catch (\Exception $e) {
                    // If geocoding fails, use default coordinates for Kigali
                    $validated['latitude'] = -1.9441;
                    $validated['longitude'] = 30.0619;
                }
            }

            $validated['landlord_id'] = Auth::id();
            $validated['status'] = 'pending'; // Requires admin approval
            $validated['is_available'] = true; // Set default availability
            $validated['location'] = $validated['address']; // Set location to same as address

            // Handle checkbox fields - set to false if not present
            $checkboxFields = [
                'has_balcony', 'has_garden', 'has_pool', 'has_gym', 'has_security',
                'has_elevator', 'has_air_conditioning', 'has_heating', 'has_internet',
                'has_cable_tv', 'pets_allowed', 'smoking_allowed'
            ];

            foreach ($checkboxFields as $field) {
                $validated[$field] = $request->has($field) ? (bool) $request->input($field) : false;
            }

            // Only include the fields we want to save (avoid array to string conversion issues)
            $propertyData = [
                'landlord_id' => $validated['landlord_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'location' => $validated['location'],
                'address' => $validated['address'],
                'neighborhood' => $validated['neighborhood'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'type' => $validated['type'],
                'bedrooms' => $validated['bedrooms'],
                'bathrooms' => $validated['bathrooms'],
                'area' => $validated['area'] ?? null,
                'furnishing_status' => $validated['furnishing_status'] ?? null,
                'parking_spaces' => $validated['parking_spaces'] ?? 0,
                'status' => $validated['status'],
                'is_available' => $validated['is_available'],
            ];
            
            // Add checkbox fields
            foreach ($checkboxFields as $field) {
                $propertyData[$field] = $validated[$field];
            }

            $property = Property::create($propertyData);

            // Handle image uploads
            \Log::info('Checking for image uploads', [
                'hasFile' => $request->hasFile('images'),
                'allFiles' => $request->allFiles(),
                'property_id' => $property->id
            ]);
            
            if ($request->hasFile('images')) {
                \Log::info('Images found, processing...', ['count' => count($request->file('images'))]);
                
                foreach ($request->file('images') as $index => $file) {
                    if ($file && $file->isValid() && $file->getSize() > 0) {
                        try {
                            $filename = time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                            $path = $file->storeAs('properties/' . $property->id, $filename, 'public');
                            
                            $imageType = $request->input("image_types.{$index}", 'interior');

                            $image = Image::create([
                                'property_id' => $property->id,
                                'filename' => $filename,
                                'path' => $path,
                                'image_type' => $imageType,
                                'is_primary' => $index === 0,
                                'image_order' => $index,
                                'sort_order' => $index,
                            ]);
                            
                            \Log::info('Image saved successfully', ['image_id' => $image->id, 'path' => $path]);
                        } catch (\Exception $e) {
                            \Log::error('Image upload failed: ' . $e->getMessage(), [
                                'property_id' => $property->id,
                                'filename' => $filename ?? 'unknown',
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                            // Continue with other images even if one fails
                        }
                    } else {
                        \Log::warning('Invalid image file', ['index' => $index, 'valid' => $file ? $file->isValid() : false]);
                    }
                }
            } else {
                \Log::warning('No images found in request');
            }

            // Handle blueprint uploads
            if ($request->hasFile('blueprints')) {
                foreach ($request->file('blueprints') as $index => $file) {
                    if ($file && $file->isValid() && $file->getSize() > 0) {
                        try {
                            $filename = time() . '_blueprint_' . $index . '.' . $file->getClientOriginalExtension();
                            $path = $file->storeAs('properties/' . $property->id, $filename, 'public');

                            Image::create([
                                'property_id' => $property->id,
                                'filename' => $filename,
                                'path' => $path,
                                'image_type' => 'blueprint',
                                'is_primary' => false,
                                'image_order' => 0,
                                'sort_order' => 0,
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Blueprint upload failed: ' . $e->getMessage(), [
                                'property_id' => $property->id,
                                'filename' => $filename ?? 'unknown',
                            ]);
                        }
                    }
                }
            }

            // Cache nearby amenities for this property
            if ($property->hasCoordinates()) {
                $locationService = new LocationService();
                $locationService->cachePropertyAmenities($property);
            }

            return redirect()->route('properties.index')
                            ->with('success', 'Property created successfully! Waiting for admin approval.');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Error creating property: ' . $e->getMessage());
        }
    }

    public function edit(Property $property)
    {
        // Only property owner or admin can edit
        if (!Auth::user()->isAdmin() && $property->landlord_id !== Auth::id()) {
            abort(403);
        }

        return view('properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        if (!Auth::user()->isAdmin() && $property->landlord_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'bedrooms' => 'required|integer|min:1',
            'bathrooms' => 'required|integer|min:1',
            'type' => 'required|string|in:house,apartment,studio,condo,villa,townhouse,commercial',
            'area' => 'nullable|numeric|min:0',
            'parking_spaces' => 'nullable|integer|min:0',
            'furnishing_status' => 'nullable|string|in:furnished,semi_furnished,unfurnished',
            'has_balcony' => 'boolean',
            'has_garden' => 'boolean',
            'has_pool' => 'boolean',
            'has_gym' => 'boolean',
            'has_security' => 'boolean',
            'has_elevator' => 'boolean',
            'has_air_conditioning' => 'boolean',
            'has_heating' => 'boolean',
            'has_internet' => 'boolean',
            'has_cable_tv' => 'boolean',
            'pets_allowed' => 'boolean',
            'smoking_allowed' => 'boolean',
            'is_available' => 'sometimes|boolean',
            'update_notes' => 'nullable|string|max:500',
        ]);

        // Handle checkbox fields - set to false if not present
        $checkboxFields = [
            'has_balcony', 'has_garden', 'has_pool', 'has_gym', 'has_security',
            'has_elevator', 'has_air_conditioning', 'has_heating', 'has_internet',
            'has_cable_tv', 'pets_allowed', 'smoking_allowed'
        ];

        foreach ($checkboxFields as $field) {
            $validated[$field] = $request->has($field) ? (bool) $request->input($field) : false;
        }

        // Remove update_notes from validated data as it's handled separately
        $updateNotes = $validated['update_notes'] ?? null;
        unset($validated['update_notes']);

        // Only include fields that should be updated (avoid array fields)
        $updateData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'location' => $validated['location'],
            'type' => $validated['type'],
            'bedrooms' => $validated['bedrooms'],
            'bathrooms' => $validated['bathrooms'],
            'area' => $validated['area'] ?? null,
            'furnishing_status' => $validated['furnishing_status'] ?? null,
            'parking_spaces' => $validated['parking_spaces'] ?? 0,
            'is_available' => $validated['is_available'] ?? $property->is_available,
        ];
        
        // Add checkbox fields
        foreach ($checkboxFields as $field) {
            $updateData[$field] = $validated[$field];
        }

        // Check if this is an admin updating directly
        if (Auth::user()->isAdmin()) {
            $updateData['status'] = $request->input('status', $property->status);
            $property->update($updateData);
            
            return redirect()->route('properties.index')
                            ->with('success', 'Property updated successfully!');
        }

        // For landlords: check if property is approved and needs versioning
        if ($property->status === 'active' && $property->version_status === 'original') {
            // Property is approved - create a pending version
            $changes = array_diff_assoc($updateData, $property->toArray());
            
            if (!empty($changes)) {
                $pendingVersion = $property->createPendingVersion($changes, $updateNotes);
                
                return redirect()->route('properties.index')
                                ->with('success', 'Property update submitted for admin approval. You will be notified once it\'s reviewed.');
            } else {
                return redirect()->route('properties.index')
                                ->with('info', 'No changes detected.');
            }
        } else {
            // Property is not approved yet - update directly
            $property->update($updateData);
            
            return redirect()->route('properties.index')
                            ->with('success', 'Property updated successfully!');
        }

        // Handle new image uploads
        if ($request->hasFile('new_images')) {
            $request->validate([
                'new_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            foreach ($request->file('new_images') as $index => $file) {
                $filename = time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('properties/' . $property->id, $filename, 'public');

                Image::create([
                    'property_id' => $property->id,
                    'filename' => $filename,
                    'path' => $path,
                    'is_primary' => false, // New images are not primary by default
                    'sort_order' => $property->images()->count() + $index,
                ]);
            }
        }
    }

    public function destroy(Property $property)
    {
        if (!Auth::user()->isAdmin() && $property->landlord_id !== Auth::id()) {
            abort(403);
        }

        $property->delete();

        return redirect()->route('properties.index')
                        ->with('success', 'Property deleted successfully!');
    }


    /**
     * Add property to comparison
     */
    public function addToComparison(Property $property)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $user = Auth::user();
        
        if (!$user->isRenter()) {
            return response()->json(['error' => 'Only renters can compare properties'], 403);
        }

        // Get current comparison from session
        $comparison = session('property_comparison', []);
        
        // Add property if not already in comparison (max 3 properties)
        if (!in_array($property->id, $comparison) && count($comparison) < 3) {
            $comparison[] = $property->id;
            session(['property_comparison' => $comparison]);
            return response()->json(['added' => true, 'message' => 'Added to comparison']);
        } elseif (in_array($property->id, $comparison)) {
            return response()->json(['added' => false, 'message' => 'Property already in comparison']);
        } else {
            return response()->json(['added' => false, 'message' => 'Maximum 3 properties can be compared']);
        }
    }

    /**
     * Remove property from comparison
     */
    public function removeFromComparison(Property $property)
    {
        $comparison = session('property_comparison', []);
        $comparison = array_values(array_filter($comparison, function($id) use ($property) {
            return $id != $property->id;
        }));
        session(['property_comparison' => $comparison]);
        
        return response()->json(['removed' => true, 'message' => 'Removed from comparison']);
    }

    /**
     * Show comparison page
     */
    public function compare()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if (!$user->isRenter()) {
            abort(403, 'Only renters can compare properties');
        }

        $comparisonIds = session('property_comparison', []);
        $properties = Property::whereIn('id', $comparisonIds)
                             ->with(['images', 'landlord'])
                             ->get();

        return view('properties.compare', compact('properties'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{
    public function store(Request $request, Property $property)
    {
        // Check if user owns the property or is admin
        if (!Auth::user()->isAdmin() && $property->landlord_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $uploadedImages = [];

        foreach ($request->file('images') as $index => $file) {
            $filename = time() . '_' . $index . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('properties/' . $property->id, $filename, 'public');

            $image = Image::create([
                'property_id' => $property->id,
                'filename' => $filename,
                'path' => $path,
                'alt_text' => $request->input('alt_text.' . $index),
                'is_primary' => $index === 0 && $property->images()->count() === 0,
                'sort_order' => $property->images()->count() + $index,
            ]);

            $uploadedImages[] = $image;
        }

        return response()->json([
            'success' => true,
            'images' => $uploadedImages,
            'message' => 'Images uploaded successfully!'
        ]);
    }

    public function destroy(Image $image)
    {
        // Check permissions
        if (!Auth::user()->isAdmin() && $image->property->landlord_id !== Auth::id()) {
            abort(403);
        }

        // Delete file from storage
        Storage::disk('public')->delete($image->path);

        // Delete from database
        $image->delete();

        return response()->json(['success' => true]);
    }

    public function setPrimary(Image $image)
    {
        // Check permissions
        if (!Auth::user()->isAdmin() && $image->property->landlord_id !== Auth::id()) {
            abort(403);
        }

        // Remove primary from other images
        $image->property->images()->update(['is_primary' => false]);

        // Set this image as primary
        $image->update(['is_primary' => true]);

        return response()->json(['success' => true]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display reviews for a property
     */
    public function index(Property $property)
    {
        $reviews = Review::getApprovedReviewsForProperty($property->id);
        $averageRating = Review::getAveragePropertyRating($property->id);
        $reviewCount = Review::getPropertyReviewCount($property->id);
        $hasReviewed = Review::hasUserReviewedProperty(Auth::id(), $property->id);

        return view('reviews.index', compact('property', 'reviews', 'averageRating', 'reviewCount', 'hasReviewed'));
    }

    /**
     * Show the form for creating a new review
     */
    public function create(Property $property)
    {
        // Only renters can create reviews
        if (!Auth::user()->isRenter()) {
            abort(403);
        }

        // Check if user has already reviewed this property
        if (Review::hasUserReviewedProperty(Auth::id(), $property->id)) {
            return redirect()->route('properties.show', $property)
                            ->with('error', 'You have already reviewed this property.');
        }

        return view('reviews.create', compact('property'));
    }

    /**
     * Store a newly created review
     */
    public function store(Request $request, Property $property)
    {
        // Only renters can create reviews
        if (!Auth::user()->isRenter()) {
            abort(403);
        }

        // Check if user has already reviewed this property
        if (Review::hasUserReviewedProperty(Auth::id(), $property->id)) {
            return redirect()->route('properties.show', $property)
                            ->with('error', 'You have already reviewed this property.');
        }

        $validated = $request->validate([
            'property_rating' => 'required|integer|min:1|max:5',
            'landlord_rating' => 'required|integer|min:1|max:5',
            'property_review' => 'nullable|string|max:1000',
            'landlord_review' => 'nullable|string|max:1000',
            'is_anonymous' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['property_id'] = $property->id;
        $validated['landlord_id'] = $property->landlord_id;
        $validated['is_approved'] = false; // Requires admin approval

        Review::create($validated);

        return redirect()->route('properties.show', $property)
                        ->with('success', 'Review submitted successfully! It will be published after admin approval.');
    }

    /**
     * Display the specified review
     */
    public function show(Review $review)
    {
        if (!$review->is_approved) {
            abort(404);
        }

        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing a review
     */
    public function edit(Review $review)
    {
        // Only the review author can edit
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        // Can't edit approved reviews
        if ($review->is_approved) {
            return redirect()->route('properties.show', $review->property)
                            ->with('error', 'Cannot edit approved reviews.');
        }

        return view('reviews.edit', compact('review'));
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, Review $review)
    {
        // Only the review author can update
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        // Can't update approved reviews
        if ($review->is_approved) {
            return redirect()->route('properties.show', $review->property)
                            ->with('error', 'Cannot update approved reviews.');
        }

        $validated = $request->validate([
            'property_rating' => 'required|integer|min:1|max:5',
            'landlord_rating' => 'required|integer|min:1|max:5',
            'property_review' => 'nullable|string|max:1000',
            'landlord_review' => 'nullable|string|max:1000',
            'is_anonymous' => 'boolean',
        ]);

        $review->update($validated);

        return redirect()->route('properties.show', $review->property)
                        ->with('success', 'Review updated successfully!');
    }

    /**
     * Remove the specified review
     */
    public function destroy(Review $review)
    {
        // Only the review author or admin can delete
        if ($review->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $property = $review->property;
        $review->delete();

        return redirect()->route('properties.show', $property)
                        ->with('success', 'Review deleted successfully!');
    }

    /**
     * Approve a review (Admin only)
     */
    public function approve(Review $review)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $review->update(['is_approved' => true]);

        return redirect()->back()
                        ->with('success', 'Review approved successfully!');
    }

    /**
     * Reject a review (Admin only)
     */
    public function reject(Review $review)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $review->delete();

        return redirect()->back()
                        ->with('success', 'Review rejected and deleted.');
    }
}
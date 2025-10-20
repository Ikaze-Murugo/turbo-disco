<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;

class LandlordProfileController extends Controller
{
    public function show(Request $request, User $user, ?string $slug = null)
    {
        // Only landlords have public profiles
        if (!$user->isLandlord()) {
            abort(404);
        }

        // Compute a simple slug for display/SEO (optional). Avoid redirects to prevent loops.
        $computedSlug = str($user->business_name ?: $user->name)->slug('-');

        // Determine viewer role and visibility
        $viewer = auth()->user();
        $isAdmin = $viewer && $viewer->isAdmin();

        // Status visibility rules
        $approvedStatuses = ['active', 'featured'];
        $adminVisibleStatuses = array_merge($approvedStatuses, ['pending', 'pending_update']);

        // Sorting rules
        // - Admin: show pending first, then approved, newest first
        // - Others: featured first, then newest approved
        $query = Property::where('landlord_id', $user->id)
            ->when($isAdmin, function ($q) use ($adminVisibleStatuses) {
                $q->whereIn('status', $adminVisibleStatuses)
                  ->orderByRaw("CASE WHEN status IN ('pending','pending_update') THEN 0 ELSE 1 END")
                  ->orderByDesc('created_at');
            }, function ($q) use ($approvedStatuses) {
                $q->whereIn('status', $approvedStatuses)
                  ->orderByRaw("CASE WHEN status = 'featured' THEN 0 ELSE 1 END")
                  ->orderByDesc('created_at');
            });

        $properties = $query->paginate(9);

        // Basic stats
        $stats = [
            'properties_listed' => Property::where('landlord_id', $user->id)->count(),
            'average_rating' => $user->getAverageLandlordRating(),
            'review_count' => $user->getLandlordReviewCount(),
        ];

        return view('landlords.show', compact('user', 'properties', 'stats', 'computedSlug', 'isAdmin'));
    }
}



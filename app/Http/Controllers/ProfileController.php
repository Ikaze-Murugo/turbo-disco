<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserStatistics;
use App\Models\LandlordStatistics;
use App\Models\AdminStatistics;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Display the user's profile overview.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $user->updateLastActive();

        // Get role-specific profile data
        $profileData = $this->getRoleSpecificProfile($user);

        return view('profile.index', [
            'user' => $user,
            'profileData' => $profileData,
        ]);
    }

    /**
     * Display the user's profile edit form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'bio' => ['nullable', 'string', 'max:500'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'location' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'social_links' => ['nullable', 'array'],
            'social_links.*' => ['nullable', 'url'],
            'preferences' => ['nullable', 'array'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'business_license' => ['nullable', 'string', 'max:255'],
            'emergency_contact' => ['nullable', 'array'],
        ]);

        // Ensure social_links is properly formatted as an array
        if (isset($validated['social_links']) && is_array($validated['social_links'])) {
            // Remove empty values from social_links
            $validated['social_links'] = array_filter($validated['social_links'], function($value) {
                return !empty($value);
            });
        }

        // Update user profile
        $user->update($validated);
        
        // Update profile completion percentage
        $user->profile_completion_percentage = $user->getProfileCompletionPercentage();
        $user->save();

        return Redirect::route('profile.index')->with('status', 'profile-updated');
    }

    /**
     * Update the user's profile picture.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_picture' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = $request->user();

        // Delete old profile picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Store new profile picture
        $path = $request->file('profile_picture')->store('profiles', 'public');
        $user->update(['profile_picture' => $path]);

        return Redirect::route('profile.index')->with('status', 'avatar-updated');
    }

    /**
     * Display the user's settings.
     */
    public function settings(Request $request): View
    {
        $user = $request->user();
        
        return view('profile.settings', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return Redirect::route('profile.settings')->with('status', 'password-updated');
    }

    /**
     * Update the user's preferences.
     */
    public function updatePreferences(Request $request): RedirectResponse
    {
        $request->validate([
            'preferences' => ['nullable', 'array'],
        ]);

        $user = $request->user();
        $user->update(['preferences' => $request->preferences]);

        return Redirect::route('profile.settings')->with('status', 'preferences-updated');
    }

    /**
     * Display the user's statistics.
     */
    public function statistics(Request $request): View
    {
        $user = $request->user();
        $statistics = $this->getRoleSpecificStatistics($user);

        return view('profile.statistics', [
            'user' => $user,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get role-specific profile data.
     */
    private function getRoleSpecificProfile(User $user): array
    {
        if ($user->isRenter()) {
            return $user->getRenterProfile();
        } elseif ($user->isLandlord()) {
            return $user->getLandlordProfile();
        } elseif ($user->isAdmin()) {
            return $user->getAdminProfile();
        }

        return [];
    }

    /**
     * Get role-specific statistics.
     */
    private function getRoleSpecificStatistics(User $user): array
    {
        if ($user->isRenter()) {
            return [
                'properties_viewed' => $this->getRenterPropertiesViewed($user),
                'properties_favorited' => $this->getRenterPropertiesFavorited($user),
                'messages_sent' => $this->getRenterMessagesSent($user),
                'reviews_given' => $this->getRenterReviewsGiven($user),
                'searches_performed' => $this->getRenterSearchesPerformed($user),
            ];
        } elseif ($user->isLandlord()) {
            return [
                'properties_listed' => $this->getLandlordPropertiesListed($user),
                'total_views' => $this->getLandlordTotalViews($user),
                'total_inquiries' => $this->getLandlordTotalInquiries($user),
                'response_rate' => $this->getLandlordResponseRate($user),
                'revenue_generated' => $this->getLandlordRevenueGenerated($user),
            ];
        } elseif ($user->isAdmin()) {
            return [
                'reports_processed' => $this->getAdminReportsProcessed($user),
                'properties_approved' => $this->getAdminPropertiesApproved($user),
                'users_managed' => $this->getAdminUsersManaged($user),
                'tickets_resolved' => $this->getAdminTicketsResolved($user),
                'system_actions' => $this->getAdminSystemActions($user),
            ];
        }

        return [];
    }

    // Renter Statistics Methods
    private function getRenterPropertiesViewed(User $user): int
    {
        // This would need a property_views table to track actual views
        // For now, return a placeholder
        return 0;
    }

    private function getRenterPropertiesFavorited(User $user): int
    {
        return $user->favorites()->count();
    }

    private function getRenterMessagesSent(User $user): int
    {
        return \App\Models\Message::where('sender_id', $user->id)->count();
    }

    private function getRenterReviewsGiven(User $user): int
    {
        return $user->reviews()->count();
    }

    private function getRenterSearchesPerformed(User $user): int
    {
        // This would need a search_history table
        return 0;
    }

    // Landlord Statistics Methods
    private function getLandlordPropertiesListed(User $user): int
    {
        return $user->properties()->count();
    }

    private function getLandlordTotalViews(User $user): int
    {
        return $user->properties()->sum('views_count');
    }

    private function getLandlordTotalInquiries(User $user): int
    {
        return \App\Models\Message::whereHas('property', function($query) use ($user) {
            $query->where('landlord_id', $user->id);
        })->count();
    }

    private function getLandlordResponseRate(User $user): float
    {
        $totalMessages = \App\Models\Message::whereHas('property', function($query) use ($user) {
            $query->where('landlord_id', $user->id);
        })->count();
        
        $responses = \App\Models\Message::where('sender_id', $user->id)->count();
        
        return $totalMessages > 0 ? round(($responses / $totalMessages) * 100, 2) : 0;
    }

    private function getLandlordRevenueGenerated(User $user): float
    {
        // This would need a transactions/rentals table
        return 0;
    }

    // Admin Statistics Methods
    private function getAdminReportsProcessed(User $user): int
    {
        return \App\Models\Report::where('assigned_to', $user->id)->count();
    }

    private function getAdminPropertiesApproved(User $user): int
    {
        return \App\Models\Property::where('approved_by', $user->id)->count();
    }

    private function getAdminUsersManaged(User $user): int
    {
        // This would need to track admin actions
        return 0;
    }

    private function getAdminTicketsResolved(User $user): int
    {
        return \App\Models\Report::where('assigned_to', $user->id)
            ->where('status', 'resolved')
            ->count();
    }

    private function getAdminSystemActions(User $user): int
    {
        // This would need an activity_log table
        return 0;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\Review;
use App\Models\Report;
use App\Models\MessageReport;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_landlords' => User::where('role', 'landlord')->count(),
            'total_renters' => User::where('role', 'renter')->count(),
            'total_properties' => Property::count(),
            'pending_properties' => Property::where('status', 'pending')->count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
            // Report statistics - include both property and message reports
            'total_reports' => Report::count() + MessageReport::count(),
            'total_property_reports' => Report::count(),
            'total_message_reports' => MessageReport::count(),
            'pending_reports' => Report::where('status', 'pending')->count() + MessageReport::where('status', 'pending')->count(),
            'urgent_reports' => Report::where('priority', 'urgent')->count() + MessageReport::where('priority', 'urgent')->count(),
            'resolved_reports' => Report::where('status', 'resolved')->count() + MessageReport::where('status', 'resolved')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function users()
    {
        $users = User::paginate(10);
        return view('admin.users', compact('users'));
    }

    public function updateUserStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $user->update($validated);

        return redirect()->back()
                        ->with('success', 'User status updated successfully!');
    }

    public function pendingProperties()
    {
        $properties = Property::where('status', 'pending')
                             ->with('landlord')
                             ->paginate(10);
        
        return view('admin.pending-properties', compact('properties'));
    }

    public function approveProperty(Property $property)
    {
        $property->update(['status' => 'active']);
        
        // Send email notification to landlord
        $landlord = $property->landlord;
        if ($landlord) {
            NotificationService::sendPropertyApprovedNotification($landlord, $property);
        }
        
        return redirect()->back()
                        ->with('success', 'Property approved successfully! Email notification sent to landlord.');
    }

    public function rejectProperty(Request $request, Property $property)
    {
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);
        
        $property->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'rejected_at' => now(),
        ]);
        
        // Send email notification to landlord
        $landlord = $property->landlord;
        if ($landlord) {
            NotificationService::sendPropertyRejectedNotification(
                $landlord, 
                $property, 
                $validated['rejection_reason'] ?? null
            );
        }
        
        return redirect()->back()
                        ->with('success', 'Property rejected successfully! Email notification sent to landlord.');
    }

    public function pendingReviews()
    {
        $reviews = Review::where('is_approved', false)
                        ->with(['user', 'property', 'landlord'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
        
        return view('admin.pending-reviews', compact('reviews'));
    }

    public function allProperties()
    {
        $properties = Property::where('status', 'active')
                             ->with(['landlord', 'images'])
                             ->orderBy('priority', 'asc')
                             ->orderBy('created_at', 'desc')
                             ->paginate(15);
        
        return view('admin.properties', compact('properties'));
    }

    public function updatePropertyPriority(Request $request, Property $property)
    {
        $validated = $request->validate([
            'priority' => 'required|in:low,medium,high',
            'is_featured' => 'boolean',
            'featured_until' => 'nullable|date|after:today',
        ]);

        $property->update($validated);

        return redirect()->back()
                        ->with('success', 'Property priority updated successfully!');
    }

    /**
     * Show pending property updates
     */
    public function pendingUpdates()
    {
        $pendingUpdates = Property::where('version_status', 'pending_update')
                                 ->with(['landlord', 'parentProperty', 'images'])
                                 ->orderBy('update_requested_at', 'desc')
                                 ->paginate(20);

        return view('admin.properties.pending-updates', compact('pendingUpdates'));
    }

    /**
     * Approve a property update
     */
    public function approveUpdate(Request $request, Property $property)
    {
        if ($property->version_status !== 'pending_update') {
            return redirect()->back()->with('error', 'This property update is not pending approval.');
        }

        try {
            $property->approveUpdate();
            
            return redirect()->route('admin.properties.pending-updates')
                            ->with('success', 'Property update approved successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve update: ' . $e->getMessage());
        }
    }

    /**
     * Reject a property update
     */
    public function rejectUpdate(Request $request, Property $property)
    {
        if ($property->version_status !== 'pending_update') {
            return redirect()->back()->with('error', 'This property update is not pending approval.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            $property->rejectUpdate($request->rejection_reason);
            
            return redirect()->route('admin.properties.pending-updates')
                            ->with('success', 'Property update rejected successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reject update: ' . $e->getMessage());
        }
    }
}
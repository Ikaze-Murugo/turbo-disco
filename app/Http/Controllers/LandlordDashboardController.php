<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Message;
use App\Models\Review;
use App\Models\Report;
use App\Models\MessageReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LandlordDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get landlord's properties
        $properties = Property::where('landlord_id', $user->id)
                            ->with(['images', 'reviews'])
                            ->latest()
                            ->get();
        
        // Calculate statistics
        $stats = [
            'total_properties' => $properties->count(),
            'active_properties' => $properties->where('status', 'active')->count(),
            'pending_properties' => $properties->where('status', 'pending')->count(),
            'total_views' => $properties->sum('views_count'),
            'total_reviews' => $properties->sum(function($property) {
                return $property->reviews->count();
            }),
            'average_rating' => $properties->avg(function($property) {
                return $property->reviews->avg('rating');
            }),
            'total_messages' => Message::where('sender_id', $user->id)
                ->orWhere('recipient_id', $user->id)->count(),
            'unread_messages' => Message::where('recipient_id', $user->id)
                ->where('is_read', false)->count(),
            'reports_received' => Report::whereHas('property', function($query) use ($user) {
                $query->where('landlord_id', $user->id);
            })->count(),
            'pending_reports' => Report::whereHas('property', function($query) use ($user) {
                $query->where('landlord_id', $user->id);
            })->where('status', 'pending')->count(),
        ];
        
        // Recent activity
        $recentMessages = Message::where('sender_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->with(['sender', 'recipient', 'property'])
            ->latest()
            ->limit(5)
            ->get();
        
        $recentReviews = Review::whereHas('property', function($query) use ($user) {
            $query->where('landlord_id', $user->id);
        })->with(['property', 'user'])
          ->latest()
          ->limit(5)
          ->get();
        
        // Property performance (last 30 days)
        $performanceData = $this->getPropertyPerformance($user->id);
        
        return view('landlord.dashboard', compact('stats', 'properties', 'recentMessages', 'recentReviews', 'performanceData'));
    }
    
    private function getPropertyPerformance($landlordId)
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        return [
            'views_this_month' => Property::where('landlord_id', $landlordId)
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->sum('views_count'),
            'new_messages_this_month' => Message::where('sender_id', $landlordId)
                ->orWhere('recipient_id', $landlordId)
                ->where('created_at', '>=', $thirtyDaysAgo)->count(),
            'new_reviews_this_month' => Review::whereHas('property', function($query) use ($landlordId) {
                $query->where('landlord_id', $landlordId);
            })->where('created_at', '>=', $thirtyDaysAgo)->count(),
        ];
    }
}

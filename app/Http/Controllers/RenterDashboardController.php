<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Message;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\Report;
use App\Models\MessageReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RenterDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get renter's data
        $favorites = Favorite::where('user_id', $user->id)
                            ->with('property.images')
                            ->latest()
                            ->limit(6)
                            ->get();
        
        $recentSearches = $this->getRecentSearches($user->id);
        
        // Calculate statistics
        $stats = [
            'total_favorites' => Favorite::where('user_id', $user->id)->count(),
            'total_messages' => Message::where('sender_id', $user->id)
                ->orWhere('recipient_id', $user->id)->count(),
            'unread_messages' => Message::where('recipient_id', $user->id)
                ->where('is_read', false)->count(),
            'reports_submitted' => Report::where('reporter_id', $user->id)->count(),
            'message_reports_submitted' => MessageReport::where('user_id', $user->id)->count(),
            'pending_reports' => Report::where('reporter_id', $user->id)
                ->where('status', 'pending')->count() + 
                MessageReport::where('user_id', $user->id)
                ->where('status', 'pending')->count(),
        ];
        
        // Recent activity
        $recentMessages = Message::where('sender_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->with(['sender', 'recipient', 'property'])
            ->latest()
            ->limit(5)
            ->get();
        
        $recentReports = Report::where('reporter_id', $user->id)
                              ->with('property')
                              ->latest()
                              ->limit(5)
                              ->get();
        
        // Recommended properties based on favorites
        $recommendedProperties = $this->getRecommendedProperties($user->id);
        
        // Activity summary (last 30 days)
        $activityData = $this->getActivitySummary($user->id);
        
        return view('renter.dashboard', compact(
            'stats', 
            'favorites', 
            'recentSearches', 
            'recentMessages', 
            'recentReports', 
            'recommendedProperties',
            'activityData'
        ));
    }
    
    private function getRecentSearches($userId)
    {
        // This would typically come from a search history table
        // For now, return empty array
        return [];
    }
    
    private function getRecommendedProperties($userId)
    {
        // Get user's favorite property types and locations
        $favoriteTypes = Favorite::where('user_id', $userId)
                                ->with('property')
                                ->get()
                                ->pluck('property.type')
                                ->unique()
                                ->toArray();
        
        $favoriteNeighborhoods = Favorite::where('user_id', $userId)
                                        ->with('property')
                                        ->get()
                                        ->pluck('property.neighborhood')
                                        ->unique()
                                        ->toArray();
        
        // Get recommended properties
        $recommended = Property::where('status', 'active')
                             ->where(function($query) use ($favoriteTypes, $favoriteNeighborhoods) {
                                 if (!empty($favoriteTypes)) {
                                     $query->whereIn('type', $favoriteTypes);
                                 }
                                 if (!empty($favoriteNeighborhoods)) {
                                     $query->orWhereIn('neighborhood', $favoriteNeighborhoods);
                                 }
                             })
                             ->with(['images', 'landlord'])
                             ->inRandomOrder()
                             ->limit(6)
                             ->get();
        
        return $recommended;
    }
    
    private function getActivitySummary($userId)
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        return [
            'properties_viewed' => 0, // Would need a property_views table
            'messages_sent' => Message::where('sender_id', $userId)
                ->where('created_at', '>=', $thirtyDaysAgo)->count(),
            'favorites_added' => Favorite::where('user_id', $userId)
                ->where('created_at', '>=', $thirtyDaysAgo)->count(),
            'reports_submitted' => Report::where('reporter_id', $userId)
                ->where('created_at', '>=', $thirtyDaysAgo)->count() +
                MessageReport::where('user_id', $userId)
                ->where('created_at', '>=', $thirtyDaysAgo)->count(),
        ];
    }
}

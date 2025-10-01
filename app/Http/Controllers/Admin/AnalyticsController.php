<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\Report;
use App\Models\Message;
use App\Models\Review;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard.
     */
    public function index(): View
    {
        $analytics = $this->getAnalyticsData();
        
        return view('admin.analytics.index', compact('analytics'));
    }

    /**
     * Get comprehensive analytics data.
     */
    private function getAnalyticsData(): array
    {
        $now = Carbon::now();
        $thirtyDaysAgo = $now->copy()->subDays(30);
        $sevenDaysAgo = $now->copy()->subDays(7);

        return [
            // User Statistics
            'total_users' => User::count(),
            'total_renters' => User::where('role', 'renter')->count(),
            'total_landlords' => User::where('role', 'landlord')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'new_users_this_month' => User::where('created_at', '>=', $thirtyDaysAgo)->count(),
            'new_users_this_week' => User::where('created_at', '>=', $sevenDaysAgo)->count(),

            // Property Statistics
            'total_properties' => Property::count(),
            'active_properties' => Property::where('status', 'active')->count(),
            'pending_properties' => Property::where('status', 'pending')->count(),
            'approved_properties_this_month' => Property::where('status', 'active')
                ->where('created_at', '>=', $thirtyDaysAgo)->count(),
            'total_property_views' => Property::sum('views_count'),

            // Average Statistics
            'avg_properties_per_landlord' => $this->getAveragePropertiesPerLandlord(),
            'avg_views_per_property' => $this->getAverageViewsPerProperty(),
            'avg_properties_viewed_per_renter' => $this->getAveragePropertiesViewedPerRenter(),

            // Reports and Messages
            'total_reports' => Report::count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'resolved_reports' => Report::where('status', 'resolved')->count(),
            'total_messages' => Message::count(),
            'total_reviews' => Review::count(),
            'total_favorites' => Favorite::count(),

            // Monthly Data for Charts
            'properties_approved_by_month' => $this->getPropertiesApprovedByMonth(),
            'user_registrations_by_month' => $this->getUserRegistrationsByMonth(),
            'property_views_by_month' => $this->getPropertyViewsByMonth(),
        ];
    }

    /**
     * Get average properties per landlord.
     */
    private function getAveragePropertiesPerLandlord(): float
    {
        $landlords = User::where('role', 'landlord')->count();
        if ($landlords === 0) return 0;
        
        $totalProperties = Property::count();
        return round($totalProperties / $landlords, 2);
    }

    /**
     * Get average views per property.
     */
    private function getAverageViewsPerProperty(): float
    {
        $totalProperties = Property::count();
        if ($totalProperties === 0) return 0;
        
        $totalViews = Property::sum('views_count');
        return round($totalViews / $totalProperties, 2);
    }

    /**
     * Get average properties viewed per renter.
     */
    private function getAveragePropertiesViewedPerRenter(): float
    {
        $renters = User::where('role', 'renter')->count();
        if ($renters === 0) return 0;
        
        // This would need a property_views table to track actual views
        // For now, return a placeholder based on favorites
        $totalFavorites = Favorite::count();
        return round($totalFavorites / $renters, 2);
    }

    /**
     * Get properties approved by month for the last 12 months.
     */
    private function getPropertiesApprovedByMonth(): array
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('Y-m');
            $count = Property::where('status', 'active')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $data[] = [
                'month' => $date->format('M Y'),
                'count' => $count
            ];
        }
        return $data;
    }

    /**
     * Get user registrations by month for the last 12 months.
     */
    private function getUserRegistrationsByMonth(): array
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('Y-m');
            $count = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $data[] = [
                'month' => $date->format('M Y'),
                'count' => $count
            ];
        }
        return $data;
    }

    /**
     * Get property views by month for the last 12 months.
     */
    private function getPropertyViewsByMonth(): array
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('Y-m');
            
            // This would need a property_views table to track actual views by date
            // For now, return a placeholder
            $count = Property::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('views_count');
            
            $data[] = [
                'month' => $date->format('M Y'),
                'count' => $count
            ];
        }
        return $data;
    }
}

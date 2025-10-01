<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\MessageReport;
use App\Models\TicketAssignment;
use App\Models\User;
use App\Models\AnalyticsCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportAnalyticsController extends Controller
{
    public function __construct()
    {
        // Middleware is handled at the route level
    }
    
    /**
     * Display the main analytics dashboard.
     */
    public function dashboard()
    {
        $metrics = Cache::remember('analytics_dashboard', 300, function() {
            return [
                'total_reports' => Report::count(),
                'pending_reports' => Report::where('status', 'pending')->count(),
                'resolved_reports' => Report::where('status', 'resolved')->count(),
                'avg_resolution_time' => $this->getAverageResolutionTime(),
                'category_distribution' => $this->getCategoryDistribution(),
                'admin_workload' => $this->getAdminWorkload(),
                'recent_trends' => $this->getRecentTrends(),
                'message_reports' => [
                    'total' => MessageReport::count(),
                    'pending' => MessageReport::where('status', 'pending')->count(),
                    'resolved' => MessageReport::where('status', 'resolved')->count(),
                ],
            ];
        });
        
        return view('admin.analytics.dashboard', compact('metrics'));
    }
    
    /**
     * Get overview analytics.
     */
    public function overview()
    {
        $overview = AnalyticsCache::getCached('analytics_overview') ?? $this->generateOverviewData();
        
        return response()->json($overview);
    }
    
    /**
     * Get report-specific analytics.
     */
    public function reports(Request $request)
    {
        $days = $request->get('days', 30);
        $cacheKey = "analytics_reports_{$days}";
        
        $data = AnalyticsCache::getCached($cacheKey) ?? $this->generateReportAnalytics($days);
        
        return response()->json($data);
    }
    
    /**
     * Get admin performance analytics.
     */
    public function admins()
    {
        $data = AnalyticsCache::getCached('analytics_admins') ?? $this->generateAdminAnalytics();
        
        return response()->json($data);
    }
    
    /**
     * Export analytics data.
     */
    public function export(Request $request)
    {
        $this->middleware('admin.permission:analytics.export');
        
        $format = $request->get('format', 'csv');
        $type = $request->get('type', 'overview');
        
        switch ($type) {
            case 'reports':
                $data = $this->generateReportAnalytics(90);
                break;
            case 'admins':
                $data = $this->generateAdminAnalytics();
                break;
            default:
                $data = $this->generateOverviewData();
        }
        
        return $this->exportData($data, $format, $type);
    }
    
    /**
     * Get average resolution time.
     */
    private function getAverageResolutionTime()
    {
        // SQLite-compatible version
        $reports = Report::whereNotNull('resolved_at')
                        ->select('created_at', 'resolved_at')
                        ->get();
        
        if ($reports->isEmpty()) {
            return 0;
        }
        
        $totalHours = $reports->sum(function ($report) {
            $created = \Carbon\Carbon::parse($report->created_at);
            $resolved = \Carbon\Carbon::parse($report->resolved_at);
            return $created->diffInHours($resolved);
        });
        
        return round($totalHours / $reports->count(), 2);
    }
    
    /**
     * Get category distribution.
     */
    private function getCategoryDistribution()
    {
        return Report::selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->orderBy('count', 'desc')
                    ->get();
    }
    
    /**
     * Get admin workload.
     */
    private function getAdminWorkload()
    {
        return User::whereHas('adminRoles')
                  ->withCount('activeTickets')
                  ->orderBy('active_tickets_count', 'desc')
                  ->get();
    }
    
    /**
     * Get recent trends.
     */
    private function getRecentTrends($days = 30)
    {
        return Report::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays($days))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
    }
    
    /**
     * Generate overview data.
     */
    private function generateOverviewData()
    {
        $data = [
            'total_reports' => Report::count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'resolved_reports' => Report::where('status', 'resolved')->count(),
            'avg_resolution_time' => $this->getAverageResolutionTime(),
            'category_distribution' => $this->getCategoryDistribution(),
            'admin_workload' => $this->getAdminWorkload(),
            'recent_trends' => $this->getRecentTrends(),
            'message_reports' => [
                'total' => MessageReport::count(),
                'pending' => MessageReport::where('status', 'pending')->count(),
                'resolved' => MessageReport::where('status', 'resolved')->count(),
            ],
            'assignment_stats' => [
                'total_assignments' => TicketAssignment::count(),
                'active_assignments' => TicketAssignment::active()->count(),
                'completed_assignments' => TicketAssignment::byStatus('completed')->count(),
            ],
        ];
        
        AnalyticsCache::store('analytics_overview', $data, 60);
        
        return $data;
    }
    
    /**
     * Generate report analytics.
     */
    private function generateReportAnalytics($days)
    {
        $data = [
            'trends' => $this->getRecentTrends($days),
            'category_distribution' => $this->getCategoryDistribution(),
            'status_distribution' => Report::selectRaw('status, COUNT(*) as count')
                                         ->groupBy('status')
                                         ->get(),
            'priority_distribution' => Report::selectRaw('priority, COUNT(*) as count')
                                           ->groupBy('priority')
                                           ->get(),
            'resolution_times' => $this->getResolutionTimesByCategory(),
        ];
        
        AnalyticsCache::store("analytics_reports_{$days}", $data, 60);
        
        return $data;
    }
    
    /**
     * Generate admin analytics.
     */
    private function generateAdminAnalytics()
    {
        $data = [
            'workload_distribution' => User::whereHas('adminRoles')
                                          ->withCount(['activeTickets', 'completedTickets'])
                                          ->with('adminRoles')
                                          ->get(),
            'performance_metrics' => User::whereHas('adminRoles')
                                        ->withCount(['assignedTickets', 'completedTickets'])
                                        ->get()
                                        ->map(function ($admin) {
                                            return [
                                                'admin_id' => $admin->id,
                                                'name' => $admin->name,
                                                'total_tickets' => $admin->assigned_tickets_count,
                                                'completed_tickets' => $admin->completed_tickets_count,
                                                'completion_rate' => $admin->assigned_tickets_count > 0 
                                                    ? round(($admin->completed_tickets_count / $admin->assigned_tickets_count) * 100, 2)
                                                    : 0,
                                            ];
                                        }),
            'assignment_stats' => [
                'total_assignments' => TicketAssignment::count(),
                'active_assignments' => TicketAssignment::active()->count(),
                'completed_assignments' => TicketAssignment::byStatus('completed')->count(),
                'avg_resolution_time' => $this->getAverageAssignmentResolutionTime(),
            ],
        ];
        
        AnalyticsCache::store('analytics_admins', $data, 60);
        
        return $data;
    }
    
    /**
     * Export data in specified format.
     */
    private function exportData($data, $format, $type)
    {
        switch ($format) {
            case 'csv':
                return $this->exportToCsv($data, $type);
            case 'json':
                return response()->json($data);
            default:
                return response()->json($data);
        }
    }
    
    /**
     * Export data to CSV.
     */
    private function exportToCsv($data, $type)
    {
        $filename = "analytics_{$type}_" . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Flatten the data for CSV export
            $this->flattenDataForCsv($file, $data);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Flatten data for CSV export.
     */
    private function flattenDataForCsv($file, $data, $prefix = '')
    {
        foreach ($data as $key => $value) {
            $currentKey = $prefix ? "{$prefix}_{$key}" : $key;
            
            if (is_array($value)) {
                if (isset($value[0]) && is_array($value[0])) {
                    // Array of objects
                    fputcsv($file, [$currentKey]);
                    foreach ($value as $item) {
                        fputcsv($file, $item);
                    }
                } else {
                    // Nested array
                    $this->flattenDataForCsv($file, $value, $currentKey);
                }
            } else {
                fputcsv($file, [$currentKey, $value]);
            }
        }
    }
    
    /**
     * Get resolution times by category (SQLite-compatible).
     */
    private function getResolutionTimesByCategory()
    {
        $reports = Report::whereNotNull('resolved_at')
                        ->select('category', 'created_at', 'resolved_at')
                        ->get();
        
        $categoryTimes = $reports->groupBy('category')->map(function ($categoryReports) {
            $totalHours = $categoryReports->sum(function ($report) {
                $created = \Carbon\Carbon::parse($report->created_at);
                $resolved = \Carbon\Carbon::parse($report->resolved_at);
                return $created->diffInHours($resolved);
            });
            
            return [
                'category' => $categoryReports->first()->category,
                'avg_hours' => round($totalHours / $categoryReports->count(), 2)
            ];
        });
        
        return $categoryTimes->values();
    }
    
    /**
     * Get average assignment resolution time (SQLite-compatible).
     */
    private function getAverageAssignmentResolutionTime()
    {
        $assignments = TicketAssignment::byStatus('completed')
                                    ->select('assigned_at', 'completed_at')
                                    ->get();
        
        if ($assignments->isEmpty()) {
            return 0;
        }
        
        $totalHours = $assignments->sum(function ($assignment) {
            $assigned = \Carbon\Carbon::parse($assignment->assigned_at);
            $completed = \Carbon\Carbon::parse($assignment->completed_at);
            return $assigned->diffInHours($completed);
        });
        
        return round($totalHours / $assignments->count(), 2);
    }
}

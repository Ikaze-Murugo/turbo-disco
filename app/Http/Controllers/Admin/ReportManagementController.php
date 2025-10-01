<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportComment;
use App\Models\ReportNotification;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Property;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ReportManagementController extends Controller
{
    public function __construct()
    {
        // Middleware is handled at the route level
    }

    /**
     * Display the admin reports dashboard.
     */
    public function index(Request $request)
    {
        $query = Report::with(['reporter', 'reportedUser', 'reportedProperty', 'reportedMessage', 'resolvedBy']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('report_type', $request->type);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Sort by priority and creation date
        $reports = $query->orderByRaw("
            CASE priority 
                WHEN 'urgent' THEN 1 
                WHEN 'high' THEN 2 
                WHEN 'medium' THEN 3 
                WHEN 'low' THEN 4 
            END
        ")->orderBy('created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total' => Report::count(),
            'pending' => Report::pending()->count(),
            'investigating' => Report::investigating()->count(),
            'resolved' => Report::resolved()->count(),
            'high_priority' => Report::highPriority()->count(),
        ];

        // Get filter options
        $filterOptions = [
            'statuses' => ['pending', 'investigating', 'resolved', 'dismissed'],
            'types' => ['property', 'user', 'message', 'bug', 'feature_request'],
            'priorities' => ['low', 'medium', 'high', 'urgent'],
            'categories' => ReportCategory::active()->pluck('name')->unique()->toArray(),
        ];

        return view('admin.reports.index', compact('reports', 'stats', 'filterOptions'));
    }

    /**
     * Display the specified report.
     */
    public function show(Report $report)
    {
        $report->load([
            'reporter', 
            'reportedUser', 
            'reportedProperty', 
            'reportedMessage', 
            'resolvedBy',
            'comments.user',
            'statusHistory.changedBy'
        ]);

        // Get related reports
        $relatedReports = Report::where('id', '!=', $report->id)
                               ->where(function($query) use ($report) {
                                   if ($report->reported_user_id) {
                                       $query->where('reported_user_id', $report->reported_user_id);
                                   }
                                   if ($report->reported_property_id) {
                                       $query->where('reported_property_id', $report->reported_property_id);
                                   }
                               })
                               ->with(['reporter', 'reportedUser', 'reportedProperty'])
                               ->orderBy('created_at', 'desc')
                               ->limit(5)
                               ->get();

        return view('admin.reports.show', compact('report', 'relatedReports'));
    }

    /**
     * Update the report status.
     */
    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,investigating,resolved,dismissed',
            'admin_notes' => 'nullable|string|max:2000',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);

        $oldStatus = $report->status;
        
        $report->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'],
            'priority' => $validated['priority'] ?? $report->priority,
            'resolved_by' => $validated['status'] === 'resolved' ? Auth::id() : null,
            'resolved_at' => $validated['status'] === 'resolved' ? now() : null,
        ]);

        // Log the activity
        ActivityLog::log('report_status_updated', Auth::id(), 'report', $report->id, [
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'],
        ]);

        return redirect()->back()->with('success', 'Report status updated successfully!');
    }

    /**
     * Resolve the report with actions.
     */
    public function resolve(Request $request, Report $report)
    {
        $validated = $request->validate([
            'resolution_actions' => 'required|array',
            'resolution_actions.*' => 'required|string',
            'admin_notes' => 'required|string|max:2000',
            'notify_reporter' => 'boolean',
            'notify_reported_user' => 'boolean',
        ]);

        $actions = $validated['resolution_actions'];
        $resolutionActions = [];

        // Process each action
        foreach ($actions as $action) {
            switch ($action) {
                case 'warn_user':
                    $this->warnUser($report->reportedUser);
                    $resolutionActions[] = 'User warned';
                    break;
                case 'suspend_user':
                    $this->suspendUser($report->reportedUser);
                    $resolutionActions[] = 'User suspended';
                    break;
                case 'ban_user':
                    $this->banUser($report->reportedUser);
                    $resolutionActions[] = 'User banned';
                    break;
                case 'hide_property':
                    $this->hideProperty($report->reportedProperty);
                    $resolutionActions[] = 'Property hidden';
                    break;
                case 'delete_property':
                    $this->deleteProperty($report->reportedProperty);
                    $resolutionActions[] = 'Property deleted';
                    break;
                case 'delete_message':
                    $this->deleteMessage($report->reportedMessage);
                    $resolutionActions[] = 'Message deleted';
                    break;
            }
        }

        // Update the report
        $report->update([
            'status' => 'resolved',
            'admin_notes' => $validated['admin_notes'],
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
            'resolution_actions' => $resolutionActions,
        ]);

        // Send notifications
        if ($validated['notify_reporter'] ?? false) {
            $this->notifyReporter($report);
        }

        if ($validated['notify_reported_user'] ?? false && $report->reportedUser) {
            $this->notifyReportedUser($report);
        }

        // Log the activity
        ActivityLog::log('report_resolved', Auth::id(), 'report', $report->id, [
            'resolution_actions' => $resolutionActions,
            'admin_notes' => $validated['admin_notes'],
        ]);

        return redirect()->route('admin.reports.index')
                        ->with('success', 'Report resolved successfully with actions taken!');
    }

    /**
     * Bulk actions on reports.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:mark_investigating,mark_resolved,mark_dismissed,delete',
            'report_ids' => 'required|array',
            'report_ids.*' => 'exists:reports,id',
        ]);

        $reports = Report::whereIn('id', $validated['report_ids'])->get();

        foreach ($reports as $report) {
            switch ($validated['action']) {
                case 'mark_investigating':
                    $report->update(['status' => 'investigating']);
                    break;
                case 'mark_resolved':
                    $report->update([
                        'status' => 'resolved',
                        'resolved_by' => Auth::id(),
                        'resolved_at' => now(),
                    ]);
                    break;
                case 'mark_dismissed':
                    $report->update(['status' => 'dismissed']);
                    break;
                case 'delete':
                    $report->delete();
                    break;
            }

            // Log the activity
            ActivityLog::log('report_bulk_action', Auth::id(), 'report', $report->id, [
                'action' => $validated['action'],
            ]);
        }

        return redirect()->back()->with('success', 'Bulk action completed successfully!');
    }

    // Helper methods for resolution actions
    private function warnUser($user)
    {
        if ($user) {
            // Add warning to user profile or send warning email
            // Implementation depends on your user management system
        }
    }

    private function suspendUser($user)
    {
        if ($user) {
            $user->update(['suspended_until' => now()->addDays(7)]);
        }
    }

    private function banUser($user)
    {
        if ($user) {
            $user->update(['banned_at' => now()]);
        }
    }

    private function hideProperty($property)
    {
        if ($property) {
            $property->update(['status' => 'hidden']);
        }
    }

    private function deleteProperty($property)
    {
        if ($property) {
            $property->delete();
        }
    }

    private function deleteMessage($message)
    {
        if ($message) {
            $message->delete();
        }
    }

    private function notifyReporter($report)
    {
        // Send email notification to reporter
        // Implementation depends on your email system
    }

    private function notifyReportedUser($report)
    {
        // Send email notification to reported user
        // Implementation depends on your email system
    }

    /**
     * Add admin comment to a report
     */
    public function addComment(Request $request, Report $report)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
            'is_internal' => 'boolean',
            'is_admin_response' => 'boolean',
        ]);

        $comment = $report->addComment(
            $validated['comment'],
            $validated['is_internal'] ?? false,
            $validated['is_admin_response'] ?? false
        );

        // If it's an admin response, notify the reporter
        if ($validated['is_admin_response'] ?? false) {
            ReportNotification::createForReporter(
                $report,
                'admin_response',
                'Admin Response',
                "An admin has responded to your report: " . $validated['comment']
            );
        }

        // Log the activity
        ActivityLog::log('admin_comment_added', Auth::id(), 'report', $report->id, [
            'comment_id' => $comment->id,
            'is_internal' => $validated['is_internal'] ?? false,
            'is_admin_response' => $validated['is_admin_response'] ?? false,
        ]);

        return redirect()->back()->with('success', 'Comment added successfully!');
    }

    /**
     * Update report status with enhanced tracking
     */
    public function updateStatus(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,investigating,resolved,dismissed',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'reason' => 'nullable|string|max:1000',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $oldStatus = $report->status;
        $oldPriority = $report->priority;

        // Update the report
        $updateData = ['status' => $validated['status']];
        if (isset($validated['priority'])) {
            $updateData['priority'] = $validated['priority'];
        }
        if (isset($validated['admin_notes'])) {
            $updateData['admin_notes'] = $validated['admin_notes'];
        }
        if ($validated['status'] === 'resolved') {
            $updateData['resolved_by'] = Auth::id();
            $updateData['resolved_at'] = now();
        }

        $report->update($updateData);

        // Record status change in history
        $report->statusHistory()->create([
            'changed_by' => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'old_priority' => $oldPriority,
            'new_priority' => $validated['priority'] ?? $oldPriority,
            'reason' => $validated['reason'],
        ]);

        // Notify reporter of status change
        if ($oldStatus !== $validated['status']) {
            $report->notifyReporterOfStatusChange($validated['status'], $validated['reason']);
        }

        // Log the activity
        ActivityLog::log('report_status_updated', Auth::id(), 'report', $report->id, [
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'old_priority' => $oldPriority,
            'new_priority' => $validated['priority'] ?? $oldPriority,
        ]);

        return redirect()->back()->with('success', 'Report status updated successfully!');
    }


    /**
     * Get report analytics
     */
    public function analytics()
    {
        $stats = [
            'total_reports' => Report::count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'resolved_reports' => Report::where('status', 'resolved')->count(),
            'dismissed_reports' => Report::where('status', 'dismissed')->count(),
            'avg_resolution_time' => $this->getAverageResolutionTime(),
            'reports_by_category' => $this->getReportsByCategory(),
            'reports_by_priority' => $this->getReportsByPriority(),
            'reports_by_status' => $this->getReportsByStatus(),
            'recent_activity' => $this->getRecentActivity(),
        ];

        return view('admin.reports.analytics', compact('stats'));
    }

    private function getAverageResolutionTime()
    {
        $resolvedReports = Report::where('status', 'resolved')
                                ->whereNotNull('resolved_at')
                                ->get();

        if ($resolvedReports->isEmpty()) {
            return 0;
        }

        $totalDays = $resolvedReports->sum(function ($report) {
            return $report->created_at->diffInDays($report->resolved_at);
        });

        return round($totalDays / $resolvedReports->count(), 1);
    }

    private function getReportsByCategory()
    {
        return Report::selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->orderBy('count', 'desc')
                    ->get();
    }

    private function getReportsByPriority()
    {
        return Report::selectRaw('priority, COUNT(*) as count')
                    ->groupBy('priority')
                    ->orderBy('count', 'desc')
                    ->get();
    }

    private function getReportsByStatus()
    {
        return Report::selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->orderBy('count', 'desc')
                    ->get();
    }

    private function getRecentActivity()
    {
        return ActivityLog::where('resource_type', 'report')
                         ->with('user')
                         ->orderBy('created_at', 'desc')
                         ->limit(10)
                         ->get();
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessageReport;
use App\Models\MessageReportComment;
use App\Models\MessageReportNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MessageReportManagementController extends Controller
{
    public function __construct()
    {
        // Middleware is handled at the route level
    }

    /**
     * Display a listing of message reports
     */
    public function index(Request $request)
    {
        $query = MessageReport::with(['sender', 'recipient', 'message']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('sender', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $messageReports = $query->paginate(20);

        // Get filter options
        $statuses = MessageReport::distinct()->pluck('status');
        $priorities = MessageReport::distinct()->pluck('priority');
        $categories = MessageReport::distinct()->pluck('category');
        $admins = User::where('role', 'admin')->get();

        return view('admin.message-reports.index', compact(
            'messageReports', 'statuses', 'priorities', 'categories', 'admins'
        ));
    }

    /**
     * Display the specified message report
     */
    public function show(MessageReport $messageReport)
    {
        $messageReport->load([
            'sender',
            'recipient',
            'message',
            'assignedTo',
            'resolvedBy',
            'comments.user',
            'statusHistory.changedBy',
            'notifications'
        ]);

        // Get related reports
        $relatedReports = MessageReport::where('sender_id', $messageReport->sender_id)
            ->where('id', '!=', $messageReport->id)
            ->with(['message', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.message-reports.show', compact('messageReport', 'relatedReports'));
    }

    /**
     * Add a comment to a message report
     */
    public function addComment(Request $request, MessageReport $messageReport)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:2000',
            'is_internal' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Invalid comment.');
        }

        $isInternal = $request->boolean('is_internal', false);

        $messageReport->addComment(
            $request->comment,
            Auth::id(),
            $isInternal,
            true // is_admin_comment
        );

        // Notify reporter if it's a public comment
        if (!$isInternal) {
            MessageReportNotification::createForReporter(
                $messageReport,
                'admin_comment',
                'Admin Comment Added',
                "An admin has added a comment to your message report: {$request->comment}",
                ['comment' => $request->comment]
            );
        }

        return redirect()->back()
            ->with('success', 'Comment added successfully.');
    }

    /**
     * Update the status of a message report
     */
    public function updateStatus(Request $request, MessageReport $messageReport)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,investigating,resolved,dismissed',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'reason' => 'nullable|string|max:1000',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Invalid status update.');
        }

        $oldStatus = $messageReport->status;
        $newStatus = $request->status;
        $newPriority = $request->priority;
        $reason = $request->reason;
        $assignedTo = $request->assigned_to;

        // Update assigned user if provided
        if ($assignedTo) {
            $messageReport->assigned_to = $assignedTo;
        }

        // Update status and priority
        $messageReport->updateStatus($newStatus, $newPriority, $reason, Auth::id());

        // Set resolved_at if status is resolved
        if ($newStatus === 'resolved') {
            $messageReport->resolved_at = now();
            $messageReport->resolved_by = Auth::id();
            $messageReport->save();
        }

        return redirect()->back()
            ->with('success', 'Message report status updated successfully.');
    }

    /**
     * Resolve a message report
     */
    public function resolve(Request $request, MessageReport $messageReport)
    {
        $validator = Validator::make($request->all(), [
            'resolution_actions' => 'required|array',
            'resolution_actions.*' => 'string|max:500',
            'resolution_notes' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Invalid resolution data.');
        }

        $messageReport->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
            'resolution_actions' => json_encode($request->resolution_actions),
            'resolution_notes' => $request->resolution_notes,
        ]);

        // Record status change
        $messageReport->statusHistory()->create([
            'changed_by' => Auth::id(),
            'old_status' => $messageReport->getOriginal('status'),
            'new_status' => 'resolved',
            'reason' => 'Report resolved by admin',
            'metadata' => [
                'resolution_actions' => $request->resolution_actions,
                'resolution_notes' => $request->resolution_notes,
            ],
        ]);

        // Notify reporter
        MessageReportNotification::createForReporter(
            $messageReport,
            'report_resolved',
            'Message Report Resolved',
            "Your message report has been resolved. Resolution notes: {$request->resolution_notes}",
            [
                'resolution_actions' => $request->resolution_actions,
                'resolution_notes' => $request->resolution_notes,
            ]
        );

        return redirect()->back()
            ->with('success', 'Message report resolved successfully.');
    }

    /**
     * Get analytics for message reports
     */
    public function analytics()
    {
        $stats = [
            'total_reports' => MessageReport::count(),
            'pending_reports' => MessageReport::where('status', 'pending')->count(),
            'investigating_reports' => MessageReport::where('status', 'investigating')->count(),
            'resolved_reports' => MessageReport::where('status', 'resolved')->count(),
            'dismissed_reports' => MessageReport::where('status', 'dismissed')->count(),
            'urgent_reports' => MessageReport::where('priority', 'urgent')->count(),
            'high_priority_reports' => MessageReport::where('priority', 'high')->count(),
        ];

        $reportsByCategory = $this->getReportsByCategory();
        $reportsByPriority = $this->getReportsByPriority();
        $reportsByStatus = $this->getReportsByStatus();
        $recentActivity = $this->getRecentActivity();

        return view('admin.message-reports.analytics', compact(
            'stats', 'reportsByCategory', 'reportsByPriority', 
            'reportsByStatus', 'recentActivity'
        ));
    }

    /**
     * Get reports by category
     */
    private function getReportsByCategory()
    {
        return MessageReport::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Get reports by priority
     */
    private function getReportsByPriority()
    {
        return MessageReport::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Get reports by status
     */
    private function getReportsByStatus()
    {
        return MessageReport::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Get recent activity
     */
    private function getRecentActivity()
    {
        return MessageReport::with(['sender', 'message'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
    }
}
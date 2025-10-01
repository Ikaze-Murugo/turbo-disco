<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportComment;
use App\Models\ReportNotification;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('throttle:5,1')->only('store'); // 5 attempts per minute
    }

    /**
     * Display a listing of the user's reports.
     */
    public function index()
    {
        $reports = Report::where('reporter_id', Auth::id())
                        ->with(['reportedUser', 'reportedProperty', 'reportedMessage', 'resolvedBy'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return view('reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new report.
     */
    public function create(Request $request, $property = null, $user = null, $message = null)
    {
        // Determine report type and resource ID based on route parameters
        if ($property) {
            $reportType = 'property';
            $resourceId = $property;
        } elseif ($user) {
            $reportType = 'user';
            $resourceId = $user;
        } elseif ($message) {
            $reportType = 'message';
            $resourceId = $message;
        } else {
            $reportType = $request->get('type', 'property');
            $resourceId = $request->get('id');
        }
        
        $categories = ReportCategory::active()
                                  ->byType($reportType)
                                  ->ordered()
                                  ->get();

        // Get the resource being reported
        $resource = $this->getResource($reportType, $resourceId);

        return view('reports.create', compact('reportType', 'resourceId', 'categories', 'resource'));
    }

    /**
     * Store a newly created report.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:property,user,message,bug,feature_request',
            'category' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'reported_user_id' => 'nullable|exists:users,id',
            'reported_property_id' => 'nullable|exists:properties,id',
            'reported_message_id' => 'nullable|exists:messages,id',
            'evidence_files' => 'nullable|array|max:5',
            'evidence_files.*' => 'file|mimes:jpeg,png,jpg,gif,pdf|max:2048',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);

        // Determine priority based on category
        $priority = $validated['priority'] ?? $this->determinePriority($validated['category']);

        // Handle evidence file uploads
        $evidenceUrls = [];
        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('reports/evidence', $filename, 'public');
                $evidenceUrls[] = $path;
            }
        }

        // Determine reported_user_id based on report type
        $reportedUserId = $validated['reported_user_id'] ?? null;
        $reportedPropertyId = $validated['reported_property_id'] ?? null;
        $reportedMessageId = $validated['reported_message_id'] ?? null;
        
        if ($validated['report_type'] === 'property' && $reportedPropertyId) {
            $property = \App\Models\Property::find($reportedPropertyId);
            $reportedUserId = $property?->landlord_id;
        } elseif ($validated['report_type'] === 'message' && $reportedMessageId) {
            $message = \App\Models\Message::find($reportedMessageId);
            $reportedUserId = $message?->sender_id;
        }

        // Create the report
        $report = Report::create([
            'reporter_id' => Auth::id(),
            'reported_user_id' => $reportedUserId,
            'reported_property_id' => $reportedPropertyId,
            'reported_message_id' => $reportedMessageId,
            'report_type' => $validated['report_type'],
            'category' => $validated['category'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'evidence_urls' => json_encode($evidenceUrls), // Explicitly convert to JSON
            'priority' => $priority,
            'status' => 'pending',
        ]);

        // Log the activity
        ActivityLog::log('report_submitted', Auth::id(), 'report', $report->id, [
            'report_type' => $report->report_type,
            'category' => $report->category,
        ]);

        return redirect()->route('reports.index')
                        ->with('success', 'Report submitted successfully! We will review it and take appropriate action.');
    }

    /**
     * Display the specified report.
     */
    public function show(Report $report)
    {
        // Users can only view their own reports
        if ($report->reporter_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $report->load([
            'reportedUser', 
            'reportedProperty', 
            'reportedMessage', 
            'resolvedBy',
            'publicComments.user',
            'statusHistory.changedBy',
            'notifications'
        ]);

        return view('reports.show', compact('report'));
    }

    /**
     * Get the resource being reported
     */
    private function getResource($type, $id)
    {
        if (!$id) return null;

        return match($type) {
            'property' => \App\Models\Property::find($id),
            'user' => \App\Models\User::find($id),
            'message' => \App\Models\Message::find($id),
            default => null
        };
    }

    /**
     * Determine priority based on category
     */
    private function determinePriority($category)
    {
        return match($category) {
            'fraud', 'harassment' => 'high',
            'inappropriate_content' => 'medium',
            'spam', 'fake_listing' => 'medium',
            'technical_issue' => 'medium',
            'feature_request' => 'low',
            default => 'medium'
        };
    }

    /**
     * Display user's reports with enhanced tracking
     */
    public function myReports()
    {
        $reports = Report::where('reporter_id', Auth::id())
                        ->with(['reportedUser', 'reportedProperty', 'reportedMessage', 'resolvedBy', 'comments', 'notifications'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        $stats = [
            'total' => $reports->total(),
            'pending' => Report::where('reporter_id', Auth::id())->where('status', 'pending')->count(),
            'investigating' => Report::where('reporter_id', Auth::id())->where('status', 'investigating')->count(),
            'resolved' => Report::where('reporter_id', Auth::id())->where('status', 'resolved')->count(),
            'dismissed' => Report::where('reporter_id', Auth::id())->where('status', 'dismissed')->count(),
        ];

        return view('reports.my-reports', compact('reports', 'stats'));
    }

    /**
     * Add a comment to a report
     */
    public function addComment(Request $request, Report $report)
    {
        // Ensure user can only comment on their own reports
        if ($report->reporter_id !== Auth::id()) {
            abort(403, 'You can only comment on your own reports.');
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        $comment = $report->addComment($validated['comment']);

        // Log the activity
        ActivityLog::log('report_comment_added', Auth::id(), 'report', $report->id, [
            'comment_id' => $comment->id,
        ]);

        return redirect()->back()->with('success', 'Comment added successfully!');
    }

    /**
     * Mark notifications as read
     */
    public function markNotificationsRead(Report $report)
    {
        $report->notifications()
               ->where('user_id', Auth::id())
               ->unread()
               ->update([
                   'is_read' => true,
                   'read_at' => now(),
               ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount()
    {
        $count = ReportNotification::where('user_id', Auth::id())
                                  ->unread()
                                  ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Request follow-up on a report
     */
    public function requestFollowUp(Request $request, Report $report)
    {
        // Ensure user can only request follow-up on their own reports
        if ($report->reporter_id !== Auth::id()) {
            abort(403, 'You can only request follow-up on your own reports.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Add a comment requesting follow-up
        $report->addComment("Follow-up requested: " . $validated['message']);

        // Create notification for admins
        ReportNotification::create([
            'report_id' => $report->id,
            'user_id' => $report->reporter_id,
            'type' => 'follow_up_request',
            'title' => 'Follow-up Requested',
            'message' => 'User has requested follow-up on their report.',
            'metadata' => ['message' => $validated['message']],
        ]);

        // Log the activity
        ActivityLog::log('report_follow_up_requested', Auth::id(), 'report', $report->id, [
            'message' => $validated['message'],
        ]);

        return redirect()->back()->with('success', 'Follow-up request submitted successfully!');
    }
}
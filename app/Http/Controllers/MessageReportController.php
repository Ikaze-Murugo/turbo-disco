<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageReport;
use App\Models\MessageReportComment;
use App\Models\MessageReportNotification;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MessageReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the form for creating a new message report
     */
    public function create(Message $message)
    {
        // Ensure user can report this message
        if (!$this->canReportMessage($message)) {
            abort(403, 'You cannot report this message.');
        }

        $categories = ReportCategory::where('is_active', true)
                                   ->where('report_type', 'message')
                                   ->ordered()
                                   ->get();
        
        return view('message-reports.create', compact('message', 'categories'));
    }

    /**
     * Store a newly created message report
     */
    public function store(Request $request, Message $message)
    {
        // Clean up evidence URLs - remove empty/null values
        $evidenceUrls = $request->input('evidence_urls', []);
        $evidenceUrls = array_filter($evidenceUrls, function($url) {
            return !empty($url) && $url !== null;
        });
        $request->merge(['evidence_urls' => array_values($evidenceUrls)]);

        // Validate request
        $validator = Validator::make($request->all(), [
            'category' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => 'required|url|max:500',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);

        if ($validator->fails()) {
            // Log validation errors for debugging
            \Log::info('Message report validation failed', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all()
            ]);
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Ensure user can report this message
        if (!$this->canReportMessage($message)) {
            \Log::info('User cannot report message', [
                'user_id' => Auth::id(),
                'message_id' => $message->id,
                'message_sender_id' => $message->sender_id,
                'message_recipient_id' => $message->recipient_id
            ]);
            abort(403, 'You cannot report this message.');
        }

        // Check for duplicate reports
        $existingReport = MessageReport::where('message_id', $message->id)
            ->where('sender_id', Auth::id())
            ->where('status', '!=', 'dismissed')
            ->first();

        if ($existingReport) {
            return redirect()->back()
                ->with('error', 'You have already reported this message.');
        }

        try {
            DB::beginTransaction();

            // Create the main report
            $report = Report::create([
                'reporter_id' => Auth::id(),
                'reported_message_id' => $message->id,
                'report_type' => 'message',
                'category' => $request->category,
                'title' => $request->title,
                'description' => $request->description,
                'evidence_urls' => json_encode($request->evidence_urls ?? []),
                'priority' => $request->priority ?? 'medium',
                'status' => 'pending',
            ]);

            // Create the message report
            $messageReport = MessageReport::create([
                'report_id' => $report->id,
                'message_id' => $message->id,
                'conversation_id' => $message->conversation_id ?? null, // Handle null conversation_id
                'message_content' => $message->body,
                'sender_id' => Auth::id(), // The person reporting the message
                'recipient_id' => $message->sender_id, // The person who sent the message being reported
                'report_type' => 'message',
                'category' => $request->category,
                'title' => $request->title,
                'description' => $request->description,
                'evidence_urls' => json_encode($request->evidence_urls ?? []),
                'priority' => $request->priority ?? 'medium',
                'status' => 'pending',
            ]);

            // Log activity
            ActivityLog::log('message_report_submitted', Auth::id(), 'message_report', $messageReport->id, [
                'message_id' => $message->id,
                'category' => $request->category,
                'priority' => $request->priority ?? 'medium',
            ]);

            // Create initial notification
            MessageReportNotification::createForReporter(
                $messageReport,
                'report_submitted',
                'Message Report Submitted',
                'Your message report has been submitted and is under review.',
                ['report_id' => $report->id]
            );

            DB::commit();

            return redirect()->route('message-reports.show', $messageReport)
                ->with('success', 'Message report submitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the actual error for debugging
            \Log::error('Message report submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'message_id' => $message->id
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to submit message report. Please try again. Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified message report
     */
    public function show(MessageReport $messageReport)
    {
        // Users can only view their own reports or admins can view all
        if ($messageReport->sender_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $messageReport->load([
            'message',
            'sender',
            'recipient',
            'assignedTo',
            'resolvedBy',
            'publicComments.user',
            'statusHistory.changedBy',
            'notifications'
        ]);

        return view('message-reports.show', compact('messageReport'));
    }

    /**
     * Display user's message reports
     */
    public function myReports()
    {
        $messageReports = MessageReport::where('sender_id', Auth::id())
            ->with(['message', 'recipient', 'assignedTo', 'resolvedBy', 'comments', 'notifications'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total' => $messageReports->total(),
            'pending' => MessageReport::where('sender_id', Auth::id())->where('status', 'pending')->count(),
            'investigating' => MessageReport::where('sender_id', Auth::id())->where('status', 'investigating')->count(),
            'resolved' => MessageReport::where('sender_id', Auth::id())->where('status', 'resolved')->count(),
            'dismissed' => MessageReport::where('sender_id', Auth::id())->where('status', 'dismissed')->count(),
        ];

        return view('message-reports.my-reports', compact('messageReports', 'stats'));
    }

    /**
     * Add a comment to a message report
     */
    public function addComment(Request $request, MessageReport $messageReport)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Invalid comment.');
        }

        // Users can only comment on their own reports
        if ($messageReport->sender_id !== Auth::id()) {
            abort(403);
        }

        $messageReport->addComment(
            $request->comment,
            Auth::id(),
            false, // is_internal
            false  // is_admin_comment
        );

        // Notify admins of new comment
        $this->notifyAdminsOfNewComment($messageReport, $request->comment);

        return redirect()->back()
            ->with('success', 'Comment added successfully.');
    }

    /**
     * Mark notifications as read
     */
    public function markNotificationsRead(MessageReport $messageReport)
    {
        $messageReport->notifications()
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        $count = MessageReportNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Request follow-up on a message report
     */
    public function requestFollowUp(Request $request, MessageReport $messageReport)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Invalid follow-up message.');
        }

        // Users can only request follow-up on their own reports
        if ($messageReport->sender_id !== Auth::id()) {
            abort(403);
        }

        // Add comment
        $messageReport->addComment(
            "Follow-up requested: " . $request->message,
            Auth::id(),
            false,
            false
        );

        // Update status to pending if it was resolved/dismissed
        if (in_array($messageReport->status, ['resolved', 'dismissed'])) {
            $messageReport->updateStatus('pending', null, 'Follow-up requested by user');
        }

        // Notify admins
        $this->notifyAdminsOfFollowUp($messageReport, $request->message);

        return redirect()->back()
            ->with('success', 'Follow-up request submitted successfully.');
    }

    /**
     * Check if user can report a message
     */
    private function canReportMessage(Message $message): bool
    {
        // User cannot report their own messages
        if ($message->sender_id === Auth::id()) {
            return false;
        }

        // User must be the recipient or have access to the conversation
        // For now, allow any authenticated user to report messages they can see
        // This can be made more restrictive based on your business logic
        return true;
    }

    /**
     * Notify admins of new comment
     */
    private function notifyAdminsOfNewComment(MessageReport $messageReport, string $comment)
    {
        $admins = \App\Models\User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            MessageReportNotification::create([
                'message_report_id' => $messageReport->id,
                'user_id' => $admin->id,
                'type' => 'new_comment',
                'title' => 'New Comment on Message Report',
                'message' => "User {$messageReport->sender->name} added a comment to their message report.",
                'metadata' => [
                    'comment' => $comment,
                    'reporter_id' => $messageReport->sender_id,
                ],
            ]);
        }
    }

    /**
     * Notify admins of follow-up request
     */
    private function notifyAdminsOfFollowUp(MessageReport $messageReport, string $message)
    {
        $admins = \App\Models\User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            MessageReportNotification::create([
                'message_report_id' => $messageReport->id,
                'user_id' => $admin->id,
                'type' => 'follow_up_request',
                'title' => 'Follow-up Requested on Message Report',
                'message' => "User {$messageReport->sender->name} requested a follow-up on their message report.",
                'metadata' => [
                    'follow_up_message' => $message,
                    'reporter_id' => $messageReport->sender_id,
                ],
            ]);
        }
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\MessageReport;
use App\Models\TicketAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class TicketAssignmentController extends Controller
{
    public function __construct()
    {
        // Middleware is handled at the route level
    }
    
    /**
     * Assign a report to an admin.
     */
    public function assignReport(Request $request, Report $report)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'integer|min:1|max:5',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Check if admin has permission to assign
        $assignee = User::find($request->assigned_to);
        if (!$assignee->hasAdminRole('Junior Admin') && !$assignee->hasAdminRole('Senior Admin')) {
            return back()->with('error', 'Can only assign to admin users.');
        }
        
        // Check if report is already assigned
        if ($report->ticketAssignments()->active()->exists()) {
            return back()->with('error', 'This report is already assigned to an admin.');
        }
        
        TicketAssignment::create([
            'report_id' => $report->id,
            'assigned_to' => $request->assigned_to,
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
            'priority' => $request->priority ?? 1,
            'notes' => $request->notes,
            'status' => 'assigned',
        ]);
        
        // Update report status
        $report->update(['status' => 'investigating']);
        
        return back()->with('success', 'Report assigned successfully.');
    }
    
    /**
     * Auto-assign a report to the admin with least workload.
     */
    public function autoAssignReport(Report $report)
    {
        // Find admin with least workload
        $admin = User::whereHas('adminRoles')
                    ->withCount('activeTickets')
                    ->orderBy('active_tickets_count')
                    ->first();
        
        if (!$admin) {
            return back()->with('error', 'No available admins found.');
        }
        
        // Check if report is already assigned
        if ($report->ticketAssignments()->active()->exists()) {
            return back()->with('error', 'This report is already assigned to an admin.');
        }
        
        TicketAssignment::create([
            'report_id' => $report->id,
            'assigned_to' => $admin->id,
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
            'priority' => $report->priority === 'high' ? 5 : 3,
            'status' => 'assigned',
        ]);
        
        $report->update(['status' => 'investigating']);
        
        return back()->with('success', "Report auto-assigned to {$admin->name}.");
    }
    
    /**
     * Assign a message report to an admin.
     */
    public function assignMessageReport(Request $request, MessageReport $messageReport)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'integer|min:1|max:5',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Check if admin has permission to assign
        $assignee = User::find($request->assigned_to);
        if (!$assignee->hasAdminRole('Junior Admin') && !$assignee->hasAdminRole('Senior Admin')) {
            return back()->with('error', 'Can only assign to admin users.');
        }
        
        // Check if message report is already assigned
        if ($messageReport->assigned_to) {
            return back()->with('error', 'This message report is already assigned to an admin.');
        }
        
        $messageReport->update([
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority ?? 1,
            'status' => 'investigating',
        ]);
        
        return back()->with('success', 'Message report assigned successfully.');
    }
    
    /**
     * Reassign a ticket to another admin.
     */
    public function reassign(Request $request, TicketAssignment $assignment)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $newAssignee = User::find($request->assigned_to);
        if (!$newAssignee->hasAdminRole('Junior Admin') && !$newAssignee->hasAdminRole('Senior Admin')) {
            return back()->with('error', 'Can only reassign to admin users.');
        }
        
        $assignment->reassign(
            $request->assigned_to,
            auth()->id(),
            $request->notes
        );
        
        return back()->with('success', 'Ticket reassigned successfully.');
    }
    
    /**
     * Mark a ticket as completed.
     */
    public function complete(TicketAssignment $assignment)
    {
        $assignment->markCompleted();
        
        // Update the associated report status
        if ($assignment->report) {
            $assignment->report->update(['status' => 'resolved']);
        }
        
        return back()->with('success', 'Ticket marked as completed.');
    }
    
    /**
     * Get workload distribution for all admins.
     */
    public function workloadDistribution()
    {
        $admins = User::whereHas('adminRoles')
                     ->withCount(['activeTickets', 'completedTickets'])
                     ->with('adminRoles')
                     ->get()
                     ->map(function ($admin) {
                         return [
                             'id' => $admin->id,
                             'name' => $admin->name,
                             'active_tickets' => $admin->active_tickets_count,
                             'completed_tickets' => $admin->completed_tickets_count,
                             'roles' => $admin->adminRoles->pluck('name'),
                         ];
                     });
        
        return response()->json($admins);
    }
    
    /**
     * Get assignment statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_assignments' => TicketAssignment::count(),
            'active_assignments' => TicketAssignment::active()->count(),
            'completed_assignments' => TicketAssignment::byStatus('completed')->count(),
            'avg_resolution_time' => $this->getAverageResolutionTime(),
            'assignments_by_priority' => TicketAssignment::selectRaw('priority, COUNT(*) as count')
                                                        ->groupBy('priority')
                                                        ->get(),
        ];
        
        return response()->json($stats);
    }
    
    /**
     * Get average resolution time (SQLite-compatible).
     */
    private function getAverageResolutionTime()
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $campaigns = EmailCampaign::with(['template', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.email.campaigns.index', compact('campaigns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $templates = EmailTemplate::where('is_active', true)->get();
        return view('admin.email.campaigns.create', compact('templates'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'template_id' => 'nullable|exists:email_templates,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'target_audience' => 'required|in:all,landlords,renters,admin,custom',
            'target_criteria' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = $request->has('scheduled_at') ? 'scheduled' : 'draft';
        $validated['target_criteria'] = $request->input('target_criteria', []);

        $campaign = EmailCampaign::create($validated);

        // Calculate recipient count
        $recipientCount = $this->calculateRecipientCount($validated);
        $campaign->update(['total_recipients' => $recipientCount]);

        return redirect()->route('admin.email.campaigns.show', $campaign)
            ->with('success', 'Campaign created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailCampaign $emailCampaign)
    {
        $emailCampaign->load(['template', 'creator', 'recipients']);
        $stats = $emailCampaign->getStats();
        
        return view('admin.email.campaigns.show', compact('emailCampaign', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailCampaign $emailCampaign)
    {
        if ($emailCampaign->status !== 'draft') {
            return redirect()->route('admin.email.campaigns.show', $emailCampaign)
                ->with('error', 'Only draft campaigns can be edited.');
        }

        $templates = EmailTemplate::where('is_active', true)->get();
        return view('admin.email.campaigns.edit', compact('emailCampaign', 'templates'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailCampaign $emailCampaign)
    {
        if ($emailCampaign->status !== 'draft') {
            return redirect()->route('admin.email.campaigns.show', $emailCampaign)
                ->with('error', 'Only draft campaigns can be edited.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'template_id' => 'nullable|exists:email_templates,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'target_audience' => 'required|in:all,landlords,renters,admin,custom',
            'target_criteria' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $validated['target_criteria'] = $request->input('target_criteria', []);
        $validated['status'] = $request->has('scheduled_at') ? 'scheduled' : 'draft';

        $emailCampaign->update($validated);

        // Recalculate recipient count
        $recipientCount = $this->calculateRecipientCount($validated);
        $emailCampaign->update(['total_recipients' => $recipientCount]);

        return redirect()->route('admin.email.campaigns.show', $emailCampaign)
            ->with('success', 'Campaign updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailCampaign $emailCampaign)
    {
        if ($emailCampaign->status === 'sending') {
            return redirect()->route('admin.email.campaigns.index')
                ->with('error', 'Cannot delete campaign that is currently being sent.');
        }

        $emailCampaign->delete();

        return redirect()->route('admin.email.campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }

    /**
     * Send the campaign
     */
    public function send(EmailCampaign $emailCampaign)
    {
        if (!$emailCampaign->canBeSent()) {
            return back()->with('error', 'Campaign cannot be sent in its current status.');
        }

        // Queue the email sending job
        \App\Jobs\SendBulkEmailJob::dispatch($emailCampaign);

        $emailCampaign->update(['status' => 'sending']);

        return back()->with('success', 'Campaign is being sent.');
    }

    /**
     * Calculate recipient count based on target criteria
     */
    private function calculateRecipientCount(array $criteria): int
    {
        $query = User::query();

        switch ($criteria['target_audience']) {
            case 'landlords':
                $query->where('role', 'landlord');
                break;
            case 'renters':
                $query->where('role', 'renter');
                break;
            case 'admin':
                $query->where('role', 'admin');
                break;
            case 'custom':
                // Apply custom criteria
                if (isset($criteria['target_criteria']['location'])) {
                    $query->whereHas('properties', function($q) use ($criteria) {
                        $q->where('location', $criteria['target_criteria']['location']);
                    });
                }
                break;
        }

        return $query->count();
    }
}
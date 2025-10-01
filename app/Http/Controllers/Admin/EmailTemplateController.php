<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = EmailTemplate::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.email.templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $availableVariables = (new EmailTemplate())->getAvailableVariables();
        return view('admin.email.templates.create', compact('availableVariables'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:announcement,promotional,system,newsletter',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['variables'] = $request->input('variables', []);
        $validated['is_active'] = $request->has('is_active');

        EmailTemplate::create($validated);

        return redirect()->route('admin.email.templates.index')
            ->with('success', 'Email template created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailTemplate $emailTemplate)
    {
        $emailTemplate->load('creator');
        return view('admin.email.templates.show', compact('emailTemplate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        $availableVariables = $emailTemplate->getAvailableVariables();
        return view('admin.email.templates.edit', compact('emailTemplate', 'availableVariables'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:announcement,promotional,system,newsletter',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['variables'] = $request->input('variables', []);
        $validated['is_active'] = $request->has('is_active');

        $emailTemplate->update($validated);

        return redirect()->route('admin.email.templates.index')
            ->with('success', 'Email template updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        // Check if template is being used in campaigns
        if ($emailTemplate->campaigns()->count() > 0) {
            return redirect()->route('admin.email.templates.index')
                ->with('error', 'Cannot delete template that is being used in campaigns.');
        }

        $emailTemplate->delete();

        return redirect()->route('admin.email.templates.index')
            ->with('success', 'Email template deleted successfully.');
    }
}
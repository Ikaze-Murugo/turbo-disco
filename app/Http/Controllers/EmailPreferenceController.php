<?php

namespace App\Http\Controllers;

use App\Models\UserEmailPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailPreferenceController extends Controller
{
    /**
     * Display the user's email preferences
     */
    public function index()
    {
        $preferences = Auth::user()->emailPreferences ?? UserEmailPreference::createDefaults(Auth::user());
        $frequencyOptions = UserEmailPreference::getFrequencyOptions();
        
        return view('user.email-preferences', compact('preferences', 'frequencyOptions'));
    }

    /**
     * Update the user's email preferences
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'receive_announcements' => 'boolean',
            'receive_promotions' => 'boolean',
            'receive_system_emails' => 'boolean',
            'receive_newsletters' => 'boolean',
            'receive_property_updates' => 'boolean',
            'receive_message_notifications' => 'boolean',
            'frequency' => 'required|in:immediate,daily,weekly,monthly',
        ]);

        Auth::user()->emailPreferences()->updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        return back()->with('success', 'Email preferences updated successfully.');
    }

    /**
     * Unsubscribe user from all emails
     */
    public function unsubscribe($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        
        $user->emailPreferences()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'receive_announcements' => false,
                'receive_promotions' => false,
                'receive_system_emails' => false,
                'receive_newsletters' => false,
                'receive_property_updates' => false,
                'receive_message_notifications' => false,
                'frequency' => 'immediate',
            ]
        );

        return view('emails.unsubscribed', compact('user'));
    }
}
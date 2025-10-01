<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class EmailVerificationController extends Controller
{
    /**
     * Show the email verification notice
     */
    public function notice()
    {
        if (Auth::user()->isEmailVerified()) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-email');
    }

    /**
     * Send a new email verification notification
     */
    public function send(Request $request)
    {
        $user = Auth::user();

        if ($user->isEmailVerified()) {
            return redirect()->route('dashboard');
        }

        // Generate new verification token
        $token = $user->generateVerificationToken();
        
        // Create verification URL
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            ['id' => $user->id, 'token' => $token]
        );

        // Send verification email
        Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationUrl));

        return back()->with('status', 'verification-link-sent');
    }

    /**
     * Verify the user's email address
     */
    public function verify(Request $request, $id, $token)
    {
        $user = User::findOrFail($id);

        // Check if user is already verified
        if ($user->isEmailVerified()) {
            return redirect()->route('dashboard')->with('status', 'already-verified');
        }

        // Check if the token is valid
        if (!$user->isVerificationTokenValid($token)) {
            return redirect()->route('verification.notice')->with('error', 'Invalid or expired verification link.');
        }

        // Verify the user
        $user->verifyEmail();

        // Log the user in if they're not already logged in
        if (!Auth::check()) {
            Auth::login($user);
        }

        return redirect()->route('dashboard')->with('status', 'verified');
    }

    /**
     * Resend verification email
     */
    public function resend(Request $request)
    {
        $user = Auth::user();

        if ($user->isEmailVerified()) {
            return redirect()->route('dashboard');
        }

        // Generate new verification token
        $token = $user->generateVerificationToken();
        
        // Create verification URL
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            ['id' => $user->id, 'token' => $token]
        );

        // Send verification email
        Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationUrl));

        return back()->with('status', 'verification-link-sent');
    }
}

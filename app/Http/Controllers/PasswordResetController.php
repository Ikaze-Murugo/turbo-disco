<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    /**
     * Show the password reset request form
     */
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset email
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'We could not find a user with that email address.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate reset token
        $token = Str::random(64);
        
        // Store token in database with expiration
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Create reset URL
        $resetUrl = URL::temporarySignedRoute(
            'password.reset',
            now()->addHour(),
            ['token' => $token, 'email' => $user->email]
        );

        // Send reset email
        Mail::to($user->email)->send(new PasswordResetMail($user, $resetUrl));

        return back()->with('status', 'We have emailed your password reset link!');
    }

    /**
     * Show the password reset form
     */
    public function showResetForm(Request $request, $token, $email)
    {
        // Verify the signed URL
        if (!URL::hasValidSignature($request)) {
            return redirect()->route('password.request')->with('error', 'Invalid or expired reset link.');
        }

        // Verify token exists and is not expired
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('created_at', '>', now()->subHour())
            ->first();

        if (!$resetRecord || !Hash::check($token, $resetRecord->token)) {
            return redirect()->route('password.request')->with('error', 'Invalid or expired reset link.');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Reset the user's password
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Verify the signed URL
        if (!URL::hasValidSignature($request)) {
            return redirect()->route('password.request')->with('error', 'Invalid or expired reset link.');
        }

        // Verify token exists and is not expired
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('created_at', '>', now()->subHour())
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return redirect()->route('password.request')->with('error', 'Invalid or expired reset link.');
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete the reset token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Your password has been reset successfully!');
    }
}

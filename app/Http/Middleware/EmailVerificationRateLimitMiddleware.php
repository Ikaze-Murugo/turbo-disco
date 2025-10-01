<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EmailVerificationRateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = auth()->id();
        $email = auth()->user()?->email ?? $request->input('email');
        
        if (!$userId && !$email) {
            return $next($request);
        }
        
        // User-based rate limiting (5 resend requests per hour per user)
        if ($userId) {
            $userKey = 'email_verification_attempts_user:' . $userId;
            if (RateLimiter::tooManyAttempts($userKey, 5)) {
                $this->logSecurityEvent('EMAIL_VERIFICATION_USER_RATE_LIMIT_EXCEEDED', $request);
                return $this->buildRateLimitResponse($userKey, 'user account');
            }
        }
        
        // Email-based rate limiting (10 requests per hour per email)
        if ($email) {
            $emailKey = 'email_verification_attempts_email:' . strtolower($email);
            if (RateLimiter::tooManyAttempts($emailKey, 10)) {
                $this->logSecurityEvent('EMAIL_VERIFICATION_EMAIL_RATE_LIMIT_EXCEEDED', $request);
                return $this->buildRateLimitResponse($emailKey, 'email address');
            }
        }
        
        // IP-based rate limiting (15 requests per hour per IP)
        $ipKey = 'email_verification_attempts_ip:' . $request->ip();
        if (RateLimiter::tooManyAttempts($ipKey, 15)) {
            $this->logSecurityEvent('EMAIL_VERIFICATION_IP_RATE_LIMIT_EXCEEDED', $request);
            return $this->buildRateLimitResponse($ipKey, 'IP address');
        }
        
        return $next($request);
    }
    
    /**
     * Build rate limit response.
     */
    protected function buildRateLimitResponse(string $key, string $type): Response
    {
        $seconds = RateLimiter::availableIn($key);
        $minutes = ceil($seconds / 60);
        
        $message = "Too many email verification requests for this {$type}. Please try again in {$minutes} minutes.";
        
        if (request()->expectsJson()) {
            return response()->json([
                'message' => $message,
                'retry_after' => $seconds,
                'rate_limit_type' => $type
            ], 429);
        }
        
        return redirect()->back()
            ->withErrors(['email' => $message]);
    }
    
    /**
     * Log security events for monitoring.
     */
    protected function logSecurityEvent(string $event, Request $request): void
    {
        Log::warning('Security Event: ' . $event, [
            'ip' => $request->ip(),
            'user_id' => auth()->id(),
            'email' => auth()->user()?->email ?? $request->input('email'),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
            'url' => $request->fullUrl(),
            'method' => $request->method()
        ]);
    }
}

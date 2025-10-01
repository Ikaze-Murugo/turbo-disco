<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetRateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->input('email');
        
        if (!$email) {
            return $next($request);
        }
        
        // Email-based rate limiting (3 requests per hour per email)
        $emailKey = 'password_reset_attempts_email:' . strtolower($email);
        if (RateLimiter::tooManyAttempts($emailKey, 3)) {
            $this->logSecurityEvent('PASSWORD_RESET_EMAIL_RATE_LIMIT_EXCEEDED', $request);
            return $this->buildRateLimitResponse($emailKey, 'email address');
        }
        
        // IP-based rate limiting (5 requests per hour per IP)
        $ipKey = 'password_reset_attempts_ip:' . $request->ip();
        if (RateLimiter::tooManyAttempts($ipKey, 5)) {
            $this->logSecurityEvent('PASSWORD_RESET_IP_RATE_LIMIT_EXCEEDED', $request);
            return $this->buildRateLimitResponse($ipKey, 'IP address');
        }
        
        // Combined rate limiting (2 requests per hour per combination)
        $combinedKey = 'password_reset_attempts_combined:' . md5(strtolower($email) . '|' . $request->ip());
        if (RateLimiter::tooManyAttempts($combinedKey, 2)) {
            $this->logSecurityEvent('PASSWORD_RESET_COMBINED_RATE_LIMIT_EXCEEDED', $request);
            return $this->buildRateLimitResponse($combinedKey, 'account');
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
        
        $message = "Too many password reset requests for this {$type}. Please try again in {$minutes} minutes.";
        
        if (request()->expectsJson()) {
            return response()->json([
                'message' => $message,
                'retry_after' => $seconds,
                'rate_limit_type' => $type
            ], 429);
        }
        
        return redirect()->back()
            ->withInput(request()->only('email'))
            ->withErrors(['email' => $message]);
    }
    
    /**
     * Log security events for monitoring.
     */
    protected function logSecurityEvent(string $event, Request $request): void
    {
        Log::warning('Security Event: ' . $event, [
            'ip' => $request->ip(),
            'email' => $request->input('email'),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
            'url' => $request->fullUrl(),
            'method' => $request->method()
        ]);
    }
}

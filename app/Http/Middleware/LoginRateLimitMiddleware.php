<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class LoginRateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);
        
        // Check IP-based rate limiting (stricter)
        $ipKey = 'login_attempts_ip:' . $request->ip();
        if (RateLimiter::tooManyAttempts($ipKey, 10)) {
            $this->logSecurityEvent('IP_RATE_LIMIT_EXCEEDED', $request);
            return $this->buildRateLimitResponse($ipKey, 'IP');
        }
        
        // Check email-based rate limiting
        if ($request->has('email')) {
            $emailKey = 'login_attempts_email:' . strtolower($request->input('email'));
            if (RateLimiter::tooManyAttempts($emailKey, 5)) {
                $this->logSecurityEvent('EMAIL_RATE_LIMIT_EXCEEDED', $request);
                return $this->buildRateLimitResponse($emailKey, 'Email');
            }
        }
        
        // Check combined key rate limiting (most restrictive)
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->logSecurityEvent('COMBINED_RATE_LIMIT_EXCEEDED', $request);
            return $this->buildRateLimitResponse($key, 'Combined');
        }
        
        return $next($request);
    }
    
    /**
     * Resolve the request signature for rate limiting.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $email = $request->input('email', 'unknown');
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        
        return 'login_attempts_combined:' . md5(strtolower($email) . '|' . $ip . '|' . substr($userAgent, 0, 50));
    }
    
    /**
     * Build rate limit response.
     */
    protected function buildRateLimitResponse(string $key, string $type): Response
    {
        $seconds = RateLimiter::availableIn($key);
        $minutes = ceil($seconds / 60);
        
        $message = match($type) {
            'IP' => "Too many login attempts from this IP address. Please try again in {$minutes} minutes.",
            'Email' => "Too many login attempts for this email address. Please try again in {$minutes} minutes.",
            'Combined' => "Too many login attempts. Please try again in {$minutes} minutes.",
            default => "Rate limit exceeded. Please try again in {$minutes} minutes."
        };
        
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

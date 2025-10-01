<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PropertyManagementRateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return $next($request);
        }
        
        $routeName = $request->route()?->getName();
        
        // Different limits based on the action
        switch ($routeName) {
            case 'properties.store':
                // Property creation: 10 properties per day per landlord
                $key = 'property_creation_attempts_user:' . $userId;
                $limit = 10;
                $decay = 86400; // 24 hours
                $action = 'create properties';
                break;
                
            case 'properties.update':
                // Property updates: 50 updates per day per landlord
                $key = 'property_update_attempts_user:' . $userId;
                $limit = 50;
                $decay = 86400; // 24 hours
                $action = 'update properties';
                break;
                
            default:
                // General property management: 100 actions per day per landlord
                $key = 'property_management_attempts_user:' . $userId;
                $limit = 100;
                $decay = 86400; // 24 hours
                $action = 'manage properties';
                break;
        }
        
        if (RateLimiter::tooManyAttempts($key, $limit)) {
            $this->logSecurityEvent('PROPERTY_MANAGEMENT_RATE_LIMIT_EXCEEDED', $request, $action);
            return $this->buildRateLimitResponse($key, $action);
        }
        
        // IP-based rate limiting for property management (20 actions per hour per IP)
        $ipKey = 'property_management_attempts_ip:' . $request->ip();
        if (RateLimiter::tooManyAttempts($ipKey, 20)) {
            $this->logSecurityEvent('PROPERTY_MANAGEMENT_IP_RATE_LIMIT_EXCEEDED', $request, $action);
            return $this->buildRateLimitResponse($ipKey, 'property management from this IP');
        }
        
        return $next($request);
    }
    
    /**
     * Build rate limit response.
     */
    protected function buildRateLimitResponse(string $key, string $action): Response
    {
        $seconds = RateLimiter::availableIn($key);
        $hours = ceil($seconds / 3600);
        
        $message = "Too many attempts to {$action}. Please try again in {$hours} hours.";
        
        if (request()->expectsJson()) {
            return response()->json([
                'message' => $message,
                'retry_after' => $seconds,
                'action' => $action
            ], 429);
        }
        
        return redirect()->back()
            ->withErrors(['error' => $message]);
    }
    
    /**
     * Log security events for monitoring.
     */
    protected function logSecurityEvent(string $event, Request $request, string $action): void
    {
        Log::warning('Security Event: ' . $event, [
            'ip' => $request->ip(),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'action' => $action,
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
            'url' => $request->fullUrl(),
            'method' => $request->method()
        ]);
    }
}

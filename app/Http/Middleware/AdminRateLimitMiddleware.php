<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminRateLimitMiddleware
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
        
        // Stricter limits for admin actions
        switch ($routeName) {
            case 'admin.login':
                // Admin login: 3 attempts per 15 minutes per IP
                $key = 'admin_login_attempts_ip:' . $request->ip();
                $limit = 3;
                $decay = 900; // 15 minutes
                $action = 'admin login';
                break;
                
            case 'admin.properties.approve':
            case 'admin.properties.reject':
                // Property approval/rejection: 50 actions per hour per admin
                $key = 'admin_property_actions_user:' . $userId;
                $limit = 50;
                $decay = 3600; // 1 hour
                $action = 'approve/reject properties';
                break;
                
            case 'admin.reviews.approve':
            case 'admin.reviews.reject':
                // Review approval/rejection: 100 actions per hour per admin
                $key = 'admin_review_actions_user:' . $userId;
                $limit = 100;
                $decay = 3600; // 1 hour
                $action = 'approve/reject reviews';
                break;
                
            case 'admin.users.suspend':
            case 'admin.users.activate':
                // User management: 10 actions per hour per admin
                $key = 'admin_user_actions_user:' . $userId;
                $limit = 10;
                $decay = 3600; // 1 hour
                $action = 'manage users';
                break;
                
            default:
                // General admin actions: 100 actions per hour per admin
                $key = 'admin_actions_user:' . $userId;
                $limit = 100;
                $decay = 3600; // 1 hour
                $action = 'perform admin actions';
                break;
        }
        
        if (RateLimiter::tooManyAttempts($key, $limit)) {
            $this->logSecurityEvent('ADMIN_RATE_LIMIT_EXCEEDED', $request, $action);
            return $this->buildRateLimitResponse($key, $action);
        }
        
        // IP-based rate limiting for admin actions (stricter)
        $ipKey = 'admin_actions_ip:' . $request->ip();
        if (RateLimiter::tooManyAttempts($ipKey, 50)) {
            $this->logSecurityEvent('ADMIN_IP_RATE_LIMIT_EXCEEDED', $request, $action);
            return $this->buildRateLimitResponse($ipKey, 'admin actions from this IP');
        }
        
        return $next($request);
    }
    
    /**
     * Build rate limit response.
     */
    protected function buildRateLimitResponse(string $key, string $action): Response
    {
        $seconds = RateLimiter::availableIn($key);
        $minutes = ceil($seconds / 60);
        
        $message = "Too many attempts to {$action}. Please try again in {$minutes} minutes.";
        
        if (request()->expectsJson()) {
            return response()->json([
                'message' => $message,
                'retry_after' => $seconds,
                'action' => $action,
                'admin_action' => true
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
        Log::critical('Admin Security Event: ' . $event, [
            'ip' => $request->ip(),
            'admin_user_id' => auth()->id(),
            'admin_email' => auth()->user()?->email,
            'action' => $action,
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'admin_action' => true
        ]);
    }
}

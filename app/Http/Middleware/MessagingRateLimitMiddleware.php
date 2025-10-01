<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class MessagingRateLimitMiddleware
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
        
        // Different limits based on the messaging action
        switch ($routeName) {
            case 'messages.store':
                // Message sending: 50 messages per hour per user
                $key = 'message_sending_attempts_user:' . $userId;
                $limit = 50;
                $decay = 3600; // 1 hour
                $action = 'send messages';
                break;
                
            case 'messages.reply':
                // Message replies: 100 replies per hour per user
                $key = 'message_reply_attempts_user:' . $userId;
                $limit = 100;
                $decay = 3600; // 1 hour
                $action = 'reply to messages';
                break;
                
            default:
                // General messaging: 75 actions per hour per user
                $key = 'messaging_attempts_user:' . $userId;
                $limit = 75;
                $decay = 3600; // 1 hour
                $action = 'use messaging features';
                break;
        }
        
        if (RateLimiter::tooManyAttempts($key, $limit)) {
            $this->logSecurityEvent('MESSAGING_RATE_LIMIT_EXCEEDED', $request, $action);
            return $this->buildRateLimitResponse($key, $action);
        }
        
        // IP-based rate limiting for messaging (100 actions per hour per IP)
        $ipKey = 'messaging_attempts_ip:' . $request->ip();
        if (RateLimiter::tooManyAttempts($ipKey, 100)) {
            $this->logSecurityEvent('MESSAGING_IP_RATE_LIMIT_EXCEEDED', $request, $action);
            return $this->buildRateLimitResponse($ipKey, 'messaging from this IP');
        }
        
        // Spam prevention: Check for rapid successive messages to same recipient
        $recipientId = $request->input('recipient_id') ?? $request->input('landlord_id') ?? $request->input('renter_id');
        if ($recipientId) {
            $spamKey = 'messaging_spam_prevention:' . $userId . ':' . $recipientId;
            if (RateLimiter::tooManyAttempts($spamKey, 10)) {
                $this->logSecurityEvent('MESSAGING_SPAM_PREVENTION_TRIGGERED', $request, $action);
                return $this->buildRateLimitResponse($spamKey, 'send messages to this recipient');
            }
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
            'recipient_id' => $request->input('recipient_id') ?? $request->input('landlord_id') ?? $request->input('renter_id'),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
            'url' => $request->fullUrl(),
            'method' => $request->method()
        ]);
    }
}

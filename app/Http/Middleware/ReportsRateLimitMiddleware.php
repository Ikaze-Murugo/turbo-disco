<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportsRateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow 5 reports per hour per user
        $key = 'reports:' . auth()->id();
        $maxAttempts = 5;
        $decayMinutes = 60;

        if (cache()->has($key)) {
            $attempts = cache()->get($key);
            if ($attempts >= $maxAttempts) {
                return response()->json([
                    'message' => 'Too many reports submitted. Please try again later.',
                    'retry_after' => cache()->get($key . ':retry_after', $decayMinutes * 60)
                ], 429);
            }
        }

        // Increment attempts
        $attempts = cache()->get($key, 0) + 1;
        cache()->put($key, $attempts, now()->addMinutes($decayMinutes));
        cache()->put($key . ':retry_after', $decayMinutes * 60, now()->addMinutes($decayMinutes));

        return $next($request);
    }
}
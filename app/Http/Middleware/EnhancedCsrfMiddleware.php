<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnhancedCsrfMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip CSRF for API routes and certain safe methods
        if ($this->shouldSkipCsrf($request)) {
            return $next($request);
        }

        // Validate CSRF token
        if (!$this->validateCsrfToken($request)) {
            $this->logCsrfViolation($request);
            return $this->handleCsrfViolation($request);
        }

        // Validate request origin
        if (!$this->validateOrigin($request)) {
            $this->logOriginViolation($request);
            return $this->handleOriginViolation($request);
        }

        // Validate referer header
        if (!$this->validateReferer($request)) {
            $this->logRefererViolation($request);
            return $this->handleRefererViolation($request);
        }

        // Regenerate CSRF token for security
        $this->regenerateCsrfToken($request);

        return $next($request);
    }

    /**
     * Check if CSRF validation should be skipped.
     */
    private function shouldSkipCsrf(Request $request): bool
    {
        // Skip for safe methods
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            return true;
        }

        // Skip for API routes
        if ($request->is('api/*')) {
            return true;
        }

        // Skip for certain file upload endpoints (handled separately)
        if ($request->is('storage/*') || $request->is('images/*')) {
            return true;
        }

        return false;
    }

    /**
     * Validate CSRF token.
     */
    private function validateCsrfToken(Request $request): bool
    {
        $token = $request->input('_token') ?? $request->header('X-CSRF-TOKEN');
        
        if (!$token) {
            return false;
        }

        $sessionToken = $request->session()->token();
        
        // Ensure session token exists
        if (!$sessionToken) {
            return false;
        }
        
        return hash_equals($sessionToken, $token);
    }

    /**
     * Validate request origin.
     */
    private function validateOrigin(Request $request): bool
    {
        $origin = $request->header('Origin');
        $host = $request->getHost();
        
        if (!$origin) {
            return true; // Some browsers don't send Origin header
        }

        // Parse origin URL
        $parsedOrigin = parse_url($origin);
        if (!$parsedOrigin || !isset($parsedOrigin['host'])) {
            return false;
        }

        // Check if origin matches the request host
        return $parsedOrigin['host'] === $host;
    }

    /**
     * Validate referer header.
     */
    private function validateReferer(Request $request): bool
    {
        $referer = $request->header('Referer');
        $host = $request->getHost();
        
        if (!$referer) {
            return true; // Some browsers don't send Referer header
        }

        // Parse referer URL
        $parsedReferer = parse_url($referer);
        if (!$parsedReferer || !isset($parsedReferer['host'])) {
            return false;
        }

        // Check if referer matches the request host
        return $parsedReferer['host'] === $host;
    }

    /**
     * Regenerate CSRF token for enhanced security.
     */
    private function regenerateCsrfToken(Request $request): void
    {
        // Only regenerate for POST requests to prevent issues with GET requests
        if ($request->isMethod('POST')) {
            $request->session()->regenerateToken();
        }
    }

    /**
     * Handle CSRF token violation.
     */
    private function handleCsrfViolation(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'CSRF token mismatch. Please refresh the page and try again.',
                'error' => 'csrf_token_mismatch'
            ], 419);
        }

        return redirect()->back()
            ->withInput($request->except('_token'))
            ->withErrors(['error' => 'CSRF token mismatch. Please refresh the page and try again.']);
    }

    /**
     * Handle origin violation.
     */
    private function handleOriginViolation(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Invalid request origin.',
                'error' => 'invalid_origin'
            ], 403);
        }

        return redirect()->back()
            ->withErrors(['error' => 'Invalid request origin.']);
    }

    /**
     * Handle referer violation.
     */
    private function handleRefererViolation(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Invalid request referer.',
                'error' => 'invalid_referer'
            ], 403);
        }

        return redirect()->back()
            ->withErrors(['error' => 'Invalid request referer.']);
    }

    /**
     * Log CSRF violation.
     */
    private function logCsrfViolation(Request $request): void
    {
        Log::warning('CSRF Token Violation', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'token_provided' => $request->input('_token') ? 'yes' : 'no',
            'session_token' => $request->session()->token(),
            'timestamp' => now()
        ]);
    }

    /**
     * Log origin violation.
     */
    private function logOriginViolation(Request $request): void
    {
        Log::warning('Origin Header Violation', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'origin' => $request->header('Origin'),
            'host' => $request->getHost(),
            'timestamp' => now()
        ]);
    }

    /**
     * Log referer violation.
     */
    private function logRefererViolation(Request $request): void
    {
        Log::warning('Referer Header Violation', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'referer' => $request->header('Referer'),
            'host' => $request->getHost(),
            'timestamp' => now()
        ]);
    }
}

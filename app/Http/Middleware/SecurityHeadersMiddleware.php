<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Ensure response is a Response object
        if (!$response instanceof \Symfony\Component\HttpFoundation\Response) {
            $response = new \Illuminate\Http\Response($response);
        }

        // Content Security Policy (CSP)
        $response->headers->set('Content-Security-Policy', $this->getCSPHeader());

        // X-Frame-Options - Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');

        // X-Content-Type-Options - Prevent MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-XSS-Protection - Enable XSS filtering
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer-Policy - Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Strict-Transport-Security - Force HTTPS (only in production)
        if (app()->environment('production') && $request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Permissions-Policy - Control browser features
        $response->headers->set('Permissions-Policy', $this->getPermissionsPolicyHeader());

        // Cross-Origin-Embedder-Policy - Control cross-origin embedding
        $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');

        // Cross-Origin-Opener-Policy - Control cross-origin window access
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        // Cross-Origin-Resource-Policy - Control cross-origin resource access
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        // Cache-Control for sensitive pages
        if ($this->isSensitivePage($request)) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }

    /**
     * Get Content Security Policy header.
     */
    private function getCSPHeader(): string
    {
        // Development-friendly CSP that allows necessary resources
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
            "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com data:",
            "img-src 'self' data: https: blob:",
            "media-src 'self' data: https: blob:",
            "connect-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'"
        ];

        // Only add strict policies in production
        if (app()->environment('production')) {
            $csp[] = "upgrade-insecure-requests";
            $csp[] = "block-all-mixed-content";
        }

        return implode('; ', $csp);
    }

    /**
     * Get Permissions Policy header.
     */
    private function getPermissionsPolicyHeader(): string
    {
        $permissions = [
            'camera=()',
            'microphone=()',
            'geolocation=()',
            'payment=()',
            'usb=()',
            'magnetometer=()',
            'gyroscope=()',
            'accelerometer=()',
            'ambient-light-sensor=()',
            'autoplay=()',
            'battery=()',
            'bluetooth=()',
            'clipboard-read=()',
            'clipboard-write=()',
            'display-capture=()',
            'fullscreen=(self)',
            'gamepad=()',
            'hid=()',
            'idle-detection=()',
            'local-fonts=()',
            'midi=()',
            'nfc=()',
            'notifications=()',
            'persistent-storage=()',
            'picture-in-picture=()',
            'publickey-credentials-get=()',
            'screen-wake-lock=()',
            'serial=()',
            'speaker-selection=()',
            'storage-access=()',
            'sync-xhr=()',
            'unoptimized-images=()',
            'usb=()',
            'web-share=()',
            'xr-spatial-tracking=()'
        ];

        return implode(', ', $permissions);
    }

    /**
     * Check if the current page is sensitive and should not be cached.
     */
    private function isSensitivePage(Request $request): bool
    {
        $sensitiveRoutes = [
            'login',
            'register',
            'password.request',
            'password.reset',
            'verification.notice',
            'verification.send',
            'admin.dashboard',
            'admin.users',
            'admin.pending-properties',
            'admin.pending-reviews',
            'profile.edit',
            'messages.create',
            'messages.store',
            'properties.create',
            'properties.store',
            'properties.edit',
            'properties.update'
        ];

        $routeName = $request->route()?->getName();
        
        return in_array($routeName, $sensitiveRoutes) || 
               $request->is('admin/*') || 
               $request->is('profile*') ||
               $request->is('messages*') ||
               $request->is('properties/create') ||
               $request->is('properties/*/edit');
    }
}

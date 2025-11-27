<?php

namespace App\Http\Middleware;

use App\Models\UserEvent;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackUserEventsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Track page views and basic user behavior for ML fraud detection.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track GET requests (page views)
        if ($request->isMethod('GET') && !$this->shouldSkipTracking($request)) {
            $this->trackPageView($request);
        }

        return $response;
    }

    /**
     * Track a page view event.
     */
    private function trackPageView(Request $request): void
    {
        try {
            UserEvent::create([
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'event_type' => 'page_view',
                'page_url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referrer' => $request->header('referer'),
                'device_type' => $this->getDeviceType($request),
                'browser' => $this->getBrowser($request),
                'os' => $this->getOS($request),
            ]);
        } catch (\Exception $e) {
            // Silently fail to not disrupt user experience
            logger()->error('Failed to track user event: ' . $e->getMessage());
        }
    }

    /**
     * Determine if tracking should be skipped for this request.
     */
    private function shouldSkipTracking(Request $request): bool
    {
        // Skip tracking for:
        // - Admin routes (too much noise)
        // - API routes
        // - Asset requests
        // - AJAX requests (tracked separately via JS)
        
        $path = $request->path();
        
        return $request->ajax()
            || Str::startsWith($path, ['admin/', 'api/', '_debugbar'])
            || Str::contains($path, ['.js', '.css', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.woff', '.ttf']);
    }

    /**
     * Get device type from user agent.
     */
    private function getDeviceType(Request $request): string
    {
        $userAgent = strtolower($request->userAgent() ?? '');
        
        if (Str::contains($userAgent, ['mobile', 'android', 'iphone', 'ipod'])) {
            return 'mobile';
        }
        
        if (Str::contains($userAgent, ['tablet', 'ipad'])) {
            return 'tablet';
        }
        
        return 'desktop';
    }

    /**
     * Get browser from user agent.
     */
    private function getBrowser(Request $request): ?string
    {
        $userAgent = strtolower($request->userAgent() ?? '');
        
        if (Str::contains($userAgent, 'edge')) return 'Edge';
        if (Str::contains($userAgent, 'chrome')) return 'Chrome';
        if (Str::contains($userAgent, 'safari')) return 'Safari';
        if (Str::contains($userAgent, 'firefox')) return 'Firefox';
        if (Str::contains($userAgent, 'opera')) return 'Opera';
        if (Str::contains($userAgent, 'msie') || Str::contains($userAgent, 'trident')) return 'IE';
        
        return null;
    }

    /**
     * Get operating system from user agent.
     */
    private function getOS(Request $request): ?string
    {
        $userAgent = strtolower($request->userAgent() ?? '');
        
        if (Str::contains($userAgent, 'windows')) return 'Windows';
        if (Str::contains($userAgent, 'mac')) return 'MacOS';
        if (Str::contains($userAgent, 'linux')) return 'Linux';
        if (Str::contains($userAgent, 'android')) return 'Android';
        if (Str::contains($userAgent, ['iphone', 'ipad'])) return 'iOS';
        
        return null;
    }
}

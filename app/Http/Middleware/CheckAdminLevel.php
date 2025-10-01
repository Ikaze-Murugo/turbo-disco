<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $minLevel): Response
    {
        $user = Auth::user();
        
        if (!$user || !$user->hasAdminLevel($minLevel)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Insufficient admin level',
                    'message' => "You need admin level {$minLevel} or higher to access this resource"
                ], 403);
            }
            
            abort(403, "You do not have sufficient admin privileges. Required level: {$minLevel}");
        }
        
        return $next($request);
    }
}

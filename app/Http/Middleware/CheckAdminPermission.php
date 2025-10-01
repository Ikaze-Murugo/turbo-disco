<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::user();
        
        if (!$user || !$user->hasAdminPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Insufficient permissions',
                    'message' => "You do not have permission to perform this action: {$permission}"
                ], 403);
            }
            
            abort(403, "You do not have permission to access this resource. Required permission: {$permission}");
        }
        
        return $next($request);
    }
}

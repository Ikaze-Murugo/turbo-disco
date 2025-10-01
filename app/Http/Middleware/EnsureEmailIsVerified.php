<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|null
     */
    public function handle(Request $request, Closure $next, $redirectToRoute = null)
    {
        // For now, just allow all authenticated users to pass through
        // TODO: Implement proper email verification check
        if (! $request->user()) {
            return $request->expectsJson()
                    ? abort(401, 'Unauthenticated.')
                    : Redirect::guest(URL::route('login'));
        }

        return $next($request);
    }
}

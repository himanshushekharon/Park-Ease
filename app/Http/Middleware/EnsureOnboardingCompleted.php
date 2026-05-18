<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->onboarding_completed) {
            // Check if the current route is NOT the onboarding route to prevent redirect loops
            if (!$request->is('onboarding') && !$request->is('api/onboarding*') && !$request->is('owner/kyc') && !$request->is('api/owner/kyc*') && !$request->is('logout') && !$request->is('api/logout')) {
                return redirect('/onboarding');
            }
        }

        return $next($request);
    }
}

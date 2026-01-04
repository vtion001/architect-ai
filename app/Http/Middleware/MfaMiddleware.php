<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MfaMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->mfa_enabled && !session('mfa_verified')) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'MFA challenge required', 'mfa_required' => true], 403);
            }
            return redirect()->route('mfa.challenge');
        }

        return $next($request);
    }
}
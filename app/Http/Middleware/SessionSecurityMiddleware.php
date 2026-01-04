<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class SessionSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $userType = $this->getUserType($user);
        $config = config("iam.sessions.$userType");

        if (!$config) {
            return $next($request);
        }

        // 1. Check Max Duration
        $sessionStartedAt = session('session_started_at');
        if (!$sessionStartedAt) {
            session(['session_started_at' => now()->timestamp]);
        } else {
            $maxDuration = $config['max_duration'] * 60; // to seconds
            if (now()->timestamp - $sessionStartedAt > $maxDuration) {
                return $this->logout($request, 'Session expired (max duration exceeded).');
            }
        }

        // 2. Check Inactivity
        $lastActivity = session('last_activity_at');
        if ($lastActivity) {
            $inactivityTimeout = $config['inactivity_timeout'] * 60; // to seconds
            if (now()->timestamp - $lastActivity > $inactivityTimeout) {
                return $this->logout($request, 'Session expired due to inactivity.');
            }
        }

        // 3. Update Last Activity
        session(['last_activity_at' => now()->timestamp]);

        return $next($request);
    }

    protected function getUserType($user): string
    {
        if ($user->is_developer) return 'developer';
        
        $role = $user->roles()->first()?->name;
        
        return match ($role) {
            'Agency Owner' => 'agency_owner',
            'Agency Admin' => 'agency_admin',
            default => 'sub_account_user',
        };
    }

    protected function logout(Request $request, string $message)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json(['error' => $message], 401);
        }

        return redirect()->route('login')->with('warning', $message);
    }
}
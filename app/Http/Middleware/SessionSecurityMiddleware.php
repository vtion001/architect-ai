<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class SessionSecurityMiddleware
{
    public function __construct(protected \App\Services\AuthorizationService $authService) {}

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

        // 1. Identity Baseline (Guardian)
        // Establish baseline if not set
        if (!session()->has('identity_baseline_ip')) {
            session([
                'identity_baseline_ip' => $request->ip(),
                'identity_baseline_ua' => $request->userAgent(),
            ]);
        } else {
            // Check for Identity Drift (Suspicious shift in IP or Browser)
            $driftDetected = session('identity_baseline_ip') !== $request->ip() || 
                             session('identity_baseline_ua') !== $request->userAgent();

            if ($driftDetected && !app()->isLocal()) {
                $this->authService->audit(
                    $user, 
                    'security.identity_drift', 
                    null, 
                    'denied', 
                    "Suspicious identity shift. Expected: " . session('identity_baseline_ip') . " | Found: " . $request->ip()
                );

                return $this->logout($request, 'Identity drift detected. Session terminated for security.');
            }
        }

        if (!$config) {
            return $next($request);
        }

        // 2. Check Max Duration
        $sessionStartedAt = session('session_started_at');
        if (!$sessionStartedAt) {
            session(['session_started_at' => now()->timestamp]);
        } else {
            $maxDuration = $config['max_duration'] * 60; // to seconds
            if (now()->timestamp - $sessionStartedAt > $maxDuration) {
                return $this->logout($request, 'Session protocol finalized (max duration exceeded).');
            }
        }

        // 3. Check Inactivity
        $lastActivity = session('last_activity_at');
        if ($lastActivity) {
            $inactivityTimeout = $config['inactivity_timeout'] * 60; // to seconds
            if (now()->timestamp - $lastActivity > $inactivityTimeout) {
                return $this->logout($request, 'Session suspended due to inactivity.');
            }
        }

        // 4. Update Registry
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
<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function __construct(protected \App\Services\AuthorizationService $authService) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = null;
        $user = auth()->user();

        // 1. Prioritize Session Hot-Swap Context
        if ($user && session()->has('current_tenant_id')) {
            $sessionId = session('current_tenant_id');
            $potentialTenant = Tenant::withoutGlobalScope('tenant')->find($sessionId);

            if ($potentialTenant) {
                // Verify the user is authorized for this SPECIFIC node
                $isAuthorized = $user->tenant_id === $potentialTenant->id || 
                                $user->is_developer || 
                                ($user->tenant->type === 'agency' && $potentialTenant->parent_id === $user->tenant_id);

                if ($isAuthorized) {
                    $tenant = $potentialTenant;
                } else {
                    // Security Violation: Revoke session context
                    session()->forget('current_tenant_id');
                    $this->authService->audit($user, 'security.session_revoked', $potentialTenant, 'denied', "Illegal session context detected. Access revoked.");
                }
            }
        }

        // 2. Fallback to Domain, Slug, or native tenant if no hot-swap is active
        if (!$tenant) {
            $host = $request->getHost();
            $slug = $request->route('tenant_slug') ?? $request->header('X-Tenant-Slug');

            if ($host && !in_array($host, [config('app.url'), 'localhost', '127.0.0.1'])) {
                $tenant = Tenant::where('metadata->custom_domain', $host)->first();
            }

            if (!$tenant && $slug) {
                $tenant = Tenant::where('slug', $slug)->first();
            }

            if (!$tenant && $user) {
                $tenant = $user->tenant;
            }
        }

        if (!$tenant && $this->requiresTenant($request)) {
            return response()->json(['error' => 'Tenant context required'], 400);
        }

        if ($tenant) {
            // 3. Final Isolation Verification
            if ($user && !$user->is_developer && $user->tenant_id !== $tenant->id && $tenant->parent_id !== $user->tenant_id) {
                $this->authService->audit($user, 'tenant.isolation_violation', $tenant, 'denied', "Unauthorized access attempt to: {$tenant->slug}");
                return response()->json(['error' => 'Unauthorized for this workspace'], 403);
            }

            app()->instance(Tenant::class, $tenant);
            session(['current_tenant_id' => $tenant->id]);
        }

        return $next($request);
    }

    protected function requiresTenant(Request $request): bool
    {
        // Add logic to exclude global routes (like agency registration)
        return ! $request->is('api/auth/register-agency');
    }
}
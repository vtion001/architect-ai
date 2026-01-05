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
        // 1. Identify Tenant from Domain, Slug, or Header
        $host = $request->getHost();
        $slug = $request->route('tenant_slug') ?? $request->header('X-Tenant-Slug');

        if ($host && !in_array($host, [config('app.url'), 'localhost', '127.0.0.1'])) {
            // Attempt to find tenant by custom domain in metadata
            $tenant = Tenant::where('metadata->custom_domain', $host)->first();
        }

        if (!isset($tenant) && !$slug && auth()->check()) {
            // Fallback to user's tenant if authenticated
            $tenant = auth()->user()->tenant;
        } elseif (!isset($tenant) && $slug) {
            $tenant = Tenant::where('slug', $slug)->first();
        } elseif (!isset($tenant)) {
            $tenant = null;
        }

        if (!$tenant && $this->requiresTenant($request)) {
            return response()->json(['error' => 'Tenant context required'], 400);
        }

        if ($tenant) {
            // 2. Validate user belongs to tenant (if logged in)
            if (auth()->check() && !auth()->user()->is_developer && auth()->user()->tenant_id !== $tenant->id) {
                // Log cross-tenant attempt
                $this->authService->audit(
                    auth()->user(),
                    'tenant.isolation_violation',
                    $tenant,
                    'denied',
                    "User attempted to access unauthorized tenant: {$tenant->slug}"
                );

                return response()->json(['error' => 'Unauthorized for this workspace'], 403);
            }

            // 3. Set global tenant context for the request
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
<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Identify Tenant from Slug (either in route or header)
        $slug = $request->route('tenant_slug') ?? $request->header('X-Tenant-Slug');

        if (!$slug && auth()->check()) {
            // Fallback to user's tenant if authenticated
            $tenant = auth()->user()->tenant;
        } elseif ($slug) {
            $tenant = Tenant::where('slug', $slug)->first();
        } else {
            $tenant = null;
        }

        if (!$tenant && $this->requiresTenant($request)) {
            return response()->json(['error' => 'Tenant context required'], 400);
        }

        if ($tenant) {
            // 2. Validate user belongs to tenant (if logged in)
            if (auth()->check() && !auth()->user()->is_developer && auth()->user()->tenant_id !== $tenant->id) {
                return response()->json(['error' => 'Unauthorized for this workspace'], 403);
            }

            // 3. Set global tenant context for the request
            app()->instance(Tenant::class, $tenant);
        }

        return $next($request);
    }

    protected function requiresTenant(Request $request): bool
    {
        // Add logic to exclude global routes (like agency registration)
        return ! $request->is('api/auth/register-agency');
    }
}
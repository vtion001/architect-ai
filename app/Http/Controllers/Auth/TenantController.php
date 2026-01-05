<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantController extends Controller
{
    public function __construct(protected AuthorizationService $authService) {}

    /**
     * Seamlessly switch the user's active workspace context.
     */
    public function switch(Request $request, Tenant $tenant)
    {
        $user = Auth::user();

        // 1. Verify Authorization
        // A user can switch to a tenant if:
        // - It is their native tenant
        // - They are a developer (Break-Glass)
        // - They are an Agency Owner and the target is a sub-account of their native tenant
        $isAuthorized = $user->tenant_id === $tenant->id || 
                        $user->is_developer || 
                        ($user->tenant->type === 'agency' && $tenant->parent_id === $user->tenant_id);

        if (!$isAuthorized) {
            $this->authService->audit($user, 'tenant.switch_denied', $tenant, 'denied', "Unauthorized context switch attempt to: {$tenant->slug}");
            abort(403, 'Unauthorized workspace switch.');
        }

        // 2. Execute Context Swap
        session(['current_tenant_id' => $tenant->id]);

        $this->authService->audit($user, 'tenant.switched', $tenant, 'success', "Identity transitioned to workspace: {$tenant->name}");

        return redirect()->route('dashboard')->with('success', "Workspace context transitioned to: {$tenant->name}");
    }
}
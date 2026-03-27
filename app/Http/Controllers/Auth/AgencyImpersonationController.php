<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgencyImpersonationController extends Controller
{
    public function __construct(protected AuthorizationService $authService) {}

    /**
     * Allow Agency Owners to impersonate sub-account admins.
     */
    public function impersonate(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'reason' => 'required|string|min:10',
        ]);

        $owner = Auth::user();
        $targetTenant = Tenant::withoutGlobalScope('tenant')->findOrFail($request->tenant_id);

        // 1. Verify Authorization
        // Must be an agency owner AND the target must be their child tenant
        $isAuthorized = $owner->tenant->type === 'agency' && $targetTenant->parent_id === $owner->tenant_id;

        if (! $isAuthorized) {
            $this->authService->audit($owner, 'security.illegal_impersonation', $targetTenant, 'denied', "Illegal attempt to impersonate tenant node: {$targetTenant->slug}");
            abort(403, 'Unauthorized context entry.');
        }

        // 2. Locate the primary admin of the target sub-account
        // In this system, every sub-account has at least one user (the person who was invited)
        $targetUser = User::withoutGlobalScope('tenant')
            ->where('tenant_id', $targetTenant->id)
            ->first();

        if (! $targetUser) {
            abort(404, 'No authorized identity found in target node.');
        }

        // 3. Audit Protocol
        $this->authService->audit(
            $owner,
            'agency.impersonate',
            $targetTenant,
            'success',
            "Agency Owner initiated session entry. Goal: {$request->reason}"
        );

        // 4. Identity Swap
        session(['impersonated_by' => $owner->id]);
        Auth::login($targetUser);

        return response()->json([
            'message' => "Session active for {$targetTenant->name}",
            'redirect' => '/dashboard',
        ]);
    }

    /**
     * Stop impersonation and return to original owner identity.
     */
    public function stop()
    {
        if (! session()->has('impersonated_by')) {
            return redirect('/dashboard');
        }

        $ownerId = session('impersonated_by');
        $owner = User::withoutGlobalScope('tenant')->findOrFail($ownerId);

        Auth::login($owner);
        session()->forget('impersonated_by');

        return redirect('/dashboard')->with('success', 'Master identity restored.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TokenTransaction;
use App\Models\Waitlist;
use App\Services\TokenService;
use Illuminate\Http\Request;

class TenantExplorerController extends Controller
{
    public function __construct(protected TokenService $tokenService) {}

    public function index()
    {
        // Use withoutGlobalScope if necessary, but Developer can toggle it via session
        $tenants = Tenant::withCount('users')->whereNull('parent_id')->get(); // Agencies first
        
        $tenants->map(function($t) {
            $t->token_balance = $this->tokenService->getBalance($t);
            return $t;
        });

        return view('admin.tenants.tenants', compact('tenants'));
    }

    public function show(Tenant $tenant)
    {
        $tenant->load('users', 'subAccounts.users');
        $tokenBalance = $this->tokenService->getBalance($tenant);
        $transactions = TokenTransaction::where('tenant_id', $tenant->id)->latest()->take(50)->get();
        
        // Find associated waitlist lead (if any)
        $linkedWaitlist = Waitlist::whereIn('email', $tenant->users->pluck('email'))->first();

        return view('admin.tenants.show', compact('tenant', 'tokenBalance', 'transactions', 'linkedWaitlist'));
    }

    /**
     * Manually grant tokens to a tenant.
     */
    public function grantTokens(Request $request, Tenant $tenant)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'reason' => 'required|string|min:5',
        ]);

        $this->tokenService->grant($tenant, (int)$request->amount, $request->reason);

        return response()->json([
            'success' => true,
            'message' => "Successfully allocated {$request->amount} tokens to {$tenant->name}."
        ]);
    }
}
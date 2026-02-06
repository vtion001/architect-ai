<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use App\Services\TokenService;
use App\Services\AuthorizationService;
use App\Services\FeatureCreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SubAccountController extends Controller
{
    public function __construct(
        protected TokenService $tokenService,
        protected AuthorizationService $authService,
        protected FeatureCreditService $featureCreditService
    ) {}

    /**
     * List all sub-accounts for the current agency.
     */
    public function index()
    {
        $agency = app(Tenant::class);
        
        if ($agency->type !== 'agency') {
            abort(403, 'Only agencies can manage sub-accounts.');
        }

        $subAccounts = $agency->subAccounts()->withCount('users')->get();
        
        $subAccounts->map(function($sub) {
            $sub->token_balance = $this->tokenService->getBalance($sub);
            return $sub;
        });

        $capacity = [
            'current' => $subAccounts->count(),
            'max' => $agency->getMaxSubAccounts(),
            'label' => strtoupper($agency->plan ?? 'starter') . ' NODE',
            'can_create' => $agency->canCreateSubAccounts(),
        ];

        return view('tenant.sub-accounts.sub-accounts', compact('subAccounts', 'capacity'));
    }

    /**
     * Create a new Sub-Account (Nested Tenant).
     */
    public function store(Request $request)
    {
        $agency = app(Tenant::class);

        // 1. Plan Access Check - Only Agency plan can create sub-accounts
        if (!$agency->canCreateSubAccounts()) {
            $this->authService->audit(
                auth()->user(),
                'security.feature_access_denied',
                null,
                'denied',
                "Attempted to create sub-account without Agency plan."
            );

            return response()->json([
                'success' => false,
                'error' => 'feature_locked',
                'message' => 'Sub-accounts require the Agency plan. Please upgrade to continue.',
                'upgrade_url' => route('billing.upgrade'),
            ], 403);
        }

        // 2. Quota Enforcement Protocol
        $maxNodes = $agency->getMaxSubAccounts();
        $currentNodes = $agency->subAccounts()->count();

        if ($currentNodes >= $maxNodes) {
            $this->authService->audit(
                auth()->user(),
                'security.quota_breach_attempt',
                null,
                'denied',
                "Attempted to provision node beyond plan limit ({$maxNodes})."
            );

            return response()->json([
                'success' => false,
                'message' => "Grid Capacity reached. Your Agency node is limited to {$maxNodes} nested workspaces. Please contact support to scale."
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|min:3',
            'slug' => 'required|string|unique:tenants,slug',
            'admin_email' => 'required|email|unique:users,email',
        ]);

        return DB::transaction(function () use ($request, $agency) {
            // 1. Create Sub-Account (inherit plan from parent agency)
            $subAccount = Tenant::create([
                'type' => 'sub_account',
                'parent_id' => $agency->id,
                'plan' => $agency->plan, // Inherit plan from parent
                'name' => $request->name,
                'slug' => Str::slug($request->slug),
                'status' => 'active',
            ]);

            // 2. Create Sub-Account Admin
            $user = User::create([
                'tenant_id' => $subAccount->id,
                'email' => $request->admin_email,
                'password' => Hash::make(Str::random(16)),
                'status' => 'active',
            ]);

            // 3. Assign Sub-Account Admin Role
            $role = Role::where('name', 'Sub-Account Admin')->first();
            if ($role) {
                $user->roles()->attach($role->id, ['scope_type' => 'tenant']);
            }

            // 4. Provision Initial Resources (500 tokens)
            $this->tokenService->grant($subAccount, 500, 'initial_provisioning');

            // 5. Provision Feature Credits for new user
            $this->featureCreditService->provisionCreditsForUser($user);

            $this->authService->audit(
                auth()->user(),
                'subaccount.created',
                $subAccount,
                'success',
                "Provisioned nested workspace: {$subAccount->name}"
            );

            return response()->json([
                'message' => 'Sub-account and identity provisioned successfully.',
                'sub_account' => $subAccount
            ], 201);
        });
    }
}
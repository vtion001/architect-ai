<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use App\Services\TokenService;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SubAccountController extends Controller
{
    public function __construct(
        protected TokenService $tokenService,
        protected AuthorizationService $authService
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

        return view('tenant.sub-accounts.index', compact('subAccounts'));
    }

    /**
     * Create a new Sub-Account (Nested Tenant).
     */
    public function store(Request $request)
    {
        $agency = app(Tenant::class);

        $request->validate([
            'name' => 'required|string|min:3',
            'slug' => 'required|string|unique:tenants,slug',
            'admin_email' => 'required|email|unique:users,email',
        ]);

        return DB::transaction(function () use ($request, $agency) {
            // 1. Create Sub-Account
            $subAccount = Tenant::create([
                'type' => 'sub_account',
                'parent_id' => $agency->id,
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
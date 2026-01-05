<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle tenant-aware login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'slug' => 'required|exists:tenants,slug', // Tenant slug required context
        ]);

        $tenant = Tenant::where('slug', $request->slug)->firstOrFail();

        // 1. Check credentials scoped to this tenant
        $user = User::withoutGlobalScope('tenant')
            ->where('email', $request->email)
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials for this workspace.'],
            ]);
        }

        // 2. Check status
        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['Account is ' . $user->status],
            ]);
        }

        // 3. Login
        Auth::login($user, $request->boolean('remember'));
        session(['current_tenant_id' => $user->tenant_id]); // Store for scoping
        $user->update(['last_login_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Login successful',
                'token' => $user->createToken('auth_token')->plainTextToken,
                'user' => $user->load('roles'),
                'tenant' => $tenant
            ]);
        }

        return redirect()->intended('/dashboard');
    }

    /**
     * Register a new Agency (Tenant + Owner).
     */
    public function registerAgency(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|min:3',
            'slug' => 'required|string|min:3|unique:tenants,slug',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:12', // NIST minimum
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            // 1. Create Tenant
            $tenant = Tenant::create([
                'type' => 'agency',
                'name' => $request->company_name,
                'slug' => Str::slug($request->slug),
                'status' => 'active',
            ]);

            // 2. Create Owner
            $user = User::create([
                'tenant_id' => $tenant->id,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 'active',
                'mfa_enabled' => true, // Force MFA for owners
            ]);

            // 3. Assign Role
            $role = Role::where('name', 'Agency Owner')->first();
            if ($role) {
                $user->roles()->attach($role->id, ['scope_type' => 'tenant']);
            }

            // 4. Initial Token Grant (Provisioning)
            app(\App\Services\TokenService::class)->grant($tenant, 1000, 'initial_provisioning');

            \Illuminate\Support\Facades\Log::info("NEW AGENCY PROVISIONED: {$tenant->name} ({$tenant->slug}) by {$user->email}");

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Agency registered successfully',
                    'tenant_id' => $tenant->id,
                    'login_url' => url("/auth/login/{$tenant->slug}"),
                ], 201);
            }

            return redirect()->route('login', ['tenant_slug' => $tenant->slug])
                ->with('success', 'Workspace provisioned. Please sign in to establish your identity.');
        });
    }
}
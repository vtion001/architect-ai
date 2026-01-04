<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TokenService;

class SettingsController extends Controller
{
    public function __construct(protected TokenService $tokenService) {}

    /**
     * Display the settings index.
     */
    public function index(Request $request)
    {
        $tenant = app(Tenant::class);
        $user = Auth::user();
        $activeTab = $request->get('tab', 'profile');

        // Fetch recent audit logs for this tenant
        $auditLogs = \App\Models\AuditLog::with('actor')
            ->where('tenant_id', $tenant->id)
            ->latest()
            ->take(10)
            ->get();

        $tokenBalance = $this->tokenService->getBalance($tenant);

        return view('tenant.settings.index', compact('tenant', 'user', 'activeTab', 'auditLogs', 'tokenBalance'));
    }

    /**
     * Update tenant-specific branding/settings.
     */
    public function updateBranding(Request $request)
    {
        $tenant = app(Tenant::class);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'metadata.primary_color' => 'nullable|string',
            'metadata.timezone' => 'nullable|string',
            'metadata.custom_domain' => 'nullable|string|unique:tenants,metadata->custom_domain,' . $tenant->id . ',id',
        ]);

        $tenant->update([
            'name' => $request->name,
            'metadata' => array_merge($tenant->metadata ?? [], $request->metadata),
        ]);

        return back()->with('success', 'Branding updated successfully.');
    }

    /**
     * Update user profile settings.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:12|confirmed',
        ]);

        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Disable MFA for the current user.
     */
    public function disableMfa(Request $request)
    {
        $user = Auth::user();
        
        $user->update([
            'mfa_enabled' => false,
            'mfa_secret' => null,
        ]);

        session()->forget('mfa_verified');

        return back()->with('success', 'MFA has been disabled.');
    }
}
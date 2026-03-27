<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            ->orderBy('timestamp', 'desc')
            ->take(10)
            ->get();

        $tokenBalance = $this->tokenService->getBalance($tenant);
        $apiTokens = $user->tokens;

        return view('tenant.settings.settings', compact('tenant', 'user', 'activeTab', 'auditLogs', 'tokenBalance', 'apiTokens'));
    }

    /**
     * Generate a new industrial API access node.
     */
    public function generateToken(Request $request)
    {
        $request->validate(['token_name' => 'required|string|max:255']);

        $token = auth()->user()->createToken($request->token_name);

        app(\App\Services\AuthorizationService::class)->audit(
            auth()->user(),
            'api.token_generated',
            null,
            'success',
            "Generated new API access node: {$request->token_name}"
        );

        return back()->with('plain_text_token', $token->plainTextToken);
    }

    /**
     * Purge an existing API access node.
     */
    public function revokeToken(Request $request, $tokenId)
    {
        auth()->user()->tokens()->where('id', $tokenId)->delete();

        app(\App\Services\AuthorizationService::class)->audit(
            auth()->user(),
            'api.token_revoked',
            null,
            'success',
            "Purged API access node ID: {$tokenId}"
        );

        return back()->with('success', 'API access node purged successfully.');
    }

    /**
     * Update tenant-specific branding/settings.
     */
    public function updateBranding(Request $request)
    {
        $tenant = app(Tenant::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048', // 2MB max logo
            'metadata.primary_color' => 'nullable|string',
            'metadata.timezone' => 'nullable|string',
            'metadata.custom_domain' => 'nullable|string|unique:tenants,metadata->custom_domain,'.$tenant->id.',id',
        ]);

        $metadata = array_merge($tenant->metadata ?? [], $request->input('metadata', []));

        if ($request->hasFile('logo')) {
            // Re-using the logic from ContentCreator for Cloudinary/Local
            $file = $request->file('logo');
            $cloudName = config('services.cloudinary.cloud_name');
            if ($cloudName) {
                // Cloudinary Upload Protocol
                $timestamp = time();
                $signString = "timestamp=$timestamp".config('services.cloudinary.api_secret');
                $signature = sha1($signString);

                $response = \Illuminate\Support\Facades\Http::attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                    ->post("https://api.cloudinary.com/v1_1/$cloudName/auto/upload", [
                        'api_key' => config('services.cloudinary.api_key'),
                        'timestamp' => $timestamp,
                        'signature' => $signature,
                    ]);

                if ($response->successful()) {
                    $metadata['logo_url'] = $response->json()['secure_url'];
                }
            } else {
                // Local Fallback
                $filename = \Illuminate\Support\Str::random(20).'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/branding'), $filename);
                $metadata['logo_url'] = '/uploads/branding/'.$filename;
            }
        }

        $tenant->update([
            'name' => $request->name,
            'metadata' => $metadata,
        ]);

        return back()->with('success', 'Workspace identity protocol updated.');
    }

    /**
     * Update user profile settings.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email|unique:users,email,'.$user->id,
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Waitlist;
use App\Models\Invitation;
use App\Models\Role;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function __construct(protected AuthorizationService $authService) {}

    /**
     * Convert a waitlist lead into a formal invitation.
     */
    public function convertLead(Request $request, Waitlist $lead)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($lead) {
            // 1. Provision the Tenant
            $tenant = Tenant::create([
                'type' => 'agency',
                'name' => $lead->agency_name ?? ($lead->name . "'s Agency"),
                'slug' => Str::slug($lead->agency_name ?? $lead->name),
                'status' => 'active',
            ]);

            // 2. Locate Agency Owner Role
            $role = Role::where('name', 'Agency Owner')->first();

            // 3. Dispatch Invitation
            $invitation = Invitation::create([
                'tenant_id' => $tenant->id,
                'inviter_id' => auth()->id(),
                'email' => $lead->email,
                'role_id' => $role->id,
                'token' => Str::random(40),
                'expires_at' => now()->addDays(7),
            ]);

            // 4. Update Lead Status
            $lead->update(['status' => 'invited']);

            $this->authService->audit(
                auth()->user(),
                'waitlist.converted',
                $lead,
                'success',
                "Converted waitlist lead into tenant: {$tenant->name}"
            );

            return response()->json([
                'success' => true,
                'message' => 'Lead successfully architected into a tenant. Invitation dispatched.',
                'invitation_url' => url("/auth/join/{$invitation->token}")
            ]);
        });
    }

    public function index()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'total_users' => User::withoutGlobalScope('tenant')->count(),
            'critical_logs' => AuditLog::where('result', 'denied')->count(),
            'waitlist_count' => Waitlist::count(),
            'observability_active' => session('developer_observability_mode', false),
        ];

        $recentLogs = AuditLog::with(['actor', 'tenant'])->orderBy('timestamp', 'desc')->take(10)->get();
        $waitlistLeads = Waitlist::latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentLogs', 'waitlistLeads'));
    }

    public function toggleObservability(Request $request)
    {
        $request->validate([
            'enabled' => 'required|boolean',
            'justification' => 'required|string|min:10',
        ]);

        $enabled = $request->enabled;
        session(['developer_observability_mode' => $enabled]);

        $this->authService->audit(
            auth()->user(),
            $enabled ? 'developer.observability.enabled' : 'developer.observability.disabled',
            null,
            'success',
            $request->justification
        );

        return response()->json([
            'message' => 'Observability mode ' . ($enabled ? 'enabled' : 'disabled'),
            'status' => $enabled
        ]);
    }
}
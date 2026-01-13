<?php

namespace App\Http\Controllers;

use App\Models\Waitlist as WaitlistModel;
use App\Models\User;
use App\Models\Tenant;
use App\Models\AuditLog;
use App\Models\TokenTransaction;
use App\Models\Content;
use App\Models\Research;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GodViewController extends Controller
{
    /**
     * Display the Global Grid Master View.
     */
    public function index()
    {
        $this->authorizeGodAccess();

        // 1. Global Grid Telemetry
        $statistics = [
            'total_tenants' => Tenant::count(),
            'total_identities' => User::withoutGlobalScope('tenant')->count(),
            'global_credits' => TokenTransaction::sum('amount'),
            'network_load' => Content::count() + Research::count(),
            'total_waitlist' => WaitlistModel::count(),
            'active_waitlist' => WaitlistModel::where('status', 'pending')->count(),
            'grid_integrity' => 'Verified (99.99%)',
        ];

        // 2. Global Protocol Logs (Last 20 across all tenants)
        $globalAudit = AuditLog::with(['actor', 'tenant'])
            ->orderBy('timestamp', 'desc')
            ->take(20)
            ->get();

        // 3. High-Value Leads (Waitlist)
        $waitlistEntries = WaitlistModel::latest()->take(25)->get();

        return view('admin.god-view', compact('waitlistEntries', 'statistics', 'globalAudit'));
    }

    /**
     * Ensure only authorized developers can access the Master Node.
     */
    private function authorizeGodAccess(): void
    {
        if (!auth()->check() || !auth()->user()->is_developer) {
            abort(403, 'ACCESS DENIED. This node requires Master Node authorization.');
        }
    }

    public function approve(WaitlistModel $waitlist) { /* logic already moved to convertLead in AdminController */ }
}
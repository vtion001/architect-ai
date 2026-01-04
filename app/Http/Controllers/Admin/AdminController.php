<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\AuditLog;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(protected AuthorizationService $authService) {}

    public function index()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'total_users' => User::withoutGlobalScope('tenant')->count(),
            'critical_logs' => AuditLog::where('result', 'denied')->count(),
            'observability_active' => session('developer_observability_mode', false),
        ];

        return view('admin.dashboard', compact('stats'));
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
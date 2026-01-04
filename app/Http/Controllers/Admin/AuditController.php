<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with(['actor', 'tenant'])->latest();

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        if ($request->filled('actor_type')) {
            $query->where('actor_type', $request->actor_type);
        }

        $logs = $query->paginate(50);

        return view('admin.audit.index', compact('logs'));
    }
}
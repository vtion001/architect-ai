<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantExplorerController extends Controller
{
    public function index()
    {
        // Use withoutGlobalScope if necessary, but Developer can toggle it via session
        $tenants = Tenant::withCount('users')->whereNull('parent_id')->get(); // Agencies first
        return view('admin.tenants.index', compact('tenants'));
    }

    public function show(Tenant $tenant)
    {
        $tenant->load('users', 'subAccounts.users');
        return view('admin.tenants.show', compact('tenant'));
    }
}
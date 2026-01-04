<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AccessPolicy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function index()
    {
        $policies = AccessPolicy::orderBy('priority', 'desc')->get();
        return view('tenant.policies.index', compact('policies'));
    }

    public function create()
    {
        return view('tenant.policies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'effect' => 'required|in:allow,deny',
            'conditions' => 'required|json',
            'priority' => 'required|integer',
        ]);

        AccessPolicy::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $request->name,
            'effect' => $request->effect,
            'conditions' => json_decode($request->conditions, true),
            'priority' => $request->priority,
        ]);

        return redirect()->route('policies.index')->with('success', 'Access policy created successfully.');
    }

    public function destroy(AccessPolicy $policy)
    {
        $policy->delete();
        return back()->with('success', 'Policy removed.');
    }
}
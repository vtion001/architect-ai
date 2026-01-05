<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AccessPolicy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function __construct(protected \App\Services\AuthorizationService $authService) {}

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

        $policy = AccessPolicy::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $request->name,
            'effect' => $request->effect,
            'conditions' => json_decode($request->conditions, true),
            'priority' => $request->priority,
        ]);

        $this->authService->audit(
            auth()->user(), 
            'security.policy_created', 
            $policy, 
            'success', 
            "Established new security protocol: {$request->name}"
        );

        return redirect()->route('policies.index')->with('success', 'Access policy created successfully.');
    }

    public function destroy(AccessPolicy $policy)
    {
        $name = $policy->name;
        $policy->delete();

        $this->authService->audit(
            auth()->user(), 
            'security.policy_purged', 
            null, 
            'success', 
            "Security protocol purged: {$name}"
        );

        return back()->with('success', 'Policy removed.');
    }
}
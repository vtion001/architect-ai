<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::whereNull('tenant_id')->orWhere('tenant_id', auth()->user()->tenant_id)->get();

        return view('tenant.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'tenant_id' => auth()->user()->tenant_id,
            'email' => $request->email,
            'password' => Hash::make(Str::random(16)),
            'status' => 'active',
        ]);

        $user->roles()->attach($request->role_id, ['scope_type' => 'tenant']);

        return response()->json(['message' => 'User added successfully', 'user' => $user], 201);
    }
}
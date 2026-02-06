<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Role;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function __construct(protected AuthorizationService $authService) {}

    public function index()
    {
        $users = User::with('roles')->get();
        $invitations = Invitation::with('role')->whereNull('accepted_at')->get();
        $roles = Role::whereNull('tenant_id')->orWhere('tenant_id', auth()->user()->tenant_id)->get();

        $stats = [
            'total_identities' => $users->count() + $invitations->count(),
            'active_sessions' => $users->where('last_login_at', '>=', now()->subDay())->count(),
            'security_health' => $users->count() > 0 
                ? round(($users->where('mfa_enabled', true)->count() / $users->count()) * 100) 
                : 100,
        ];

        return view('tenant.users.users', compact('users', 'roles', 'invitations', 'stats'));
    }

    /**
     * Dispatch a new user invitation.
     */
    public function invite(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
        ]);

        $invitation = Invitation::create([
            'tenant_id' => auth()->user()->tenant_id,
            'inviter_id' => auth()->id(),
            'email' => $request->email,
            'role_id' => $request->role_id,
            'token' => Str::random(40),
            'expires_at' => now()->addDays(7),
        ]);

        $this->authService->audit(
            auth()->user(), 
            'user.invited', 
            $invitation, 
            'success', 
            "Identity invitation dispatched to: {$request->email}"
        );

        // In a production app, we would send an actual email here.
        // For this prototype, the invite will show up in the UI.
        return response()->json([
            'message' => 'Invitation protocol initiated.',
            'invitation' => $invitation
        ], 201);
    }

    public function store(Request $request)
    {
        // Redirect legacy store calls to invite
        return $this->invite($request);
    }
}
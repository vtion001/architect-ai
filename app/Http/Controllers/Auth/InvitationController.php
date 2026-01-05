<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    /**
     * Display the invitation acceptance page.
     */
    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        if ($invitation->isExpired()) {
            abort(403, 'This invitation has expired.');
        }

        return view('auth.invitation-join', compact('invitation'));
    }

    /**
     * Process the invitation acceptance.
     */
    public function accept(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        if ($invitation->isExpired()) {
            abort(403, 'This invitation has expired.');
        }

        $request->validate([
            'password' => 'required|string|min:12|confirmed',
        ]);

        // 1. Create the user
        $user = User::create([
            'tenant_id' => $invitation->tenant_id,
            'email' => $invitation->email,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        // 2. Assign the role
        $user->roles()->attach($invitation->role_id, ['scope_type' => 'tenant']);

        // 3. Mark invitation as accepted
        $invitation->update(['accepted_at' => now()]);

        // 4. Log in the user
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Welcome to the grid!');
    }
}
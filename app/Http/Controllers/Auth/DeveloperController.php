<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeveloperController extends Controller
{
    public function __construct(protected AuthorizationService $authService) {}

    public function impersonate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|min:10', // Justification mandatory
        ]);

        $developer = Auth::user();
        $targetUser = User::withoutGlobalScope('tenant')->findOrFail($request->user_id);

        if (!$developer->is_developer) {
            abort(403, 'Unauthorized');
        }

        // 1. Audit Log (Critical)
        $this->authService->audit(
            $developer,
            'user.impersonate',
            $targetUser,
            'success',
            $request->reason
        );

        // 2. Perform Impersonation (Login as target without password)
        Auth::login($targetUser);
        session(['impersonated_by' => $developer->id]);

        return response()->json([
            'message' => "Impersonating {$targetUser->email}",
            'redirect' => '/dashboard',
        ]);
    }

    public function stopImpersonating()
    {
        if (!session()->has('impersonated_by')) {
            abort(403);
        }

        $developerId = session('impersonated_by');
        Auth::loginUsingId($developerId);
        session()->forget('impersonated_by');

        return response()->json(['message' => 'Impersonation ended']);
    }
}
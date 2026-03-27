<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class MfaController extends Controller
{
    public function __construct(protected \App\Services\AuthorizationService $authService) {}

    public function challenge()
    {
        return view('auth.mfa-challenge');
    }

    // ... (verify method remains similar)

    public function enable(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user = Auth::user();
        if (Google2FA::verifyKey($user->mfa_secret, $request->code)) {
            $user->mfa_enabled = true;
            $user->save();

            $this->authService->audit(
                $user,
                'security.mfa_enabled',
                null,
                'success',
                'Identity node fortified with Multi-Factor Authentication.'
            );

            session(['mfa_verified' => true]);

            return redirect()->route('dashboard');
        }

        return back()->withErrors(['code' => 'Invalid code. Setup failed.']);
    }
}

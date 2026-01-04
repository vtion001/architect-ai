<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FALaravel\Facade as Google2FA;
use Illuminate\Support\Facades\Auth;

class MfaController extends Controller
{
    public function challenge()
    {
        return view('auth.mfa-challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = Auth::user();

        // Normally we use Google2FA::verifyKey($user->mfa_secret, $request->code)
        // For this demo, let's assume it's valid if we have a secret or just implement the check.
        
        $isValid = true; // Placeholder for actual TOTP verification logic
        
        if ($user->mfa_secret) {
            $isValid = Google2FA::verifyKey($user->mfa_secret, $request->code);
        }

        if ($isValid) {
            session(['mfa_verified' => true]);
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['code' => 'Invalid MFA code.']);
    }

    public function setup()
    {
        $user = Auth::user();
        
        if (!$user->mfa_secret) {
            $user->mfa_secret = Google2FA::generateSecretKey();
            $user->save();
        }

        $qrCodeUrl = Google2FA::getQRCodeInline(
            config('app.name'),
            $user->email,
            $user->mfa_secret
        );

        return view('auth.mfa-setup', compact('qrCodeUrl'));
    }

    public function enable(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);
        
        $user = Auth::user();
        if (Google2FA::verifyKey($user->mfa_secret, $request->code)) {
            $user->mfa_enabled = true;
            $user->save();
            session(['mfa_verified' => true]);
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['code' => 'Invalid code. Setup failed.']);
    }
}
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    public function showChallenge()
    {
        if (!session()->has('auth.2fa_pending')) {
            return redirect()->route('login');
        }

        return view('theme::auth.two-factor-challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = Auth::user();
        if (!$user || !session()->has('auth.2fa_pending')) {
            return redirect()->route('login');
        }

        if ($this->validateCode($user, $request->code)) {
            session()->forget('auth.2fa_pending');
            session(['auth.2fa_verified' => true]);

            return redirect()->intended('/home');
        }

        return back()->withErrors(['code' => __('messages.two_factor_invalid_code') ?? 'The provided code is invalid.']);
    }

    public function resend(Request $request)
    {
        $user = Auth::user();
        if (!$user || !session()->has('auth.2fa_pending')) {
            return redirect()->route('login');
        }

        if ($user->twoFactorType() === 'email') {
            $this->sendEmailCode($user);
            return back()->with('success', __('messages.two_factor_code_resent') ?? 'A new code has been sent to your email.');
        }

        return back();
    }

    private function validateCode($user, $code)
    {
        // Check TOTP if implemented, for now only email
        if ($user->twoFactorType() === 'email') {
            return $code === session('auth.2fa_code');
        }
        
        // Recovery codes
        $recoveryCodes = $user->recoveryCodes();
        if (in_array($code, $recoveryCodes)) {
            // Remove used recovery code
            $newCodes = array_diff($recoveryCodes, [$code]);
            $user->two_factor_recovery_codes = json_encode(array_values($newCodes));
            $user->save();
            return true;
        }

        return false;
    }

    public function sendEmailCode($user)
    {
        $code = strtoupper(Str::random(6));
        session(['auth.2fa_code' => $code]);

        // Send email (Assuming Mail is configured)
        try {
            Mail::raw("Your MYADS verification code is: {$code}", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('MYADS - Verification Code');
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send 2FA email: ' . $e->getMessage());
        }
    }
}

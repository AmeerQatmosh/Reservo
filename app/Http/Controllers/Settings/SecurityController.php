<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PasswordUpdateRequest;
use App\Http\Requests\Settings\TwoFactorAuthenticationRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;

class SecurityController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return Features::canManageTwoFactorAuthentication()
            && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
                ? [new Middleware('password.confirm', only: ['edit'])]
                : [];
    }

    public function edit(TwoFactorAuthenticationRequest $request): View
    {
        if (Features::canManageTwoFactorAuthentication()) {
            $request->ensureStateIsValid();
        }

        $user = $request->user()->fresh();
        $canManageTwoFactor = Features::canManageTwoFactorAuthentication();
        $requiresConfirmation = Features::canManageTwoFactorAuthentication()
            && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
        $twoFactorEnabled = $canManageTwoFactor && $user->hasEnabledTwoFactorAuthentication();
        $pendingTwoFactor = $canManageTwoFactor
            && Fortify::confirmsTwoFactorAuthentication()
            && ! is_null($user->two_factor_secret)
            && is_null($user->two_factor_confirmed_at);

        $qrSvg = null;
        $twoFactorSecretPlain = null;
        if ($pendingTwoFactor) {
            $qrSvg = $user->twoFactorQrCodeSvg();
            $twoFactorSecretPlain = Fortify::currentEncrypter()->decrypt($user->two_factor_secret);
        }

        $recoveryCodes = [];
        if ($canManageTwoFactor && $twoFactorEnabled && $user->two_factor_recovery_codes) {
            try {
                $recoveryCodes = $user->recoveryCodes();
            } catch (\Throwable) {
                $recoveryCodes = [];
            }
        }

        return view('settings.security', [
            'canManageTwoFactor' => $canManageTwoFactor,
            'requiresConfirmation' => $requiresConfirmation,
            'twoFactorEnabled' => $twoFactorEnabled,
            'pendingTwoFactor' => $pendingTwoFactor,
            'qrSvg' => $qrSvg,
            'twoFactorSecretPlain' => $twoFactorSecretPlain,
            'recoveryCodes' => $recoveryCodes,
        ]);
    }

    public function update(PasswordUpdateRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->password,
        ]);

        return back()->with('success', 'Password updated.');
    }
}

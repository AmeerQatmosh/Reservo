<?php

namespace App\Http\Responses;

use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

/**
 * After login, the app sends users to Blade routes (e.g. /dashboard), not Inertia pages.
 * A normal 302 + Inertia follow loads /dashboard as an XHR expecting an Inertia JSON payload,
 * which breaks and can show "plain JSON" errors. Inertia::location() forces a full document visit.
 */
final class FortifyLoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        if ($request->wantsJson()) {
            return response()->json(['two_factor' => false]);
        }

        return Inertia::location(redirect()->intended(Fortify::redirects('login')));
    }
}

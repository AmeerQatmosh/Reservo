<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mirrors {@see FortifyLoginResponse}: Blade dashboard after register must load as a
 * full document visit, not an Inertia XHR follow of a 302 redirect.
 */
final class FortifyRegisterResponse implements RegisterResponseContract
{
    public function toResponse($request): Response|JsonResponse
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 201);
        }

        return Inertia::location(redirect()->intended(Fortify::redirects('register')));
    }
}

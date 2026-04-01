<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blade pages must not be loaded via Inertia XHR — the client would show them in an error iframe.
 * Inertia GETs receive 409 + X-Inertia-Location so the browser does a full document load.
 */
class EnsureBladeFullPageForInertia
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && $request->header('X-Inertia')) {
            return Inertia::location($request->fullUrl());
        }

        return $next($request);
    }
}

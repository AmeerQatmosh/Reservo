<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Turbo Drive follows redirects with Fetch. After POST/PUT/PATCH/DELETE the correct
 * semantics are 303 See Other so the follow-up request is always GET — using 302
 * can strand navigation until a full reload (Turbo/GitHub Issue #84).
 */
final class NormalizeFormRedirectStatuses
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $response instanceof RedirectResponse) {
            return $response;
        }

        if ($response->getStatusCode() !== 302) {
            return $response;
        }

        if ($request->isMethodSafe()) {
            return $response;
        }

        return $response->setStatusCode(Response::HTTP_SEE_OTHER);
    }
}

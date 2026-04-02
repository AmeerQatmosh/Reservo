<?php

namespace App\Http\Middleware;

use App\Support\DemoState;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDemoEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! DemoState::enabled()) {
            abort(404);
        }

        return $next($request);
    }
}

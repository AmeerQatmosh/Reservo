<?php

namespace App\Http\Middleware;

use App\Support\DemoState;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDemoSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! DemoState::active()) {
            return redirect()->route('demo.index')
                ->with('success', 'Choose a role to start the guest demo.');
        }

        return $next($request);
    }
}

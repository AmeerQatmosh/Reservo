<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NavLayoutController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        if (! $request->user()?->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'layout' => ['required', 'in:horizontal,vertical'],
        ]);

        $request->user()->update(['nav_layout' => $validated['layout']]);

        return back();
    }
}

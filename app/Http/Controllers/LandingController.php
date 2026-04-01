<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()) {
            return redirect()->route('dashboard');
        }

        $featuredRooms = Room::query()
            ->orderBy('name')
            ->limit(3)
            ->get();

        return view('welcome', [
            'featuredRooms' => $featuredRooms,
        ]);
    }
}

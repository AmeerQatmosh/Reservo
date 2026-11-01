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
        $portfolio = (bool) config('reservo.portfolio_mode');

        if (! $portfolio && $request->user()) {
            return redirect()->route('dashboard');
        }

        $featuredRooms = $portfolio
            ? collect()
            : Room::query()
                ->orderBy('name')
                ->limit(3)
                ->get();

        return view('welcome', [
            'featuredRooms' => $featuredRooms,
            'demoEnabled' => $portfolio ? false : (bool) config('reservo.demo_enabled'),
        ]);
    }
}

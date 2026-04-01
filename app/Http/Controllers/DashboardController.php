<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user->isAdmin();
        $isSuperAdmin = $user->isSuperAdmin();

        $upcomingReservations = Reservation::query()
            ->where('user_id', $user->id)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->with('room')
            ->limit(5)
            ->get();

        $stats = [
            [
                'label' => 'My Reservations',
                'value' => Reservation::query()->where('user_id', $user->id)->count(),
            ],
            [
                'label' => 'Upcoming',
                'value' => Reservation::query()
                    ->where('user_id', $user->id)
                    ->where('date', '>=', now()->toDateString())
                    ->count(),
            ],
            [
                'label' => 'Available Rooms',
                'value' => Room::query()->count(),
            ],
        ];

        if ($isAdmin) {
            $stats[] = [
                'label' => 'All Reservations',
                'value' => Reservation::query()->count(),
            ];
        }

        return view('dashboard.index', [
            'upcomingReservations' => $upcomingReservations,
            'stats' => $stats,
            'isAdmin' => $isAdmin,
            'isSuperAdmin' => $isSuperAdmin,
            'roleLabel' => str_replace('_', ' ', $user->role),
        ]);
    }
}


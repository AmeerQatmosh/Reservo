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

        $myResCount = Reservation::query()->where('user_id', $user->id)->count();
        $upcomingCount = Reservation::query()
            ->where('user_id', $user->id)
            ->where('date', '>=', now()->toDateString())
            ->count();

        $favoriteRoomsCount = $user->favoriteRooms()->count();

        $stats = [
            [
                'label' => 'My reservations',
                'value' => $myResCount,
                'href' => route('reservations.my'),
                'icon' => 'clipboard-list',
                'accent' => 'indigo',
            ],
            [
                'label' => 'Upcoming',
                'value' => $upcomingCount,
                'href' => route('reservations.my', ['view' => 'calendar']),
                'icon' => 'calendar-days',
                'accent' => 'emerald',
            ],
            [
                'label' => 'Available rooms',
                'value' => Room::query()->count(),
                'href' => route('rooms.index'),
                'icon' => 'door-open',
                'accent' => 'amber',
            ],
            [
                'label' => 'Favourite rooms',
                'value' => $favoriteRoomsCount,
                'href' => route('favorite-rooms.index'),
                'icon' => 'bookmark',
                'accent' => 'rose',
            ],
        ];

        if ($isAdmin) {
            $stats[] = [
                'label' => 'All reservations',
                'value' => Reservation::query()->count(),
                'href' => route('admin.reservations.index'),
                'icon' => 'clipboard-list',
                'accent' => 'violet',
            ];
        }

        $quickLinks = [
            [
                'label' => 'Browse rooms',
                'description' => 'Search, filter, and open room details',
                'href' => route('rooms.index'),
                'icon' => 'door-open',
                'tone' => 'indigo',
            ],
            [
                'label' => 'My reservations',
                'description' => 'History list, calendar, create, edit, cancel',
                'href' => route('reservations.my'),
                'icon' => 'calendar-days',
                'tone' => 'emerald',
            ],
            [
                'label' => 'Favourite rooms',
                'description' => 'Saved spaces and quick access to book',
                'href' => route('favorite-rooms.index'),
                'icon' => 'bookmark',
                'tone' => 'rose',
            ],
        ];
        if ($isAdmin) {
            $quickLinks[] = [
                'label' => 'Manage rooms',
                'description' => 'Create, edit, and archive spaces',
                'href' => route('admin.rooms.index'),
                'icon' => 'building-2',
                'tone' => 'amber',
            ];
            $quickLinks[] = [
                'label' => 'All reservations',
                'description' => 'Operational calendar across guests',
                'href' => route('admin.reservations.index'),
                'icon' => 'clipboard-list',
                'tone' => 'violet',
            ];
            $quickLinks[] = [
                'label' => $isSuperAdmin ? 'Manage users' : 'View users',
                'description' => 'Roles, access, and accounts',
                'href' => route('admin.users.index'),
                'icon' => 'users',
                'tone' => 'rose',
            ];
        }

        return view('dashboard.index', [
            'upcomingReservations' => $upcomingReservations,
            'stats' => $stats,
            'quickLinks' => $quickLinks,
        ]);
    }
}

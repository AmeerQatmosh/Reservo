<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user->isAdmin();
        $isSuperAdmin = $user->isSuperAdmin();

        if ($isAdmin) {
            return $this->adminDashboard($user, $isSuperAdmin);
        }

        return $this->userHome($request, $user);
    }

    private function adminDashboard(User $user, bool $isSuperAdmin): View
    {
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
                'accent' => 'teal',
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
                'icon' => 'star',
                'accent' => 'yellow',
            ],
        ];

        $stats[] = [
            'label' => 'All reservations',
            'value' => Reservation::query()->count(),
            'href' => route('admin.reservations.index'),
            'icon' => 'clipboard-list',
            'accent' => 'violet',
        ];

        $quickLinks = [
            [
                'label' => 'Browse rooms',
                'description' => 'Search, filter, and open room details',
                'href' => route('rooms.index'),
                'icon' => 'door-open',
                'tone' => 'teal',
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
                'icon' => 'star',
                'tone' => 'yellow',
            ],
            [
                'label' => 'Manage rooms',
                'description' => 'Create, edit, and archive spaces',
                'href' => route('admin.rooms.index'),
                'icon' => 'building-2',
                'tone' => 'amber',
            ],
            [
                'label' => 'All reservations',
                'description' => 'Operational calendar across guests',
                'href' => route('admin.reservations.index'),
                'icon' => 'clipboard-list',
                'tone' => 'violet',
            ],
            [
                'label' => $isSuperAdmin ? 'Manage users' : 'View users',
                'description' => 'Roles, access, and accounts',
                'href' => route('admin.users.index'),
                'icon' => 'users',
                'tone' => 'rose',
            ],
        ];

        return view('dashboard.admin', [
            'upcomingReservations' => $upcomingReservations,
            'stats' => $stats,
            'quickLinks' => $quickLinks,
        ]);
    }

    private function userHome(Request $request, User $user): View
    {
        $homeTab = $request->query('tab', 'all');
        if (! in_array($homeTab, ['all', 'upcoming', 'rooms', 'favourites'], true)) {
            $homeTab = 'all';
        }

        $browseDate = now()->toDateString();

        /** @var list<int> $favoriteRoomIds */
        $favoriteRoomIds = $user->favoriteRooms()
            ->pluck('rooms.id')
            ->map(static fn ($id): int => (int) $id)
            ->values()
            ->all();

        $upcomingBase = Reservation::query()
            ->where('user_id', $user->id)
            ->where('date', '>=', now()->toDateString())
            ->with('room')
            ->orderBy('date')
            ->orderBy('start_time');

        $upcomingCount = Reservation::query()
            ->where('user_id', $user->id)
            ->where('date', '>=', now()->toDateString())
            ->count();
        $favoriteRoomsCount = $user->favoriteRooms()->count();
        $roomsTotal = Room::query()->count();

        $homeTabs = [
            'all' => __('All'),
            'upcoming' => __('Upcoming'),
            'rooms' => __('Available rooms'),
            'favourites' => __('Favourites'),
        ];

        $roomsQuery = Room::query()->orderBy('name');

        $favoriteRoomsQuery = Room::query()
            ->whereIn('id', $favoriteRoomIds)
            ->orderBy('name');

        $upcomingPreview = (clone $upcomingBase)->limit(5)->get();
        $roomsPreview = (clone $roomsQuery)->limit(6)->get();
        $favoritesPreview = (clone $favoriteRoomsQuery)->limit(6)->get();

        $upcomingPage = (clone $upcomingBase)->paginate(12)->withQueryString();
        $roomsPage = (clone $roomsQuery)->paginate(12)->withQueryString();
        $favoritesPage = (clone $favoriteRoomsQuery)->paginate(12)->withQueryString();

        return view('dashboard.home', [
            'homeTab' => $homeTab,
            'homeTabs' => $homeTabs,
            'browseDate' => $browseDate,
            'favoriteRoomIds' => $favoriteRoomIds,
            'upcomingCount' => $upcomingCount,
            'favoriteRoomsCount' => $favoriteRoomsCount,
            'roomsTotal' => $roomsTotal,
            'upcomingPreview' => $upcomingPreview,
            'roomsPreview' => $roomsPreview,
            'favoritesPreview' => $favoritesPreview,
            'upcomingPage' => $upcomingPage,
            'roomsPage' => $roomsPage,
            'favoritesPage' => $favoritesPage,
        ]);
    }
}

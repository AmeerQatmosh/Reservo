<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Support\DemoRoomListing;
use App\Support\DemoState;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DemoController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (DemoState::active()) {
            return redirect()->route('demo.hub');
        }

        return view('demo.index');
    }

    public function start(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'in:user,admin,super_admin'],
        ]);

        DemoState::bootstrap($validated['role']);

        return redirect()->route('demo.hub')
            ->with('success', 'Guest demo started. Nothing here is saved to the real database.');
    }

    public function switchRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'in:user,admin,super_admin'],
        ]);

        if (! DemoState::active()) {
            return redirect()->route('demo.index');
        }

        DemoState::setRole($validated['role']);

        return redirect()->route('demo.hub');
    }

    public function exitDemo(): RedirectResponse
    {
        DemoState::exitDemo();

        return redirect()->route('home')
            ->with('success', 'You left the guest demo.');
    }

    public function hub(): View
    {
        $today = now()->toDateString();
        $reservations = DemoState::reservations();
        $upcoming = array_values(array_filter(
            $reservations,
            fn (array $r): bool => ($r['date'] ?? '') >= $today
        ));
        usort($upcoming, function (array $a, array $b): int {
            $da = ($a['date'] ?? '').' '.($a['start_time'] ?? '');
            $db = ($b['date'] ?? '').' '.($b['start_time'] ?? '');

            return $da <=> $db;
        });
        $upcoming = array_slice($upcoming, 0, 5);

        $roomCount = count(DemoState::rooms());
        $reservationCount = count($reservations);
        $upcomingCount = count(array_filter($reservations, fn (array $r): bool => ($r['date'] ?? '') >= $today));

        $stats = [
            ['label' => 'My sandbox reservations', 'value' => $reservationCount],
            ['label' => 'Upcoming (sandbox)', 'value' => $upcomingCount],
            ['label' => 'Rooms in session', 'value' => $roomCount],
        ];
        if (DemoState::canAdmin()) {
            $stats[] = ['label' => 'All sandbox bookings', 'value' => $reservationCount];
        }

        return view('demo.hub', [
            'role' => DemoState::role(),
            'roomCount' => $roomCount,
            'reservationCount' => $reservationCount,
            'stats' => $stats,
            'upcomingReservations' => $upcoming,
            'isAdmin' => DemoState::canAdmin(),
            'isSuperAdmin' => DemoState::canSuperAdmin(),
        ]);
    }

    public function rooms(Request $request): View
    {
        if (! DemoState::canUser()) {
            abort(403);
        }

        $normalized = array_map(fn (array $r) => DemoState::normalizeRoom($r), DemoState::rooms());
        $applied = DemoRoomListing::applyRequestFilters($normalized, $request);
        $rooms = $this->paginateArrayForDemo($applied['rooms'], $request, 12);

        return view('demo.rooms', [
            'rooms' => $rooms,
            'filters' => $applied['filters'],
            'filterOptions' => DemoRoomListing::filterOptionsFromRooms($normalized),
        ]);
    }

    public function roomShow(Request $request, int $id): View
    {
        if (! DemoState::canUser()) {
            abort(403);
        }

        $room = DemoState::findRoom($id);
        if ($room === null) {
            abort(404);
        }

        $selectedDate = $request->string('date')->toString() ?: now()->toDateString();

        return view('demo.room-show', [
            'room' => $room,
            'selectedDate' => $selectedDate,
            'booked' => DemoState::reservationsForRoomOnDate($id, $selectedDate),
        ]);
    }

    public function reservationsMy(Request $request): View
    {
        if (! DemoState::canUser()) {
            abort(403);
        }

        $rooms = array_map(fn (array $r) => DemoState::normalizeRoom($r), DemoState::rooms());
        $reservations = DemoState::reservations();
        usort($reservations, function (array $a, array $b): int {
            $da = ($a['date'] ?? '').' '.($a['start_time'] ?? '');
            $db = ($b['date'] ?? '').' '.($b['start_time'] ?? '');

            return $db <=> $da;
        });

        $prefillRoomId = $request->query('room_id');
        $prefillDate = $request->query('date');

        return view('demo.reservations-my', [
            'rooms' => $rooms,
            'reservations' => $reservations,
            'prefill' => [
                'room_id' => $prefillRoomId !== null && $prefillRoomId !== '' ? (string) (int) $prefillRoomId : '',
                'date' => is_string($prefillDate) ? $prefillDate : '',
            ],
        ]);
    }

    public function storeReservation(Request $request): RedirectResponse
    {
        if (! DemoState::canUser()) {
            abort(403);
        }

        $validated = $request->validate([
            'room_id' => ['required', 'integer'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'after_store' => ['nullable', 'string', 'in:room,my'],
        ]);

        $errors = DemoState::addReservation(
            (int) $validated['room_id'],
            $validated['date'],
            $validated['start_time'],
            $validated['end_time'],
        );

        if ($errors !== null) {
            return back()->withErrors($errors)->withInput();
        }

        $success = 'Reservation created in the sandbox. Nothing is saved to the real database.';

        if (($validated['after_store'] ?? 'room') === 'my') {
            return redirect()
                ->route('demo.reservations.my')
                ->with('success', $success);
        }

        return redirect()
            ->route('demo.room.show', ['id' => $validated['room_id'], 'date' => $validated['date']])
            ->with('success', $success);
    }

    public function destroyReservation(int $id): RedirectResponse
    {
        if (! DemoState::canUser()) {
            abort(403);
        }

        DemoState::deleteReservation($id);

        return back()->with('success', 'Demo booking removed.');
    }

    public function adminRooms(Request $request): View
    {
        if (! DemoState::canAdmin()) {
            abort(403);
        }

        $normalized = array_map(fn (array $r) => DemoState::normalizeRoom($r), DemoState::rooms());
        $applied = DemoRoomListing::applyRequestFilters($normalized, $request);
        $rooms = $this->paginateArrayForDemo($applied['rooms'], $request, 20);

        return view('demo.admin-rooms', [
            'rooms' => $rooms,
            'filters' => $applied['filters'],
            'filterOptions' => DemoRoomListing::filterOptionsFromRooms($normalized),
        ]);
    }

    public function createRoom(): View
    {
        if (! DemoState::canAdmin()) {
            abort(403);
        }

        return view('demo.admin-rooms-create');
    }

    public function adminRoomShow(int $id): View
    {
        if (! DemoState::canAdmin()) {
            abort(403);
        }

        $room = DemoState::findRoom($id);
        if ($room === null) {
            abort(404);
        }

        return view('demo.admin-room-show', [
            'room' => $room,
        ]);
    }

    public function storeRoom(Request $request): RedirectResponse
    {
        if (! DemoState::canAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['required', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'size_sqm' => ['nullable', 'integer', 'min:1', 'max:50000'],
            'amenities_text' => ['nullable', 'string', 'max:5000'],
            'image_url' => ['nullable', 'string', 'url', 'max:2048'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
        ]);

        $validated['amenities'] = Room::parseAmenitiesText($validated['amenities_text'] ?? null) ?? [];
        unset($validated['amenities_text']);

        if (array_key_exists('hourly_rate', $validated) && $validated['hourly_rate'] === '') {
            $validated['hourly_rate'] = null;
        }

        $errors = DemoState::addRoom($validated);
        if ($errors !== null) {
            return back()->withErrors($errors)->withInput();
        }

        return redirect()->route('demo.admin.rooms')->with('success', 'Room added to this demo session only.');
    }

    public function destroyRoom(int $id): RedirectResponse
    {
        if (! DemoState::canAdmin()) {
            abort(403);
        }

        DemoState::deleteRoom($id);

        return redirect()->route('demo.admin.rooms')->with('success', 'Demo room removed.');
    }

    public function adminReservations(Request $request): View
    {
        if (! DemoState::canAdmin()) {
            abort(403);
        }

        $rooms = DemoState::rooms();
        $byId = [];
        $roomById = [];
        foreach ($rooms as $r) {
            $id = (int) ($r['id'] ?? 0);
            $byId[$id] = $r['name'] ?? 'Room';
            $roomById[$id] = DemoState::normalizeRoom($r);
        }

        $roomId = $request->input('room_id');
        $date = $request->input('date');
        $guestSearch = trim((string) $request->input('guest', $request->input('user', '')));

        $list = DemoState::reservations();
        if ($roomId !== null && $roomId !== '') {
            $rid = (int) $roomId;
            $list = array_values(array_filter($list, fn (array $row): bool => (int) ($row['room_id'] ?? 0) === $rid));
        }
        if ($date !== null && $date !== '') {
            $list = array_values(array_filter($list, fn (array $row): bool => ($row['date'] ?? '') === $date));
        }
        if ($guestSearch !== '') {
            $g = mb_strtolower($guestSearch);
            $list = array_values(array_filter(
                $list,
                fn (array $row): bool => str_contains(mb_strtolower((string) ($row['label'] ?? '')), $g)
            ));
        }

        usort($list, function (array $a, array $b): int {
            $da = ($a['date'] ?? '').' '.($a['start_time'] ?? '');
            $db = ($b['date'] ?? '').' '.($b['start_time'] ?? '');

            return $db <=> $da;
        });

        $reservations = $this->paginateArrayForDemo($list, $request, 30);

        $roomSelect = array_map(fn (array $r) => DemoState::normalizeRoom($r), DemoState::rooms());
        usort($roomSelect, fn (array $a, array $b): int => strcmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? '')));

        return view('demo.admin-reservations', [
            'reservations' => $reservations,
            'roomNames' => $byId,
            'roomById' => $roomById,
            'rooms' => $roomSelect,
            'filters' => [
                'room_id' => $roomId,
                'date' => $date,
                'guest' => $guestSearch,
            ],
        ]);
    }

    public function adminUsers(Request $request): View
    {
        if (! DemoState::canAdmin()) {
            abort(403);
        }

        $search = trim((string) $request->query('search'));
        $role = $request->query('role', 'all');

        $users = DemoState::sampleDirectoryUsers();
        if ($search !== '') {
            $s = mb_strtolower($search);
            $users = array_values(array_filter(
                $users,
                fn (array $u): bool => str_contains(mb_strtolower($u['name']), $s)
                    || str_contains(mb_strtolower($u['email']), $s)
            ));
        }
        if (in_array($role, ['user', 'admin', 'super_admin'], true)) {
            $users = array_values(array_filter($users, fn (array $u): bool => $u['role'] === $role));
        }

        usort($users, fn (array $a, array $b): int => strcmp($a['name'], $b['name']));

        $page = max(1, (int) $request->input('page', 1));
        $perPage = 20;
        $total = count($users);
        $slice = array_slice($users, ($page - 1) * $perPage, $perPage);
        $paginator = (new LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'pageName' => 'page'],
        ))->withQueryString();

        return view('demo.admin-users', [
            'users' => $paginator,
            'canManageRoles' => DemoState::canSuperAdmin(),
            'filters' => [
                'search' => $search,
                'role' => $role,
            ],
        ]);
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    private function paginateArrayForDemo(array $items, Request $request, int $perPage): LengthAwarePaginator
    {
        $page = max(1, (int) $request->input('page', 1));
        $total = count($items);
        $slice = array_slice($items, ($page - 1) * $perPage, $perPage);

        return (new LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'pageName' => 'page'],
        ))->withQueryString();
    }
}

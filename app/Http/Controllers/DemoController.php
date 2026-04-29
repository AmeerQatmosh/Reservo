<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Support\DemoRoomListing;
use App\Support\DemoState;
use App\Support\ReservationDatePickerBookings;
use App\Support\RoomBookingDayAvailability;
use App\Support\RoomListing;
use App\Support\UserReservationCalendar;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
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
            'browseView' => RoomListing::browseView($request),
            'browseDate' => now()->toDateString(),
            'favoriteRoomIds' => DemoState::favoriteRoomIds(),
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

        $prefill = [
            'room_id' => $prefillRoomId !== null && $prefillRoomId !== '' ? (string) (int) $prefillRoomId : '',
            'date' => is_string($prefillDate) ? $prefillDate : '',
        ];

        $myKeep = fn (array $extra): array => array_merge(
            $this->demoReservationsPrefillOnly($request),
            $extra,
        );

        $rawView = $request->query('view', 'list');
        $viewMode = $rawView === 'calendar' ? 'calendar' : 'list';

        $calendarHeading = '';
        $weeks = [];
        $reservationsByDate = collect();
        $calendarPrevUrl = '';
        $calendarNextUrl = '';
        $calendarTodayUrl = '';
        $browseRoomsUrl = route('demo.rooms');
        $calendarSubtitle = '';

        $roomsBookingSummaryMeta = collect($rooms)->mapWithKeys(function (array $room): array {
            $id = (string) ($room['id'] ?? '');
            $rate = $room['hourly_rate'] ?? null;
            $rateLabel = DemoState::hourlyRateLabel($rate !== null && $rate !== '' ? (float) $rate : null);
            $statsParts = [
                __('Up to :capacity people', ['capacity' => $room['capacity'] ?? '']),
            ];
            $sqm = $room['size_sqm'] ?? null;
            if ($sqm !== null && $sqm !== '') {
                $statsParts[] = $sqm.' m²';
            }
            if ($rateLabel !== null) {
                $statsParts[] = $rateLabel;
            }

            return [
                $id => [
                    'name' => (string) ($room['name'] ?? ''),
                    'location' => (string) ($room['location'] ?? ''),
                    'capacity' => $room['capacity'] ?? '',
                    'size_sqm' => $room['size_sqm'] ?? null,
                    'hourly_rate_label' => $rateLabel,
                    'stats_summary' => implode(' · ', $statsParts),
                    'image_url' => (string) ($room['image_url'] ?? ''),
                    'show_url' => route('demo.room.show', ['id' => $room['id']]),
                ],
            ];
        })->all();

        if ($viewMode === 'calendar') {
            [$year, $month] = UserReservationCalendar::parseYearMonthFromRequest($request);
            $grid = UserReservationCalendar::buildWeekGrid($year, $month);

            /** @phpstan-ignore-next-line */
            $weeks = $grid['weeks'];

            /** @phpstan-ignore-next-line */
            $calendarHeading = $grid['heading'];

            /** @phpstan-ignore-next-line */
            $gridStart = $grid['gridStart']->toDateString();

            /** @phpstan-ignore-next-line */
            $gridEnd = $grid['gridEnd']->toDateString();

            $inRange = array_values(array_filter(
                DemoState::reservations(),
                fn (array $r) => ($r['date'] ?? '') !== ''
                    && $r['date'] >= $gridStart
                    && $r['date'] <= $gridEnd,
            ));

            usort($inRange, function (array $a, array $b): int {
                $ca = (($a['date'] ?? '').' '.($a['start_time'] ?? ''));
                $cb = (($b['date'] ?? '').' '.($b['start_time'] ?? ''));

                return strcmp($ca, $cb);
            });

            $reservationsByDate = collect($inRange)
                ->groupBy(fn (array $r) => (string) ($r['date'] ?? ''))
                ->map(function ($items) {
                    return collect($items)->map(function (array $r) {
                        $roomId = (int) ($r['room_id'] ?? 0);
                        $room = DemoState::findRoom($roomId);
                        $date = (string) ($r['date'] ?? '');
                        $start = substr((string) ($r['start_time'] ?? '00:00:00'), 0, 5);
                        $end = substr((string) ($r['end_time'] ?? '00:00:00'), 0, 5);

                        return [
                            'href' => route('demo.room.show', ['id' => $roomId, 'date' => $date]),
                            'start' => $start,
                            'end' => $end,
                            'room_name' => $room['name'] ?? __('Room'),
                        ];
                    });
                });

            $monthReservationCount = count($inRange);

            /** @phpstan-ignore-next-line */
            $monthCarbon = $grid['monthCarbon'];
            /** @phpstan-ignore-next-line */
            $prev = $monthCarbon->copy()->subMonth();
            /** @phpstan-ignore-next-line */
            $next = $monthCarbon->copy()->addMonth();

            $calendarPrevUrl = route('demo.reservations.my', $myKeep(['view' => 'calendar', 'year' => $prev->year, 'month' => $prev->month]));
            $calendarNextUrl = route('demo.reservations.my', $myKeep(['view' => 'calendar', 'year' => $next->year, 'month' => $next->month]));
            $calendarTodayUrl = route('demo.reservations.my', $myKeep(['view' => 'calendar', 'year' => now()->year, 'month' => now()->month]));

            $calendarSubtitle = $monthReservationCount === 1
                ? __('This month has one reservation.')
                : __('This month has :count reservations.', ['count' => $monthReservationCount]);
            if ($monthReservationCount === 0) {
                $calendarSubtitle = __('No reservations in this month yet.');
            }
        }

        return view('demo.reservations-my', [
            'rooms' => $rooms,
            'reservations' => $reservations,
            'roomsBookingSummaryMeta' => $roomsBookingSummaryMeta,
            'prefill' => $prefill,
            'viewMode' => $viewMode,
            'calendarHeading' => $calendarHeading,
            'weeks' => $weeks,
            'reservationsByDate' => $reservationsByDate,
            'calendarPrevUrl' => $calendarPrevUrl,
            'calendarNextUrl' => $calendarNextUrl,
            'calendarTodayUrl' => $calendarTodayUrl,
            'browseRoomsUrl' => $browseRoomsUrl,
            'calendarSubtitle' => $calendarSubtitle,
            'miniCalendarBookings' => ReservationDatePickerBookings::forDemoSandbox(DemoState::reservations()),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function demoReservationsPrefillOnly(Request $request): array
    {
        return array_filter([
            'room_id' => $request->query('room_id'),
            'date' => $request->query('date'),
        ], fn ($value) => $value !== null && $value !== '');
    }

    public function calendar(Request $request): RedirectResponse
    {
        if (! DemoState::canUser()) {
            abort(403);
        }

        return redirect()->route('demo.reservations.my', array_merge(
            ['view' => 'calendar'],
            array_filter(
                $request->only(['year', 'month', 'room_id', 'date']),
                fn ($value) => $value !== null && $value !== '',
            ),
        ));
    }

    public function roomBookedSlots(Request $request): JsonResponse
    {
        if (! DemoState::canUser()) {
            abort(403);
        }

        $validated = $request->validate([
            'room_id' => ['required', 'integer'],
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $roomId = (int) $validated['room_id'];
        if (DemoState::findRoom($roomId) === null) {
            abort(404);
        }

        $rows = DemoState::reservationsForRoomOnDate($roomId, $validated['date']);

        $slots = [];
        foreach ($rows as $r) {
            $slots[] = [
                'start' => substr((string) ($r['start_time'] ?? ''), 0, 5),
                'end' => substr((string) ($r['end_time'] ?? ''), 0, 5),
            ];
        }

        return response()->json(['slots' => $slots]);
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

    public function toggleRoomFavorite(Request $request, int $id): RedirectResponse
    {
        if (! DemoState::canUser()) {
            abort(403);
        }

        if (DemoState::findRoom($id) === null) {
            abort(404);
        }

        DemoState::toggleFavoriteRoom($id);

        return back();
    }

    public function quickBookRoom(Request $request, int $id): RedirectResponse
    {
        if (! DemoState::canUser()) {
            abort(403);
        }

        if (DemoState::findRoom($id) === null) {
            abort(404);
        }

        $validated = $request->validate([
            'date' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $dateString = isset($validated['date'])
            ? (string) $validated['date']
            : now()->toDateString();

        if (! RoomBookingDayAvailability::hasAnyBookableSlotDemo($id, $dateString)) {
            return back()->with(
                'warning',
                __('This room has no available time window on :date—you can choose another date in My reservations or open the room details to browse other days.', [
                    'date' => $dateString,
                ]),
            );
        }

        return redirect()->route('demo.reservations.my', [
            'room_id' => $id,
            'date' => $dateString,
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

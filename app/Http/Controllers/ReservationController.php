<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Support\ReservationBookingWindow;
use App\Support\ReservationDatePickerBookings;
use App\Support\ReservationOverlapMessaging;
use App\Support\UserReservationCalendar;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function my(Request $request): View
    {
        $reservations = Reservation::query()
            ->where('user_id', $request->user()->id)
            ->with('room')
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->paginate(20);

        $rooms = Room::query()
            ->orderBy('name')
            ->get();

        $prefilledRoom = null;

        if ($request->filled('room_id')) {
            $prefilledRoom = $rooms->firstWhere('id', (int) $request->query('room_id'));
        }
        if ($prefilledRoom === null && old('room_id')) {
            $prefilledRoom = $rooms->firstWhere('id', (int) old('room_id'));
        }

        $myKeep = fn (array $extra) => array_merge(
            $this->reservationsPrefillOnly($request),
            $extra,
        );

        $rawView = $request->query('view', 'list');
        $viewMode = $rawView === 'calendar' ? 'calendar' : 'list';

        $calendarHeading = '';

        /** @phpstan-ignore-next-line */
        $weeks = [];

        $reservationsByDate = collect();
        $monthReservationCount = 0;
        $calendarPrevUrl = '';
        $calendarNextUrl = '';
        $calendarTodayUrl = '';

        $browseRoomsUrl = route('rooms.index');
        $calendarSubtitle = '';

        if ($viewMode === 'calendar') {
            [$year, $month] = UserReservationCalendar::parseYearMonthFromRequest($request);
            $grid = UserReservationCalendar::buildWeekGrid($year, $month);

            /** @phpstan-ignore-next-line */
            $weeks = $grid['weeks'];
            /** @phpstan-ignore-next-line */
            $calendarHeading = $grid['heading'];

            $monthWindow = Reservation::query()
                ->where('user_id', $request->user()->id)
                ->whereBetween('date', [
                    $grid['gridStart']->toDateString(),
                    $grid['gridEnd']->toDateString(),
                ])
                ->with(['room' => fn ($q) => $q->withTrashed()])
                ->orderBy('date')
                ->orderBy('start_time')
                ->get();

            $reservationsByDate = $monthWindow->groupBy(fn (Reservation $r): string => Carbon::parse($r->date)->format('Y-m-d'))
                ->map(fn ($group) => $group->map(fn (Reservation $r) => [
                    'href' => $r->isBeforeToday() ? null : route('reservations.edit', $r->id),
                    'start' => substr((string) $r->start_time, 0, 5),
                    'end' => substr((string) $r->end_time, 0, 5),
                    'room_name' => $r->room?->name ?? __('Room'),
                ]));

            $monthReservationCount = $monthWindow->count();

            /** @phpstan-ignore-next-line */
            $monthCarbon = $grid['monthCarbon'];

            /** @phpstan-ignore-next-line */
            $prev = $monthCarbon->copy()->subMonth();

            /** @phpstan-ignore-next-line */
            $next = $monthCarbon->copy()->addMonth();

            $calendarPrevUrl = route('reservations.my', $myKeep(['view' => 'calendar', 'year' => $prev->year, 'month' => $prev->month]));
            $calendarNextUrl = route('reservations.my', $myKeep(['view' => 'calendar', 'year' => $next->year, 'month' => $next->month]));
            $calendarTodayUrl = route('reservations.my', $myKeep(['view' => 'calendar', 'year' => now()->year, 'month' => now()->month]));

            $calendarSubtitle = $monthReservationCount === 1
                ? __('This month has one reservation.')
                : __('This month has :count reservations.', ['count' => $monthReservationCount]);
            if ($monthReservationCount === 0) {
                $calendarSubtitle = __('No reservations in this month yet.');
            }

        }

        $miniPickerReservationRows = Reservation::query()
            ->where('user_id', $request->user()->id)
            ->with(['room' => fn ($q) => $q->withTrashed()])
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->limit(500)
            ->get();

        $roomsBookingSummaryMeta = $rooms->mapWithKeys(function (Room $room): array {
            $statsParts = [
                __('Up to :capacity people', ['capacity' => $room->capacity]),
            ];
            if ($room->size_sqm !== null && $room->size_sqm !== '') {
                $statsParts[] = $room->size_sqm.' m²';
            }
            if ($label = $room->hourlyRateLabel()) {
                $statsParts[] = $label;
            }

            return [
                (string) $room->id => [
                    'name' => $room->name,
                    'location' => $room->location,
                    'capacity' => $room->capacity,
                    'size_sqm' => $room->size_sqm,
                    'hourly_rate_label' => $room->hourlyRateLabel(),
                    'stats_summary' => implode(' · ', $statsParts),
                    'image_url' => $room->image_url,
                    'show_url' => route('rooms.show', $room->id),
                ],
            ];
        })->all();

        return view('reservations.my', [
            'reservations' => $reservations,
            'rooms' => $rooms,
            'roomsBookingSummaryMeta' => $roomsBookingSummaryMeta,
            'prefill' => [
                'room_id' => $request->query('room_id'),
                'date' => $request->query('date'),
            ],
            'prefilledRoom' => $prefilledRoom,
            'viewMode' => $viewMode,
            'calendarHeading' => $calendarHeading,
            'weeks' => $weeks,
            'reservationsByDate' => $reservationsByDate,
            'monthReservationCount' => $monthReservationCount,
            'calendarPrevUrl' => $calendarPrevUrl,
            'calendarNextUrl' => $calendarNextUrl,
            'calendarTodayUrl' => $calendarTodayUrl,
            'browseRoomsUrl' => $browseRoomsUrl,
            'calendarSubtitle' => $calendarSubtitle,
            'miniCalendarBookings' => ReservationDatePickerBookings::forUserReservationModels($miniPickerReservationRows),
        ]);

    }

    /**
     * @return array<string, mixed>
     */
    protected function reservationsPrefillOnly(Request $request): array
    {
        return array_filter([
            'room_id' => $request->query('room_id'),
            'date' => $request->query('date'),
        ], fn ($v) => $v !== null && $v !== '');
    }

    public function edit(Request $request, int $id): View|RedirectResponse
    {
        $reservation = Reservation::query()
            ->where('user_id', $request->user()->id)
            ->with('room')
            ->findOrFail($id);

        if ($reservation->isBeforeToday()) {
            return redirect()
                ->route('reservations.my')
                ->withErrors([
                    'booking' => __('Past reservations cannot be changed. You can still view them in your history.'),
                ]);
        }

        $rooms = Room::query()
            ->orderBy('name')
            ->get();

        $miniPickerReservationRows = Reservation::query()
            ->where('user_id', $request->user()->id)
            ->with(['room' => fn ($q) => $q->withTrashed()])
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->limit(500)
            ->get();

        return view('reservations.edit', [
            'reservation' => $reservation,
            'rooms' => $rooms,
            'miniCalendarBookings' => ReservationDatePickerBookings::forUserReservationModels($miniPickerReservationRows),
        ]);
    }

    public function adminEdit(int $id): View
    {
        $reservation = Reservation::query()
            ->with(['room', 'user'])
            ->findOrFail($id);

        $rooms = Room::query()
            ->orderBy('name')
            ->get();

        return view('admin.reservations.edit', [
            'reservation' => $reservation,
            'rooms' => $rooms,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateReservationData($request, null);

        $userId = $request->user()->id;

        $created = DB::transaction(function () use ($validated, $userId) {
            if ($this->reservationConflictExists($validated)) {
                return null;
            }

            return Reservation::query()->create([
                'user_id' => $userId,
                'room_id' => $validated['room_id'],
                'date' => $validated['date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
            ]);
        }, 5);

        if (! $created) {
            $blocking = $this->overlappingReservationsForValidated($validated);

            return back()
                ->withErrors([
                    'overlap' => ReservationOverlapMessaging::forDatabaseReservations($validated, $blocking),
                ])
                ->withInput();
        }

        return back()->with('success', 'Reservation created.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $reservation = Reservation::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($reservation->isBeforeToday()) {
            return redirect()
                ->route('reservations.my')
                ->withErrors([
                    'booking' => __('Past reservations cannot be changed.'),
                ]);
        }

        $validated = $this->validateReservationData($request, $reservation);

        $updated = DB::transaction(function () use ($reservation, $validated) {
            if ($this->reservationConflictExists($validated, $reservation->id)) {
                return false;
            }

            $reservation->update($validated);

            return true;
        }, 5);

        if (! $updated) {
            $blocking = $this->overlappingReservationsForValidated($validated, $reservation->id);

            return back()
                ->withErrors([
                    'overlap' => ReservationOverlapMessaging::forDatabaseReservations($validated, $blocking),
                ])
                ->withInput();
        }

        return redirect()
            ->route('reservations.edit', $reservation->id)
            ->with('success', 'Reservation updated.');
    }

    public function adminUpdate(Request $request, int $id): RedirectResponse
    {
        $reservation = Reservation::query()->findOrFail($id);

        $validated = $this->validateReservationData($request, $reservation);

        $updated = DB::transaction(function () use ($reservation, $validated) {
            if ($this->reservationConflictExists($validated, $reservation->id)) {
                return false;
            }

            $reservation->update($validated);

            return true;
        }, 5);

        if (! $updated) {
            $blocking = $this->overlappingReservationsForValidated($validated, $reservation->id);

            return back()
                ->withErrors([
                    'overlap' => ReservationOverlapMessaging::forDatabaseReservations($validated, $blocking),
                ])
                ->withInput();
        }

        return redirect()
            ->route('admin.reservations.edit', $reservation->id)
            ->with('success', 'Reservation updated.');
    }

    public function index(): View
    {
        $roomId = request('room_id');
        $date = request('date');
        $userSearch = trim((string) request('user'));

        $reservations = Reservation::query()
            ->with(['room', 'user'])
            ->when($roomId, fn (Builder $query) => $query->where('room_id', $roomId))
            ->when($date, fn (Builder $query) => $query->whereDate('date', $date))
            ->when($userSearch !== '', function (Builder $query) use ($userSearch) {
                $query->whereHas('user', function (Builder $userQuery) use ($userSearch) {
                    $userQuery
                        ->where('name', 'like', "%{$userSearch}%")
                        ->orWhere('email', 'like', "%{$userSearch}%");
                });
            })
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->paginate(30)
            ->withQueryString();

        $rooms = Room::query()
            ->orderBy('name')
            ->get();

        $miniCalReservations = Reservation::query()
            ->with(['room' => fn ($q) => $q->withTrashed()])
            ->when($roomId, fn (Builder $query) => $query->where('room_id', $roomId))
            ->when($userSearch !== '', function (Builder $query) use ($userSearch) {
                $query->whereHas('user', function (Builder $userQuery) use ($userSearch) {
                    $userQuery
                        ->where('name', 'like', "%{$userSearch}%")
                        ->orWhere('email', 'like', "%{$userSearch}%");
                });
            })
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->limit(500)
            ->get();

        return view('admin.reservations.index', [
            'reservations' => $reservations,
            'rooms' => $rooms,
            'miniCalendarBookings' => ReservationDatePickerBookings::forAdminReservationModels($miniCalReservations),
            'filters' => [
                'room_id' => $roomId,
                'date' => $date,
                'user' => $userSearch,
            ],
        ]);
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $reservation = Reservation::query()->findOrFail($id);

        $user = $request->user();
        $isAdmin = $user->isAdmin();

        if (! $isAdmin && $reservation->user_id !== $user->id) {
            abort(403);
        }

        $reservation->delete();

        return back()->with('success', 'Reservation canceled.');
    }

    public function roomBookedSlots(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $slots = Reservation::query()
            ->where('room_id', $validated['room_id'])
            ->whereDate('date', $validated['date'])
            ->orderBy('start_time')
            ->get(['start_time', 'end_time']);

        return response()->json([
            'slots' => $slots->map(fn (Reservation $r): array => [
                'start' => substr((string) $r->start_time, 0, 5),
                'end' => substr((string) $r->end_time, 0, 5),
            ])->values(),
        ]);
    }

    protected function validateReservationData(Request $request, ?Reservation $existing = null): array
    {
        $validated = $request->validate([
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ], [
            'end_time.after' => 'End time must be after start time',
        ]);

        $legacy = [];
        if ($existing !== null) {
            $legacy = [
                substr((string) $existing->start_time, 0, 5),
                substr((string) $existing->end_time, 0, 5),
            ];
        }

        $bookingErrors = ReservationBookingWindow::validationErrors(
            $validated['start_time'],
            $validated['end_time'],
            $legacy,
        );

        if ($bookingErrors !== []) {
            throw ValidationException::withMessages($bookingErrors);
        }

        $validated['start_time'] .= ':00';
        $validated['end_time'] .= ':00';

        return $validated;
    }

    protected function overlappingReservationsForValidated(array $validated, ?int $ignoreReservationId = null): Collection
    {
        return Reservation::query()
            ->when($ignoreReservationId, fn (Builder $query) => $query->whereKeyNot($ignoreReservationId))
            ->where('room_id', $validated['room_id'])
            ->whereDate('date', $validated['date'])
            ->where(function (Builder $query) use ($validated) {
                $query
                    ->where('start_time', '<', $validated['end_time'])
                    ->where('end_time', '>', $validated['start_time']);
            })
            ->orderBy('start_time')
            ->get(['start_time', 'end_time']);
    }

    protected function reservationConflictExists(array $validated, ?int $ignoreReservationId = null): bool
    {
        return $this->overlappingReservationsForValidated($validated, $ignoreReservationId)->isNotEmpty();
    }
}

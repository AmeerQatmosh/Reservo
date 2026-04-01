<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Support\ReservationBookingWindow;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        return view('reservations.my', [
            'reservations' => $reservations,
            'rooms' => $rooms,
            'prefill' => [
                'room_id' => $request->query('room_id'),
                'date' => $request->query('date'),
            ],
            'prefilledRoom' => $prefilledRoom,
        ]);
    }

    public function edit(Request $request, int $id): View
    {
        $reservation = Reservation::query()
            ->where('user_id', $request->user()->id)
            ->with('room')
            ->findOrFail($id);

        $rooms = Room::query()
            ->orderBy('name')
            ->get();

        return view('reservations.edit', [
            'reservation' => $reservation,
            'rooms' => $rooms,
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
            return back()
                ->withErrors(['overlap' => 'This time slot is already booked'])
                ->withInput();
        }

        return back()->with('success', 'Reservation created.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $reservation = Reservation::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        $validated = $this->validateReservationData($request, $reservation);

        $updated = DB::transaction(function () use ($reservation, $validated) {
            if ($this->reservationConflictExists($validated, $reservation->id)) {
                return false;
            }

            $reservation->update($validated);

            return true;
        }, 5);

        if (! $updated) {
            return back()
                ->withErrors(['overlap' => 'This time slot is already booked'])
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
            return back()
                ->withErrors(['overlap' => 'This time slot is already booked'])
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

        return view('admin.reservations.index', [
            'reservations' => $reservations,
            'rooms' => $rooms,
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

    protected function reservationConflictExists(array $validated, ?int $ignoreReservationId = null): bool
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
            ->exists();
    }
}


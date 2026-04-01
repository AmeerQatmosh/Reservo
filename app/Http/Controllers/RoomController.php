<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Support\RoomListing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(): View
    {
        $query = Room::query();
        $filters = RoomListing::applyRequestFilters($query, request());
        $filterOptions = RoomListing::filterOptions();

        return view('rooms.index', [
            'rooms' => $query->paginate(12)->withQueryString(),
            'filters' => $filters,
            'filterOptions' => $filterOptions,
        ]);
    }

    public function show(int $id): View
    {
        $room = Room::query()->findOrFail($id);

        $selectedDate = request('date') ?: now()->toDateString();

        $bookedSlots = $room->reservations()
            ->whereDate('date', $selectedDate)
            ->orderBy('start_time')
            ->get();

        return view('rooms.show', [
            'room' => $room,
            'selectedDate' => $selectedDate,
            'bookedSlots' => $bookedSlots,
        ]);
    }

    public function adminIndex(): View
    {
        $status = request('status', 'all');

        $query = Room::query()->withTrashed();
        $filters = RoomListing::applyRequestFilters($query, request());

        $query
            ->when($status === 'active', fn ($q) => $q->whereNull('deleted_at'))
            ->when($status === 'deleted', fn ($q) => $q->onlyTrashed());

        $filters['status'] = $status;

        return view('admin.rooms.index', [
            'rooms' => $query->paginate(20)->withQueryString(),
            'filters' => $filters,
            'filterOptions' => RoomListing::filterOptions(withTrashed: true),
        ]);
    }

    public function adminShow(int $id): View
    {
        $room = Room::query()->withTrashed()->findOrFail($id);

        return view('admin.rooms.show', [
            'room' => $room,
        ]);
    }

    public function create(): View
    {
        return view('admin.rooms.create');
    }

    public function store(Request $request): RedirectResponse
    {
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

        $validated['amenities'] = Room::parseAmenitiesText($validated['amenities_text'] ?? null);
        unset($validated['amenities_text']);

        if (array_key_exists('hourly_rate', $validated) && $validated['hourly_rate'] === '') {
            $validated['hourly_rate'] = null;
        }

        Room::query()->create($validated);

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Room created.');
    }

    public function edit(int $id): View
    {
        $room = Room::query()->withTrashed()->findOrFail($id);

        return view('admin.rooms.edit', [
            'room' => $room,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $room = Room::query()->withTrashed()->findOrFail($id);

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

        $validated['amenities'] = Room::parseAmenitiesText($validated['amenities_text'] ?? null);
        unset($validated['amenities_text']);

        if (array_key_exists('hourly_rate', $validated) && $validated['hourly_rate'] === '') {
            $validated['hourly_rate'] = null;
        }

        $room->update($validated);

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Room updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $room = Room::query()->withTrashed()->findOrFail($id);

        $room->delete();

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Room deleted.');
    }

    public function restore(int $id): RedirectResponse
    {
        $room = Room::query()->withTrashed()->findOrFail($id);

        if ($room->trashed()) {
            $room->restore();
        }

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Room restored.');
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Support\RoomBookingDayAvailability;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoomBrowseController extends Controller
{
    public function favorites(Request $request): View
    {
        $rooms = $request->user()
            ->favoriteRooms()
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $browseDate = now()->toDateString();

        /** @var list<int> $favoriteRoomIds */
        $favoriteRoomIds = $rooms->pluck('id')->map(static fn ($id): int => (int) $id)->values()->all();

        return view('rooms.favorites', [
            'rooms' => $rooms,
            'browseDate' => $browseDate,
            'favoriteRoomIds' => $favoriteRoomIds,
        ]);
    }

    public function toggleFavorite(Request $request, Room $room): RedirectResponse
    {
        $request->user()->favoriteRooms()->toggle([$room->id]);

        return back();
    }

    public function quickBook(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $dateString = isset($validated['date'])
            ? (string) $validated['date']
            : now()->toDateString();

        if (! RoomBookingDayAvailability::hasAnyBookableSlot($room, $dateString)) {
            return back()->with(
                'warning',
                __('This room has no available time window on :date—you can choose another date in My reservations or open the room details to browse other days.', [
                    'date' => $dateString,
                ]),
            );
        }

        return redirect()->route('reservations.my', [
            'room_id' => $room->id,
            'date' => $dateString,
        ]);
    }
}

@extends('layouts.app')

@section('title', 'Admin Reservations')

@section('content')
    <x-page-breadcrumbs
        class="mb-4"
        :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Admin · Reservations'],
        ]"
    />
    <h1 class="text-xl font-semibold sm:text-2xl">Admin · Reservations</h1>

    <div class="mt-6 rounded-2xl border border-white/70 bg-white/90 p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.reservations.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-[minmax(220px,1fr)_minmax(200px,0.9fr)_minmax(0,1.2fr)_auto] xl:items-end">
            <div>
                <label for="room_id" class="block text-sm font-medium text-gray-900">Room</label>
                <select id="room_id" name="room_id" class="app-field">
                    <option value="">All rooms</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" @selected((string) ($filters['room_id'] ?? '') === (string) $room->id)>{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="date" class="block text-sm font-medium text-gray-900">Date</label>
                <input id="date" name="date" type="date" value="{{ $filters['date'] ?? '' }}" class="app-field">
            </div>

            <div>
                <label for="user" class="block text-sm font-medium text-gray-900">User</label>
                <input id="user" name="user" type="text" value="{{ $filters['user'] ?? '' }}" placeholder="Name or email" class="app-field">
            </div>

            <div class="flex flex-row flex-wrap items-end gap-2 xl:flex-nowrap xl:justify-end">
                <button
                    type="submit"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/35"
                >
                    <x-lucide name="filter" class="h-4 w-4 shrink-0" aria-hidden="true" />
                    Filter
                </button>
                <a
                    href="{{ route('admin.reservations.index') }}"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:border-gray-400 hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/15"
                >
                    <x-lucide name="rotate-ccw" class="h-4 w-4 shrink-0" aria-hidden="true" />
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if ($reservations->count() === 0)
        <div class="mt-6 rounded-lg border bg-white p-6 text-sm text-gray-700">
            No reservations match the current filters.
        </div>
    @else
        <div class="mt-6 overflow-hidden rounded-lg border bg-white">
            <div class="app-table-scroll app-table-scroll--wide">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3 w-24">Room</th>
                        <th class="px-4 py-3">Room details</th>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Time</th>
                        <th class="px-4 py-3 w-24">Est.</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($reservations as $reservation)
                        <tr class="align-top">
                            <td class="px-4 py-3">
                                @if ($reservation->room)
                                    <a href="{{ route('admin.rooms.show', $reservation->room->id) }}" class="block h-14 w-[4.5rem] overflow-hidden rounded-lg bg-gray-100 ring-1 ring-gray-200/80">
                                        @if ($reservation->room->image_url)
                                            <img src="{{ $reservation->room->image_url }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                        @else
                                            <span class="flex h-full items-center justify-center text-[10px] text-gray-400">—</span>
                                        @endif
                                    </a>
                                @else
                                    <div class="flex h-14 w-[4.5rem] items-center justify-center rounded-lg bg-gray-100 text-[10px] text-gray-400">—</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                @if ($reservation->room)
                                    <a href="{{ route('admin.rooms.show', $reservation->room->id) }}" class="font-semibold hover:underline">{{ $reservation->room->name }}</a>
                                    @if ($reservation->room->location)
                                        <div class="mt-1 max-w-xs text-xs font-normal text-gray-600">{{ $reservation->room->location }}</div>
                                    @endif
                                    <div class="mt-1 text-xs font-normal text-gray-500">
                                        {{ $reservation->room->capacity }} people
                                        @if ($reservation->room->size_sqm)
                                            · {{ $reservation->room->size_sqm }} m²
                                        @endif
                                    </div>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-gray-900">{{ $reservation->user?->name ?? '—' }}</div>
                                <div class="text-xs text-gray-600">{{ $reservation->user?->email ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $reservation->date }}</td>
                            <td class="px-4 py-3">{{ substr($reservation->start_time, 0, 5) }}–{{ substr($reservation->end_time, 0, 5) }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $reservation->estimatedTotalLabel() ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap justify-end gap-2">
                                <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="app-table-action">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.reservations.destroy', $reservation->id) }}" class="inline-flex" data-confirm-message="Are you sure you want to cancel this reservation?" data-confirm-variant="danger" data-confirm-button-label="Cancel reservation">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="app-table-action app-table-action-danger">
                                        Cancel
                                    </button>
                                </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $reservations->links() }}
        </div>
    @endif
@endsection


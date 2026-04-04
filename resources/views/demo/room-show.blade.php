@extends('layouts.app')

@section('title', $room['name'])

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Room details</div>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">{{ $room['name'] }}</h1>
            @if (! empty($room['location']))
                <p class="mt-2 text-sm text-gray-600">{{ $room['location'] }}</p>
            @endif
            <div class="mt-3 flex flex-wrap gap-2">
                <span class="inline-flex rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-medium text-gray-700">
                    Capacity: {{ $room['capacity'] }} people
                </span>
                @if (! empty($room['size_sqm']))
                    <span class="inline-flex rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-700">
                        {{ $room['size_sqm'] }} m²
                    </span>
                @endif
                @if ($lbl = \App\Support\DemoState::hourlyRateLabel(isset($room['hourly_rate']) ? (float) $room['hourly_rate'] : null))
                    <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-800">
                        {{ $lbl }} <span class="font-normal text-emerald-700">· estimate only</span>
                    </span>
                @endif
            </div>
        </div>
        <div class="flex shrink-0 flex-col items-end gap-2 sm:pt-1">
            <a href="{{ route('demo.rooms') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Back to rooms</a>
            <a href="{{ route('demo.hub') }}" class="text-sm font-medium text-gray-500 hover:text-gray-800">Sandbox home</a>
        </div>
    </div>

    <div class="mt-8 overflow-hidden rounded-3xl border border-white/70 bg-white shadow-sm">
        <div class="aspect-[21/9] max-h-[24rem] w-full overflow-hidden bg-gray-100 sm:aspect-[2.4/1]">
            @if (! empty($room['image_url']))
                <img src="{{ $room['image_url'] }}" alt="{{ $room['name'] }}" class="h-full w-full object-cover" loading="eager">
            @else
                <div class="flex h-full min-h-[12rem] w-full items-center justify-center bg-gradient-to-br from-slate-100 to-indigo-50 text-sm text-gray-500">
                    No photo for this room
                </div>
            @endif
        </div>
    </div>

    @php
        $amenities = is_array($room['amenities'] ?? null) ? $room['amenities'] : [];
    @endphp
    @if (count($amenities))
        <div class="mt-8 rounded-3xl border border-white/70 bg-white/90 p-7 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-[0.15em] text-gray-500">Amenities &amp; equipment</h2>
            <ul class="mt-4 flex flex-wrap gap-2">
                @foreach ($amenities as $amenity)
                    <li class="rounded-full border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm text-gray-800">{{ $amenity }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-8 rounded-3xl border border-white/70 bg-white/90 p-7 shadow-sm">
        <h2 class="text-sm font-semibold uppercase tracking-[0.15em] text-gray-500">About this space</h2>
        <div class="prose mt-4 max-w-none">
            <p class="whitespace-pre-line text-gray-800">{{ $room['description'] ?? '' }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-3xl border border-white/70 bg-white/90 p-7 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold tracking-tight text-gray-900">Availability on a date</h2>
                <p class="mt-1 text-sm text-gray-700">
                    Select a date to see booked time slots, then open <span class="font-medium text-gray-900">My reservations</span> to book—same steps as the real app.
                    New bookings use {{ \App\Support\ReservationBookingWindow::hoursSummary() }} ({{ \App\Support\ReservationBookingWindow::slotStepLabel() }} steps).
                </p>
            </div>
        </div>

        <form method="GET" action="{{ route('demo.room.show', $room['id']) }}" class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-end">
            <div>
                <label for="date" class="block text-sm font-medium text-gray-900">Date</label>
                <input id="date" name="date" type="date" value="{{ $selectedDate }}" min="{{ now()->toDateString() }}" class="app-field">
            </div>

            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gray-900 px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800">
                <x-lucide name="calendar-search" class="h-4 w-4 shrink-0 opacity-90" />
                Check availability
            </button>
        </form>

        <div
            id="room-availability-results"
            tabindex="-1"
            class="scroll-mt-28 mt-6 rounded-2xl border border-gray-200 bg-gray-50/90 p-5 outline-none focus-visible:ring-2 focus-visible:ring-gray-900/20 focus-visible:ring-offset-2"
        >
            <div class="text-sm font-medium text-gray-900">
                Booked slots for {{ $selectedDate }}
            </div>

            @if (count($booked) === 0)
                <div class="mt-2 text-sm text-green-700">
                    No reservations on this date in the sandbox. The room is free for new bookings.
                </div>
            @else
                <ul class="mt-3 space-y-2">
                    @foreach ($booked as $reservation)
                        <li class="rounded-2xl border border-white bg-white px-4 py-3 text-sm text-gray-800 shadow-sm">
                            {{ substr($reservation['start_time'], 0, 5) }}–{{ substr($reservation['end_time'], 0, 5) }} booked
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="mt-5 flex flex-col gap-3 rounded-2xl border border-blue-200 bg-blue-50 p-5 text-sm text-blue-900 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="font-medium">Ready to book?</div>
                <div class="mt-1 text-blue-800">Open My reservations with this room and date pre-filled—then choose your time range.</div>
                @if ($lbl = \App\Support\DemoState::hourlyRateLabel(isset($room['hourly_rate']) ? (float) $room['hourly_rate'] : null))
                    <div class="mt-2 text-xs text-blue-800/90">
                        Rate: {{ $lbl }} — totals on the booking form are estimates; no payment in the sandbox.
                    </div>
                @endif
            </div>
            <a href="{{ route('demo.reservations.my', ['room_id' => $room['id'], 'date' => $selectedDate]) }}" class="reservo-loading-link inline-flex items-center justify-center gap-2 rounded-2xl bg-gray-900 px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800">
                <x-lucide name="calendar-check" class="h-4 w-4 shrink-0 opacity-90" />
                Book this room
            </a>
        </div>
    </div>
@endsection

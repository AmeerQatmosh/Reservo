@extends('layouts.app')

@section('title', $room->name)

@section('content')
    @php
        $minDateStr = now()->toDateString();
        $availabilityCalendarDate = max($selectedDate, $minDateStr);
    @endphp
    <x-page-breadcrumbs
        class="mb-4"
        :items="[
            ['label' => 'Rooms', 'url' => route('rooms.index')],
            ['label' => $room->name],
        ]"
    />

    {{-- Full-width heading above the split so calendar aligns with photo (not title) --}}
    <header class="max-w-4xl">
        <div class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Room details</div>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">{{ $room->name }}</h1>
        @if ($room->location)
            <p class="mt-2 text-sm text-gray-600">{{ $room->location }}</p>
        @endif
        <div class="mt-4 flex flex-wrap gap-2">
            <span class="inline-flex rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-medium text-gray-700">
                Capacity: {{ $room->capacity }} people
            </span>
            @if ($room->size_sqm)
                <span class="inline-flex rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-700">
                    {{ $room->size_sqm }} m²
                </span>
            @endif
            @if ($room->hourly_rate !== null)
                <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-800">
                    {{ $room->hourlyRateLabel() }} <span class="font-normal text-emerald-700">· estimate only</span>
                </span>
            @endif
        </div>
    </header>

    {{-- Photo + descriptive cards (left) | booking stack (right), tops aligned --}}
    <div class="mt-10 flex flex-col gap-10 lg:flex-row lg:items-start lg:gap-10 xl:gap-14">
        {{-- Left: room narrative --}}
        <div class="min-w-0 flex-1 space-y-8">
            <div class="overflow-hidden rounded-3xl border border-white/70 bg-white shadow-sm">
                <div class="aspect-[5/4] max-h-[22rem] w-full overflow-hidden bg-gray-100 sm:aspect-[16/10] lg:max-h-none">
                    @if ($room->image_url)
                        <img src="{{ $room->image_url }}" alt="{{ $room->name }}" class="h-full w-full object-cover" loading="eager">
                    @else
                        <div class="flex h-full min-h-[12rem] w-full items-center justify-center text-sm text-gray-500">
                            No photo for this room yet
                        </div>
                    @endif
                </div>
            </div>

            @if (is_array($room->amenities) && count($room->amenities))
                <div class="rounded-3xl border border-white/70 bg-white/90 p-7 shadow-sm">
                    <h2 class="text-sm font-semibold uppercase tracking-[0.15em] text-gray-500">Amenities &amp; equipment</h2>
                    <ul class="mt-4 flex flex-wrap gap-2">
                        @foreach ($room->amenities as $amenity)
                            <li class="rounded-full border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm text-gray-800">{{ $amenity }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-3xl border border-white/70 bg-white/90 p-7 shadow-sm">
                <h2 class="text-sm font-semibold uppercase tracking-[0.15em] text-gray-500">About this space</h2>
                <div class="prose mt-4 max-w-none">
                    <p class="whitespace-pre-line text-gray-800">{{ $room->description }}</p>
                </div>
            </div>
        </div>

        {{-- Right: availability & booking --}}
        <aside class="w-full shrink-0 lg:w-[min(100%,26rem)] lg:max-w-[26rem]" aria-label="{{ __('Booking and availability') }}">
            <div class="space-y-5 lg:sticky lg:top-24">
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-sm sm:p-7">
                    <h2 class="text-lg font-semibold tracking-tight text-gray-900">Availability on a date</h2>
                    <p class="mt-2 text-sm text-gray-700">
                        Pick a date to see what is already booked, then reserve your slot.
                        New bookings use {{ \App\Support\ReservationBookingWindow::hoursSummary() }} ({{ \App\Support\ReservationBookingWindow::slotStepLabel() }} steps).
                    </p>

                    <form method="GET" action="{{ route('rooms.show', $room->id) }}" class="mt-5">
                        <div class="mx-auto flex w-full min-w-0 flex-col gap-4 sm:mx-0">
                            <x-reservation-date-mini-calendar
                                class="w-full max-w-none"
                                :value="$availabilityCalendarDate"
                                :min="$minDateStr"
                                input-id="availability_date"
                                name="date"
                            />
                            <button type="submit" class="inline-flex w-full shrink-0 items-center justify-center gap-2 rounded-2xl bg-gray-900 px-5 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800">
                                <x-lucide name="calendar-search" class="h-4 w-4 shrink-0 opacity-90" />
                                Check Availability
                            </button>
                        </div>
                    </form>
                </div>

                <div
                    id="room-availability-results"
                    tabindex="-1"
                    class="scroll-mt-28 rounded-2xl border border-gray-200 bg-gray-50/90 p-5 outline-none focus-visible:ring-2 focus-visible:ring-gray-900/20 focus-visible:ring-offset-2"
                >
                    <div class="text-sm font-medium text-gray-900">
                        Booked slots for {{ $selectedDate }}
                    </div>

                    @if ($bookedSlots->isEmpty())
                        <div class="mt-2 text-sm text-green-700">
                            No reservations on this date within our system. The room may still be bookable during operating hours.
                        </div>
                    @else
                        <ul class="mt-3 space-y-2">
                            @foreach ($bookedSlots as $reservation)
                                <li class="rounded-2xl border border-white bg-white px-4 py-3 text-sm text-gray-800 shadow-sm">
                                    {{ substr($reservation->start_time, 0, 5) }}–{{ substr($reservation->end_time, 0, 5) }} booked
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                @auth
                    <div class="flex flex-col gap-3 rounded-2xl border border-blue-200 bg-blue-50 p-5 text-sm text-blue-900">
                        <div>
                            <div class="font-medium">Ready to book?</div>
                            <div class="mt-1 text-blue-800">Use the selected date below—your reservation form opens with this room already chosen.</div>
                            @if ($room->hourly_rate !== null)
                                <div class="mt-2 text-xs text-blue-800/90">
                                    Rate: {{ $room->hourlyRateLabel() }} — totals on the booking form are estimates; Reservo does not take payment.
                                </div>
                            @endif
                        </div>
                        <a href="{{ route('reservations.my', ['room_id' => $room->id, 'date' => $selectedDate]) }}" class="reservo-loading-link inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gray-900 px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800">
                            <x-lucide name="calendar-check" class="h-4 w-4 shrink-0 opacity-90" />
                            Book this Room
                        </a>
                    </div>
                @else
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5 text-center text-sm text-gray-700">
                        <a href="{{ route('login') }}" class="font-medium text-gray-900 underline">Log in</a> to create a reservation.
                    </div>
                @endauth
            </div>
        </aside>
    </div>
@endsection

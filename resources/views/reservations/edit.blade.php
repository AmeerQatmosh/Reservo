@extends('layouts.app')

@section('title', 'Edit Reservation')

@section('content')
    @php
        $currentRoomId = old('room_id', $reservation->room_id);
        $currentDate = old('date', $reservation->date);
        $currentStart = old('start_time', substr($reservation->start_time, 0, 5));
        $currentEnd = old('end_time', substr($reservation->end_time, 0, 5));
        $timeSlots = \App\Support\ReservationBookingWindow::selectOptions([$currentStart, $currentEnd]);
    @endphp

    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Reservation</div>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">Edit Reservation</h1>
            <p class="mt-2 text-sm text-gray-600">Review the current booking details, then update the date, room, or time.</p>
        </div>
        <a href="{{ route('reservations.my') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Back to My Reservations</a>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2 lg:items-start">
        <div class="rounded-3xl border border-white/70 bg-white/90 p-7 shadow-sm">
            <div class="text-sm font-semibold uppercase tracking-[0.12em] text-gray-500">Reserved space</div>
            <p class="mt-1 text-sm text-gray-600">Photo, location, capacity, amenities, and full description for the room tied to this booking.</p>
            <div class="mt-6">
                @include('rooms.partials.detail-snippet', [
                    'room' => $reservation->room,
                    'imageWrapperClass' => 'h-48 w-full sm:h-56 sm:w-64',
                ])
            </div>
            <dl class="mt-6 grid gap-3 rounded-2xl border border-gray-100 bg-gray-50/90 p-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Booking date</dt>
                    <dd class="mt-0.5 font-medium text-gray-900">{{ $reservation->date }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Time window</dt>
                    <dd class="mt-0.5 font-medium text-gray-900">{{ substr($reservation->start_time, 0, 5) }}–{{ substr($reservation->end_time, 0, 5) }}</dd>
                </div>
                @if ($label = $reservation->estimatedTotalLabel())
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Estimated total</dt>
                        <dd class="mt-0.5 font-semibold text-emerald-800">{{ $label }} <span class="text-xs font-normal text-gray-600">(estimate only · no payment in app)</span></dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="rounded-3xl border border-white/70 bg-white/90 p-7 shadow-sm">
            <div class="text-sm font-medium text-gray-900">Update your choices</div>
            <p class="mt-2 text-sm text-gray-700">
                Your current selections are pre-filled below. After you save, the new values become your current reservation.
                Bookings are limited to {{ \App\Support\ReservationBookingWindow::hoursSummary() }} in {{ \App\Support\ReservationBookingWindow::slotStepLabel() }} steps unless you keep this reservation’s original times.
            </p>

            <form method="POST" action="{{ route('reservations.update', $reservation->id) }}" class="mt-6 space-y-4" id="editReservationForm" novalidate>
                @csrf
                @method('PUT')

                <div>
                    <label for="room_id" class="block text-sm font-medium text-gray-900">Room</label>
                    <select id="room_id" name="room_id" class="app-field">
                        <option value="">Select a room</option>
                        @foreach ($rooms as $room)
                            <option
                                value="{{ $room->id }}"
                                data-hourly-rate="{{ $room->hourly_rate ?? '' }}"
                                @selected((string) $currentRoomId === (string) $room->id)
                            >{{ $room->name }}@if ($room->hourly_rate !== null) ({{ $room->hourlyRateLabel() }})@endif</option>
                        @endforeach
                    </select>
                    @error('room_id')
                        <div class="mt-1 text-xs text-red-700">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-gray-900">Date</label>
                    <input id="date" name="date" type="date" value="{{ $currentDate }}" class="app-field">
                    @error('date')
                        <div class="mt-1 text-xs text-red-700">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-900">Start</label>
                        <select id="start_time" name="start_time" class="app-field">
                            <option value="">Select start</option>
                            @foreach ($timeSlots as $slot)
                                <option value="{{ $slot }}" @selected($currentStart === $slot)>{{ $slot }}</option>
                            @endforeach
                        </select>
                        @error('start_time')
                            <div class="mt-1 text-xs text-red-700">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-900">End</label>
                        <select id="end_time" name="end_time" class="app-field">
                            <option value="">Select end</option>
                            @foreach ($timeSlots as $slot)
                                <option value="{{ $slot }}" @selected($currentEnd === $slot)>{{ $slot }}</option>
                            @endforeach
                        </select>
                        @error('end_time')
                            <div class="mt-1 text-xs text-red-700">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @error('overlap')
                    <div class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
                        {{ $message }}
                    </div>
                @enderror

                <p id="editReservationEstimate" class="hidden rounded-lg border border-emerald-200 bg-emerald-50/90 px-3 py-2 text-sm text-emerald-950" role="status"></p>

                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gray-900 px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50" id="editReservationSubmit">
                    <x-lucide name="square-pen" class="h-4 w-4 shrink-0 opacity-90" />
                    Save changes
                </button>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('editReservationForm');
            const submit = document.getElementById('editReservationSubmit');
            const room = document.getElementById('room_id');
            const date = document.getElementById('date');
            const start = document.getElementById('start_time');
            const end = document.getElementById('end_time');
            const estimateEl = document.getElementById('editReservationEstimate');

            if (!form || !submit || !room || !date || !start || !end) return;

            const selectedHourlyRate = () => {
                const opt = room.selectedOptions[0];
                const raw = opt?.dataset?.hourlyRate;
                if (raw === undefined || raw === '') return null;
                const n = Number.parseFloat(raw);
                return Number.isFinite(n) ? n : null;
            };

            const slotMinutes = (slot) => {
                const [h, m] = slot.split(':').map((x) => Number.parseInt(x, 10));
                return h * 60 + m;
            };

            const refreshEstimate = () => {
                if (!estimateEl) return;
                const rate = selectedHourlyRate();
                if (rate === null || !start.value || !end.value || end.value <= start.value) {
                    estimateEl.classList.add('hidden');
                    estimateEl.textContent = '';
                    return;
                }
                const hours = (slotMinutes(end.value) - slotMinutes(start.value)) / 60;
                const total = (hours * rate).toFixed(2);
                estimateEl.textContent =
                    'New estimate: $' +
                    total +
                    ' (' +
                    hours.toFixed(2) +
                    ' hr × $' +
                    rate.toFixed(2) +
                    '/hr). No payment in Reservo.';
                estimateEl.classList.remove('hidden');
            };

            const refreshEndOptions = () => {
                const startValue = start.value;
                const currentEnd = end.value;
                let hasValidEnd = false;

                for (const opt of end.options) {
                    if (!opt.value) continue;
                    opt.disabled = !!startValue && opt.value <= startValue;
                    if (!opt.disabled && opt.value === currentEnd) hasValidEnd = true;
                }

                if (currentEnd && !hasValidEnd) {
                    end.value = '';
                }
            };

            const refreshSubmitState = () => {
                const isValid =
                    !!room.value &&
                    !!date.value &&
                    !!start.value &&
                    !!end.value &&
                    end.value > start.value;

                submit.disabled = !isValid;
            };

            start.addEventListener('change', () => {
                refreshEndOptions();
                refreshSubmitState();
                refreshEstimate();
            });

            for (const el of [room, date, start, end]) {
                el.addEventListener('change', () => {
                    refreshSubmitState();
                    refreshEstimate();
                });
            }

            refreshEndOptions();
            refreshSubmitState();
            refreshEstimate();
        })();
    </script>
@endsection


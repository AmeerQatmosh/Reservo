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

    <x-page-breadcrumbs
        class="mb-4"
        :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Admin'],
            ['label' => 'Reservations', 'url' => route('admin.reservations.index')],
            ['label' => 'Edit reservation'],
        ]"
    />
    <div>
        <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Edit Reservation</h1>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2 lg:items-start">
        <div class="rounded-3xl border border-white/70 bg-white/90 p-7 shadow-sm">
            <div class="text-sm font-semibold uppercase tracking-[0.12em] text-gray-500">Booked space (admin)</div>
            <p class="mt-1 text-sm text-gray-600">Photo, location, capacity, amenities, and description for this room.</p>
            <div class="mt-4 rounded-2xl border border-gray-100 bg-gray-50/80 p-4 text-sm text-gray-800">
                <span class="font-medium text-gray-900">User:</span>
                {{ $reservation->user?->name ?? '—' }}
                @if ($reservation->user?->email)
                    <span class="text-gray-600">({{ $reservation->user->email }})</span>
                @endif
            </div>
            <div class="mt-5">
                @include('rooms.partials.detail-snippet', [
                    'room' => $reservation->room,
                    'imageWrapperClass' => 'h-44 w-full sm:h-52 sm:w-56 sm:shrink-0',
                    'showViewLink' => true,
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
                        <dd class="mt-0.5 font-semibold text-emerald-800">{{ $label }} <span class="text-xs font-normal text-gray-600">(estimate only)</span></dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="rounded-3xl border border-white/70 bg-white/90 p-7 shadow-sm">
            <div class="text-sm font-medium text-gray-900">Update reservation</div>
            <p class="mt-2 text-sm text-gray-700">
                The current room, date, and time are pre-filled below. After saving, the updated values become the reservation's current details.
                Same booking window as the member form: {{ \App\Support\ReservationBookingWindow::hoursSummary() }}, {{ \App\Support\ReservationBookingWindow::slotStepLabel() }} steps (original times on this booking stay selectable).
            </p>

            <form method="POST" action="{{ route('admin.reservations.update', $reservation->id) }}" class="mt-6 space-y-4" id="adminEditReservationForm" novalidate>
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

                <p id="adminEditReservationEstimate" class="hidden rounded-lg border border-emerald-200 bg-emerald-50/90 px-3 py-2 text-sm text-emerald-950" role="status"></p>

                <button type="submit" class="w-full rounded-2xl bg-teal-600 px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-50" id="adminEditReservationSubmit">
                    Save changes
                </button>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('adminEditReservationForm');
            const submit = document.getElementById('adminEditReservationSubmit');
            const room = document.getElementById('room_id');
            const date = document.getElementById('date');
            const start = document.getElementById('start_time');
            const end = document.getElementById('end_time');
            const estimateEl = document.getElementById('adminEditReservationEstimate');

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
                    'New estimate: $' + total + ' (' + hours.toFixed(2) + ' hr × $' + rate.toFixed(2) + '/hr).';
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


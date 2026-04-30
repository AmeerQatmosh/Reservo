@extends('layouts.app')

@section('title', 'Edit Reservation')

@section('content')
    @php
        $currentRoomId = old('room_id', $reservation->room_id);
        $currentDate = old('date', $reservation->date);
        $currentStart = old('start_time', substr($reservation->start_time, 0, 5));
        $currentEnd = old('end_time', substr($reservation->end_time, 0, 5));
        $timeSlots = \App\Support\ReservationBookingWindow::selectOptions([$currentStart, $currentEnd]);

        $editRoomHourlyForInput = null;
        if ((string) $currentRoomId !== '') {
            $er = $rooms->firstWhere('id', (int) $currentRoomId);
            $editRoomHourlyForInput = $er && $er->hourly_rate !== null ? (string) $er->hourly_rate : '';
        }

        $minDateStr = now()->toDateString();
        $displayDate = $currentDate !== '' ? max($currentDate, $minDateStr) : '';
    @endphp

    <x-page-breadcrumbs
        class="mb-4"
        :items="[
            ['label' => 'My reservations', 'url' => route('reservations.my')],
            ['label' => $reservation->room?->name ?? 'Edit booking'],
        ]"
    />

    <div>
        <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Edit Reservation</h1>
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
                    <x-reservo-form-select
                        name="room_id"
                        hidden-id="room_id"
                        trigger-id="room_id_trigger"
                        listbox-id="room_id_listbox"
                        label="Room"
                        placeholder="Select a room"
                        :value="(string) $currentRoomId"
                        :hourly-rate="$editRoomHourlyForInput"
                    >
                        <button
                            type="button"
                            role="option"
                            data-value=""
                            class="reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm text-gray-500 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2"
                            aria-selected="{{ (string) $currentRoomId === '' ? 'true' : 'false' }}"
                        >Select a room</button>
                        @foreach ($rooms as $room)
                            <button
                                type="button"
                                role="option"
                                data-value="{{ $room->id }}"
                                data-hourly-rate="{{ $room->hourly_rate ?? '' }}"
                                @class([
                                    'reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm break-words hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2',
                                    'bg-gray-100 font-medium text-gray-900' => (string) $currentRoomId === (string) $room->id,
                                    'text-gray-900' => (string) $currentRoomId !== (string) $room->id,
                                ])
                                aria-selected="{{ (string) $currentRoomId === (string) $room->id ? 'true' : 'false' }}"
                            >{{ $room->name }}@if ($room->hourly_rate !== null) ({{ $room->hourlyRateLabel() }})@endif</button>
                        @endforeach
                    </x-reservo-form-select>
                    @error('room_id')
                        <div class="mt-1 text-xs text-red-700">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="date" class="mb-2 block text-sm font-medium text-gray-900">Date</label>
                    <x-reservation-date-mini-calendar :value="$displayDate" :min="$minDateStr" :bookings="$miniCalendarBookings ?? []" />
                    @error('date')
                        <div class="mt-1 text-xs text-red-700">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-reservo-form-select
                            name="start_time"
                            hidden-id="start_time"
                            trigger-id="start_time_trigger"
                            listbox-id="start_time_listbox"
                            label="Start"
                            placeholder="Select start"
                            :value="(string) $currentStart"
                        >
                            <button
                                type="button"
                                role="option"
                                data-value=""
                                class="reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm text-gray-500 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2"
                                aria-selected="{{ (string) $currentStart === '' ? 'true' : 'false' }}"
                            >Select start</button>
                            @foreach ($timeSlots as $slot)
                                <button
                                    type="button"
                                    role="option"
                                    data-value="{{ $slot }}"
                                    @class([
                                        'reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm text-gray-900 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2',
                                        'bg-gray-100 font-medium' => (string) $currentStart === (string) $slot,
                                    ])
                                    aria-selected="{{ (string) $currentStart === (string) $slot ? 'true' : 'false' }}"
                                >{{ $slot }}</button>
                            @endforeach
                        </x-reservo-form-select>
                        @error('start_time')
                            <div class="mt-1 text-xs text-red-700">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <x-reservo-form-select
                            name="end_time"
                            hidden-id="end_time"
                            trigger-id="end_time_trigger"
                            listbox-id="end_time_listbox"
                            label="End"
                            placeholder="Select end"
                            :value="(string) $currentEnd"
                        >
                            <button
                                type="button"
                                role="option"
                                data-value=""
                                class="reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm text-gray-500 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2"
                                aria-selected="{{ (string) $currentEnd === '' ? 'true' : 'false' }}"
                            >Select end</button>
                            @foreach ($timeSlots as $slot)
                                <button
                                    type="button"
                                    role="option"
                                    data-value="{{ $slot }}"
                                    @class([
                                        'reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm text-gray-900 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2',
                                        'bg-gray-100 font-medium' => (string) $currentEnd === (string) $slot,
                                    ])
                                    aria-selected="{{ (string) $currentEnd === (string) $slot ? 'true' : 'false' }}"
                                >{{ $slot }}</button>
                            @endforeach
                        </x-reservo-form-select>
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

                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-teal-600 px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-50" id="editReservationSubmit">
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
                const raw = room.getAttribute('data-hourly-rate');
                if (raw === null || raw === '') return null;
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

                const panel = document.getElementById('end_time_listbox');
                if (!panel) return;

                for (const btn of panel.querySelectorAll('button.reservo-form-select__opt')) {
                    if (!(btn instanceof HTMLButtonElement)) continue;
                    const v = btn.dataset.value ?? '';
                    if (!v) {
                        btn.disabled = false;
                        btn.classList.remove('opacity-45', 'pointer-events-none', 'cursor-not-allowed');
                        continue;
                    }
                    const disabled = !!startValue && v <= startValue;
                    btn.disabled = disabled;
                    btn.classList.toggle('opacity-45', disabled);
                    btn.classList.toggle('pointer-events-none', disabled);
                    btn.classList.toggle('cursor-not-allowed', disabled);
                    if (!disabled && v === currentEnd) hasValidEnd = true;
                }

                if (currentEnd && !hasValidEnd) {
                    end.value = '';
                    end.dispatchEvent(new Event('change', { bubbles: true }));
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


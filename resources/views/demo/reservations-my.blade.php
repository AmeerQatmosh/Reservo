@extends('layouts.app')

@section('title', 'My Reservations')

@section('content')
    @php
        $selectedRoomId = old('room_id', $prefill['room_id'] ?? '');
        $selectedDate = old('date', $prefill['date'] ?? '');
        $prefilledRoom = $selectedRoomId !== '' ? \App\Support\DemoState::findRoom((int) $selectedRoomId) : null;
        $timeSlots = \App\Support\ReservationBookingWindow::selectOptions([]);
    @endphp

    <div class="flex flex-col gap-8 lg:flex-row">
        <div class="w-full lg:w-[24rem] lg:flex-none">
            <div>
                <div class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Sandbox</div>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">My Reservations</h1>
                <p class="mt-2 text-sm text-gray-600">
                    Same flow as the signed-in app: pick a room, date, and time range. Overlap rules and booking windows match production. Estimates use sample hourly rates—no payment.
                </p>
            </div>

            @if ($prefilledRoom || $selectedDate)
                <div class="mt-5 overflow-hidden rounded-2xl border border-blue-200 bg-blue-50 shadow-sm">
                    @if ($prefilledRoom)
                        <div class="relative aspect-[2/1] max-h-36 w-full bg-gray-200 sm:aspect-[2.5/1]">
                            @if (! empty($prefilledRoom['image_url']))
                                <img src="{{ $prefilledRoom['image_url'] }}" alt="" class="h-full w-full object-cover" loading="lazy">
                            @else
                                <div class="flex h-full items-center justify-center text-xs text-gray-500">No photo</div>
                            @endif
                        </div>
                    @endif
                    <div class="p-5">
                        <div class="text-sm font-medium text-blue-900">Booking summary</div>
                        <div class="mt-2 space-y-1 text-sm text-blue-800">
                            @if ($prefilledRoom)
                                <div>
                                    <span class="font-medium">Room:</span>
                                    {{ $prefilledRoom['name'] }}
                                </div>
                                @if (! empty($prefilledRoom['location']))
                                    <div class="text-blue-800/90">{{ $prefilledRoom['location'] }}</div>
                                @endif
                                <div class="text-xs text-blue-800/80">
                                    Up to {{ $prefilledRoom['capacity'] }} people
                                    @if (! empty($prefilledRoom['size_sqm']))
                                        · {{ $prefilledRoom['size_sqm'] }} m²
                                    @endif
                                    @if ($lbl = \App\Support\DemoState::hourlyRateLabel(isset($prefilledRoom['hourly_rate']) ? (float) $prefilledRoom['hourly_rate'] : null))
                                        · {{ $lbl }}
                                    @endif
                                </div>
                            @endif
                            @if ($selectedDate)
                                <div>
                                    <span class="font-medium">Date:</span>
                                    {{ $selectedDate }}
                                </div>
                            @endif
                        </div>
                        @if ($prefilledRoom)
                            <a href="{{ route('demo.room.show', $prefilledRoom['id']) }}" class="mt-3 inline-block text-sm font-medium text-blue-900 underline decoration-blue-300 hover:text-blue-950">
                                View full room details
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <div class="mt-5 rounded-3xl border border-white/70 bg-white/90 p-6 shadow-sm">
                <div class="mb-5">
                    <h2 class="text-base font-semibold text-gray-900">Create reservation</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Choose a room, pick a date, then select a time range.
                        <span class="block text-gray-500">Bookable {{ \App\Support\ReservationBookingWindow::hoursSummary() }} · {{ \App\Support\ReservationBookingWindow::slotStepLabel() }} slot grid.</span>
                    </p>
                </div>

                <form method="POST" action="{{ route('demo.reservations.store') }}" class="space-y-4" id="demoReservationForm" novalidate>
                    @csrf
                    <input type="hidden" name="after_store" value="my">

                    <div>
                        <label for="room_id" class="block text-sm font-medium text-gray-900">Room</label>
                        <select id="room_id" name="room_id" class="app-field">
                            <option value="">Select a room</option>
                            @foreach ($rooms as $room)
                                @php
                                    $rate = $room['hourly_rate'] ?? null;
                                @endphp
                                <option
                                    value="{{ $room['id'] }}"
                                    data-hourly-rate="{{ $rate !== null && $rate !== '' ? $rate : '' }}"
                                    @selected((string) $selectedRoomId === (string) $room['id'])
                                >{{ $room['name'] }}@if ($lbl = \App\Support\DemoState::hourlyRateLabel($rate !== null ? (float) $rate : null)) ({{ $lbl }})@endif</option>
                            @endforeach
                        </select>
                        @error('room_id')
                            <div class="mt-1 text-xs text-red-700">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-900">Date</label>
                        <input id="date" name="date" type="date" value="{{ $selectedDate }}" min="{{ now()->toDateString() }}" class="app-field">
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
                                    <option value="{{ $slot }}" @selected(old('start_time') === $slot)>{{ $slot }}</option>
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
                                    <option value="{{ $slot }}" @selected(old('end_time') === $slot)>{{ $slot }}</option>
                                @endforeach
                            </select>
                            @error('end_time')
                                <div class="mt-1 text-xs text-red-700">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @error('time')
                        <div class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
                            {{ $message }}
                        </div>
                    @enderror
                    @error('overlap')
                        <div class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
                            {{ $message }}
                        </div>
                    @enderror

                    <p id="demoReservationEstimate" class="hidden rounded-lg border border-emerald-200 bg-emerald-50/90 px-3 py-2 text-sm text-emerald-950" role="status"></p>

                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gray-900 px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50" id="demoReservationSubmit">
                        <x-lucide name="calendar-plus" class="h-4 w-4 shrink-0 opacity-90" />
                        Create reservation
                    </button>
                </form>
            </div>
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">History</h2>
                    <p class="mt-1 text-sm text-gray-600">Your sandbox bookings for this browser session.</p>
                </div>
            </div>

            @if (count($reservations) === 0)
                <div class="mt-5 rounded-3xl border border-white/70 bg-white/90 p-6 text-sm text-gray-700 shadow-sm">
                    You have no reservations in this sandbox yet.
                </div>
            @else
                <div class="mt-5 space-y-6">
                    @foreach ($reservations as $reservation)
                        @php
                            $resRoom = \App\Support\DemoState::findRoom((int) ($reservation['room_id'] ?? 0));
                        @endphp
                        <article class="overflow-hidden rounded-3xl border border-white/70 bg-white/90 shadow-sm">
                            <div class="border-b border-gray-100 bg-gray-50/80 px-4 py-3 sm:px-5">
                                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                                    <div class="text-sm text-gray-800">
                                        <span class="font-semibold text-gray-900">{{ $reservation['date'] }}</span>
                                        <span class="text-gray-400"> · </span>
                                        <span>{{ substr($reservation['start_time'], 0, 5) }}–{{ substr($reservation['end_time'], 0, 5) }}</span>
                                        @if ($est = \App\Support\DemoState::reservationEstimateLabel($reservation, $resRoom))
                                            <span class="text-gray-400"> · </span>
                                            <span class="font-medium text-emerald-800">Est. {{ $est }}</span>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('demo.reservations.destroy', $reservation['id']) }}" class="inline-flex" data-confirm-message="Cancel this sandbox reservation?" data-confirm-variant="danger" data-confirm-button-label="Cancel reservation">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="app-table-action app-table-action-danger">
                                            <x-lucide name="circle-x" class="h-4 w-4 shrink-0" />
                                            Cancel
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="p-5 sm:p-6">
                                @if ($resRoom)
                                    @include('demo.partials.room-detail', ['room' => $resRoom])
                                @else
                                    <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50/80 p-6 text-sm text-gray-600">
                                        This room was removed from the sandbox.
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('demoReservationForm');
            const submit = document.getElementById('demoReservationSubmit');
            const room = document.getElementById('room_id');
            const date = document.getElementById('date');
            const start = document.getElementById('start_time');
            const end = document.getElementById('end_time');
            const estimateEl = document.getElementById('demoReservationEstimate');

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
                    'Estimated total: $' +
                    total +
                    ' (' +
                    hours.toFixed(2) +
                    ' hr × $' +
                    rate.toFixed(2) +
                    '/hr). Sandbox only—no payment.';
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

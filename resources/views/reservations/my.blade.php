@extends('layouts.app')

@section('title', 'My Reservations')

@section('content')
    @php
        $selectedRoomId = old('room_id', $prefill['room_id'] ?? '');
        $selectedDate = old('date', $prefill['date'] ?? '');
        $minDateStr = now()->toDateString();
        $displayDate = $selectedDate !== '' ? max($selectedDate, $minDateStr) : '';
        $bookingSummaryInitiallyVisible = (bool) ($prefilledRoom || $displayDate);

        $reservePrefillQs = array_filter([
            'room_id' => request()->query('room_id'),
            'date' => request()->query('date'),
        ], fn ($v) => $v !== null && $v !== '');
        $historyTabUrl = route('reservations.my', $reservePrefillQs);

        $calendarTabParams = array_merge(['view' => 'calendar'], $reservePrefillQs);
        if (request()->filled('year')) {
            $calendarTabParams['year'] = request()->query('year');
        }
        if (request()->filled('month')) {
            $calendarTabParams['month'] = request()->query('month');
        }
        $calendarTabUrl = route('reservations.my', $calendarTabParams);
    @endphp

    <x-page-breadcrumbs
        class="mb-6"
        :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'My reservations'],
        ]"
    />

    <div class="flex flex-col gap-8 lg:flex-row">
        <div class="w-full lg:w-[24rem] lg:flex-none">
            <div>
                <div class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Reservations</div>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">My Reservations</h1>
                <p class="mt-2 text-sm text-gray-600">
                    Create a reservation by selecting a room, date, and time range. If the room has an hourly rate, you’ll see an estimated total—there is no checkout in Reservo.
                </p>
            </div>

            <script type="application/json" id="rooms-booking-summary-meta">@json($roomsBookingSummaryMeta ?? [])</script>

            <div
                id="booking-summary-panel"
                role="region"
                aria-labelledby="booking-summary-heading"
                class="mt-5 overflow-hidden rounded-2xl border border-blue-200 bg-blue-50 shadow-sm {{ $bookingSummaryInitiallyVisible ? '' : 'hidden' }}"
                aria-hidden="{{ $bookingSummaryInitiallyVisible ? 'false' : 'true' }}"
            >
                <div
                    id="booking-summary-photo-wrap"
                    class="relative aspect-[2/1] max-h-36 w-full bg-gray-200 sm:aspect-[2.5/1] {{ $prefilledRoom ? '' : 'hidden' }}"
                >
                    <img
                        id="booking-summary-photo-img"
                        src="{{ $prefilledRoom?->image_url ?: '' }}"
                        alt=""
                        class="{{ $prefilledRoom?->image_url ? 'h-full w-full object-cover' : 'hidden h-full w-full object-cover' }}"
                        loading="lazy"
                    >
                    <div id="booking-summary-photo-empty" class="{{ $prefilledRoom && ! $prefilledRoom->image_url ? 'flex h-full items-center justify-center text-xs text-gray-500' : 'hidden flex h-full items-center justify-center text-xs text-gray-500' }}">{{ __('No photo') }}</div>
                </div>

                <div class="p-5">
                    <div id="booking-summary-heading" class="text-sm font-medium text-blue-900">{{ __('Booking summary') }}</div>

                    <div class="mt-2 space-y-1 text-sm text-blue-800">
                        <div id="booking-summary-room-lines" class="{{ $prefilledRoom ? '' : 'hidden' }}">
                            <div>
                                <span class="font-medium">{{ __('Room:') }}</span>
                                <span id="booking-summary-room-name">{{ $prefilledRoom?->name }}</span>
                            </div>
                            <div id="booking-summary-location-row" class="text-blue-800/90 {{ $prefilledRoom?->location ? '' : 'hidden' }}">{{ $prefilledRoom?->location }}</div>
                            <div id="booking-summary-stats-row" class="text-xs text-blue-800/80 {{ $prefilledRoom ? '' : 'hidden' }}">
                                @if ($prefilledRoom)
                                    {{ __('Up to :capacity people', ['capacity' => $prefilledRoom->capacity]) }}
                                    @if ($prefilledRoom->size_sqm)
                                        · {{ $prefilledRoom->size_sqm }} m²
                                    @endif
                                    @if ($prefilledRoom->hourly_rate !== null)
                                        · {{ $prefilledRoom->hourlyRateLabel() }}
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div id="booking-summary-date-wrap" class="{{ $displayDate ? '' : 'hidden' }}">
                            <span class="font-medium">{{ __('Date:') }}</span>
                            <span id="booking-summary-date-value">{{ $displayDate }}</span>
                        </div>
                    </div>

                    <a
                        id="booking-summary-room-link"
                        href="{{ $prefilledRoom ? route('rooms.show', $prefilledRoom->id) : '#' }}"
                        class="booking-summary-detail-link mt-3 inline-block text-sm font-medium text-blue-900 underline decoration-blue-300 hover:text-blue-950 {{ $prefilledRoom ? '' : 'hidden' }}"
                    >
                        {{ __('View full room details') }}
                    </a>
                </div>
            </div>

            <div class="mt-5 rounded-3xl border border-white/70 bg-white/90 p-6 shadow-sm">
                <div class="mb-5">
                    <h2 class="text-base font-semibold text-gray-900">Create reservation</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Choose a room, pick a date, then select a time range.
                        <span class="block text-gray-500">Bookable {{ \App\Support\ReservationBookingWindow::hoursSummary() }} · {{ \App\Support\ReservationBookingWindow::slotStepLabel() }} slot grid.</span>
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('reservations.store') }}"
                    class="space-y-4"
                    id="reservationForm"
                    novalidate
                    data-initial-booking-summary-visible="{{ $bookingSummaryInitiallyVisible ? '1' : '0' }}"
                    data-room-booked-slots-url="{{ route('reservations.roomBookedSlots') }}"
                    data-overlap-warntext-intro="{{ __('This range is not available for the room on the date you chose. It overlaps booked time:') }}"
                    data-overlap-warntext-cta="{{ __('Change your start and end times, or choose another date.') }}"
                >
                    @csrf

                    <div>
                        <label for="room_id" class="block text-sm font-medium text-gray-900">Room</label>
                        <select id="room_id" name="room_id" class="app-field">
                            <option value="">Select a room</option>
                            @foreach ($rooms as $room)
                                <option
                                    value="{{ $room->id }}"
                                    data-hourly-rate="{{ $room->hourly_rate ?? '' }}"
                                    @selected((string) $selectedRoomId === (string) $room->id)
                                >{{ $room->name }}@if ($room->hourly_rate !== null) ({{ $room->hourlyRateLabel() }})@endif</option>
                            @endforeach
                        </select>
                        @error('room_id')
                            <div class="mt-1 text-xs text-red-700">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <x-reservation-date-mini-calendar :value="$displayDate" :min="$minDateStr" :bookings="$miniCalendarBookings ?? []" />
                        @error('date')
                            <div class="mt-1 text-xs text-red-700">{{ $message }}</div>
                        @enderror
                    </div>

                    @php
                        $timeSlots = \App\Support\ReservationBookingWindow::selectOptions([]);
                    @endphp

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

                    @error('overlap')
                        <div class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
                            {{ $message }}
                        </div>
                    @enderror

                    <p id="reservationSlotConflictWarn" class="hidden rounded-md border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-950" role="alert"></p>

                    <p id="reservationEstimate" class="hidden rounded-lg border border-emerald-200 bg-emerald-50/90 px-3 py-2 text-sm text-emerald-950" role="status"></p>

                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gray-900 px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50" id="reservationSubmit">
                        <x-lucide name="calendar-plus" class="h-4 w-4 shrink-0 opacity-90" />
                        Create reservation
                    </button>
                </form>
            </div>
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-start sm:justify-between">
                <div>
                    <div class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">{{ __('Overview') }}</div>
                    <h2 id="bookings-overview" class="mt-2 text-xl font-semibold text-gray-900">{{ __('Bookings') }}</h2>
                    @if (($viewMode ?? 'list') === 'calendar')
                        <p class="mt-1 max-w-xl text-sm text-gray-600">
                            {{ __('Your reservations by month. Select a booking to review or edit.') }}
                            @if (! empty($calendarSubtitle))
                                <span class="mt-2 block font-medium text-gray-800">{{ $calendarSubtitle }}</span>
                            @endif
                        </p>
                    @else
                        <p class="mt-1 text-sm text-gray-600">{{ __('Review, edit, or cancel your existing reservations.') }}</p>
                    @endif
                </div>
                <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                    <x-reservation-view-tabs
                        :viewMode="$viewMode ?? 'list'"
                        :historyUrl="$historyTabUrl"
                        :calendarUrl="$calendarTabUrl"
                    />
                    @if (($viewMode ?? 'list') === 'calendar')
                        <a
                            href="{{ $browseRoomsUrl ?? route('rooms.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/35"
                        >
                            <x-lucide name="door-open" class="h-4 w-4 opacity-90" aria-hidden="true" />
                            {{ __('Browse rooms') }}
                        </a>
                    @endif
                </div>
            </div>

            @if (($viewMode ?? 'list') === 'calendar')
                <div class="mt-8" aria-labelledby="bookings-overview">
                    @include('reservations.partials.user-month-calendar-board', [
                        'calendarHeading' => $calendarHeading ?? '',
                        'prevUrl' => $calendarPrevUrl ?? '#',
                        'nextUrl' => $calendarNextUrl ?? '#',
                        'todayUrl' => $calendarTodayUrl ?? '#',
                        'weeks' => $weeks ?? [],
                        'reservationsByDate' => $reservationsByDate ?? collect(),
                    ])
                </div>
            @else

            @if ($reservations->count() === 0)
                <div class="mt-5 rounded-3xl border border-white/70 bg-white/90 p-6 text-sm text-gray-700 shadow-sm">
                    You have no reservations yet.
                </div>
            @else
                <div class="mt-5 space-y-6">
                    @foreach ($reservations as $reservation)
                        <article class="overflow-hidden rounded-3xl border border-white/70 bg-white/90 shadow-sm">
                            <div class="border-b border-gray-100 bg-gray-50/80 px-4 py-3 sm:px-5">
                                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                                    <div class="text-sm text-gray-800">
                                        <span class="font-semibold text-gray-900">{{ $reservation->date }}</span>
                                        <span class="text-gray-400"> · </span>
                                        <span>{{ substr($reservation->start_time, 0, 5) }}–{{ substr($reservation->end_time, 0, 5) }}</span>
                                        @if ($label = $reservation->estimatedTotalLabel())
                                            <span class="text-gray-400"> · </span>
                                            <span class="font-medium text-emerald-800">Est. {{ $label }}</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
                                        <a href="{{ route('reservations.edit', $reservation->id) }}" class="app-table-action">
                                            <x-lucide name="square-pen" class="h-4 w-4 shrink-0" />
                                            Edit booking
                                        </a>
                                        <form method="POST" action="{{ route('reservations.destroy', $reservation->id) }}" class="inline-flex" data-confirm-message="Are you sure you want to cancel this reservation?" data-confirm-variant="danger" data-confirm-button-label="Cancel reservation">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="app-table-action app-table-action-danger">
                                                <x-lucide name="circle-x" class="h-4 w-4 shrink-0" />
                                                Cancel
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="p-5 sm:p-6">
                                @include('rooms.partials.detail-snippet', ['room' => $reservation->room])
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $reservations->links() }}
                </div>
            @endif
            @endif
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('reservationForm');
            const submit = document.getElementById('reservationSubmit');
            const room = document.getElementById('room_id');
            const date = document.getElementById('date');
            const start = document.getElementById('start_time');
            const end = document.getElementById('end_time');
            const estimateEl = document.getElementById('reservationEstimate');
            const warnEl = document.getElementById('reservationSlotConflictWarn');

            if (!form || !submit || !room || !date || !start || !end) return;

            /** @type {string} */
            let overlapFetchedKey = '';

            /** @type {Array<{start: string, end: string}>} */
            let overlapFetchedSlots = [];

            /** @type Record<string, Record<string, unknown>> */
            let roomsSummaryMeta = {};
            const metaEl = document.getElementById('rooms-booking-summary-meta');
            if (metaEl) {
                try {
                    roomsSummaryMeta = JSON.parse(metaEl.textContent || '{}');
                } catch (_) {
                    roomsSummaryMeta = {};
                }
            }

            const bookingPanel = document.getElementById('booking-summary-panel');
            const photoWrap = document.getElementById('booking-summary-photo-wrap');
            const photoImg = document.getElementById('booking-summary-photo-img');
            const photoEmpty = document.getElementById('booking-summary-photo-empty');
            const roomLines = document.getElementById('booking-summary-room-lines');
            const roomName = document.getElementById('booking-summary-room-name');
            const locationRow = document.getElementById('booking-summary-location-row');
            const statsRow = document.getElementById('booking-summary-stats-row');
            const dateWrap = document.getElementById('booking-summary-date-wrap');
            const dateValue = document.getElementById('booking-summary-date-value');
            const roomLink = document.getElementById('booking-summary-room-link');

            let roomDirty = false;

            const setPanelVisible = function (on) {
                if (!bookingPanel) return;
                bookingPanel.classList.toggle('hidden', !on);
                bookingPanel.setAttribute('aria-hidden', on ? 'false' : 'true');
            };

            /** @param {Record<string, unknown>} r */
            const applyRoomMeta = function (r) {
                if (!photoWrap || !photoImg || !photoEmpty || !roomLines || !roomName || !locationRow || !statsRow || !roomLink) return;
                photoWrap.classList.remove('hidden');
                const imgUrl = typeof r.image_url === 'string' ? r.image_url.trim() : '';
                if (imgUrl) {
                    photoImg.src = imgUrl;
                    photoImg.classList.remove('hidden');
                    photoImg.classList.add('h-full', 'w-full', 'object-cover');
                    photoEmpty.classList.add('hidden');
                    photoEmpty.classList.remove('flex');
                } else {
                    photoImg.removeAttribute('src');
                    photoImg.classList.add('hidden');
                    photoImg.classList.remove('h-full', 'w-full', 'object-cover');
                    photoEmpty.classList.remove('hidden');
                    photoEmpty.classList.add('flex', 'h-full', 'items-center', 'justify-center', 'text-xs', 'text-gray-500');
                }
                roomName.textContent = String(r.name ?? '');
                const loc = typeof r.location === 'string' ? r.location.trim() : '';
                if (loc) {
                    locationRow.textContent = loc;
                    locationRow.classList.remove('hidden');
                } else {
                    locationRow.textContent = '';
                    locationRow.classList.add('hidden');
                }
                statsRow.textContent = typeof r.stats_summary === 'string' ? r.stats_summary : '';
                statsRow.classList.remove('hidden');
                roomLines.classList.remove('hidden');
                const href = typeof r.show_url === 'string' ? r.show_url : '#';
                roomLink.href = href;
                roomLink.classList.remove('hidden');
            };

            const syncDateLine = function () {
                if (!dateWrap || !dateValue || !date) return;
                const v = (date.value || '').trim();
                if (v) {
                    dateValue.textContent = v;
                    dateWrap.classList.remove('hidden');
                } else {
                    dateWrap.classList.add('hidden');
                }
            };

            const refreshBookingSummary = function () {
                if (!bookingPanel) return;
                const id = room.value;
                const initial = form.dataset.initialBookingSummaryVisible === '1';

                if (id) {
                    const row = roomsSummaryMeta[id];
                    setPanelVisible(true);
                    if (row) {
                        applyRoomMeta(row);
                    }
                    syncDateLine();
                    return;
                }

                if (roomDirty) {
                    setPanelVisible(false);
                    return;
                }

                if (initial) {
                    syncDateLine();
                } else {
                    setPanelVisible(false);
                }
            };

            room.addEventListener('change', function () {
                overlapFetchedKey = '';
                roomDirty = true;
                refreshBookingSummary();
            });

            date.addEventListener('input', function () {
                overlapFetchedKey = '';
                syncDateLine();
            });
            date.addEventListener('change', function () {
                overlapFetchedKey = '';
                syncDateLine();
            });

            refreshBookingSummary();

            const overlapSlotsUrl =
                typeof form.dataset.roomBookedSlotsUrl === 'string'
                    ? form.dataset.roomBookedSlotsUrl.trim()
                    : '';
            const overlapIntro =
                typeof form.dataset.overlapWarntextIntro === 'string' ? form.dataset.overlapWarntextIntro : '';
            const overlapCta =
                typeof form.dataset.overlapWarntextCta === 'string' ? form.dataset.overlapWarntextCta : '';

            /** @type {ReturnType<typeof setTimeout>|null} */
            let overlapDebounceTimer = null;

            /** @type {boolean} */
            let overlapClientBlock = false;
            let overlapWarnSeq = 0;

            const overlapsInterval = function (pickStart, pickEnd, bookStart, bookEnd) {
                return pickStart < bookEnd && pickEnd > bookStart;
            };

            const overlapsBookedStarts = function (pickStart, pickEnd, slots) {
                /** @type {Array<{start: string, end: string}>} */
                const hits = [];
                for (let i = 0; i < slots.length; i++) {
                    const s = slots[i];
                    if (!s || typeof s.start !== 'string' || typeof s.end !== 'string') continue;
                    if (overlapsInterval(pickStart, pickEnd, s.start, s.end)) hits.push(s);
                }
                return hits;
            };

            const hideOverlapWarn = function () {
                overlapClientBlock = false;
                if (!warnEl) return;
                warnEl.textContent = '';
                warnEl.classList.add('hidden');
            };

            const updateSubmitGate = function () {
                const baseValid =
                    !!room.value &&
                    !!date.value &&
                    !!start.value &&
                    !!end.value &&
                    end.value > start.value;

                submit.disabled = !baseValid || overlapClientBlock;
            };

            const applyOverlapDecision = function (hitsLen, rangesSentence) {
                if (!hitsLen || !rangesSentence) {
                    hideOverlapWarn();
                    updateSubmitGate();
                    return;
                }
                if (!warnEl) return;
                warnEl.textContent =
                    overlapIntro.trim() + ' ' + rangesSentence + '. ' + overlapCta.trim();
                warnEl.classList.remove('hidden');
                overlapClientBlock = true;
                updateSubmitGate();
            };

            const runOverlapWarn = async function () {
                if (!overlapSlotsUrl || !warnEl) return;

                if (
                    !room.value ||
                    !date.value ||
                    !start.value ||
                    !end.value ||
                    end.value <= start.value
                ) {
                    hideOverlapWarn();
                    updateSubmitGate();
                    return;
                }

                overlapWarnSeq += 1;
                const seq = overlapWarnSeq;

                const fetchKey = String(room.value) + '|' + date.value;

                try {
                    if (fetchKey !== overlapFetchedKey) {
                        const qs = new URLSearchParams({
                            room_id: String(room.value),
                            date: String(date.value),
                        });
                        const res = await fetch(overlapSlotsUrl + '?' + qs.toString(), {
                            credentials: 'same-origin',
                            headers: {
                                Accept: 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (seq !== overlapWarnSeq) return;

                        if (!res.ok) {
                            hideOverlapWarn();
                            updateSubmitGate();
                            return;
                        }

                        const body = await res.json();

                        if (seq !== overlapWarnSeq) return;

                        overlapFetchedKey = fetchKey;
                        overlapFetchedSlots = Array.isArray(body.slots) ? body.slots : [];
                    }

                    if (seq !== overlapWarnSeq) return;

                    const hits = overlapsBookedStarts(start.value, end.value, overlapFetchedSlots);
                    if (hits.length === 0) {
                        hideOverlapWarn();
                        updateSubmitGate();
                        return;
                    }

                    const rangesSentence = hits
                        .map(function (h) {
                            return String(h.start) + '–' + String(h.end);
                        })
                        .join(', ');

                    applyOverlapDecision(hits.length, rangesSentence);
                } catch (_) {
                    if (seq !== overlapWarnSeq) return;
                    hideOverlapWarn();
                    updateSubmitGate();
                }
            };

            const scheduleOverlapCheck = function () {
                if (!overlapSlotsUrl) {
                    hideOverlapWarn();
                    updateSubmitGate();
                    return;
                }

                if (overlapDebounceTimer !== null) {
                    window.clearTimeout(overlapDebounceTimer);
                }
                overlapDebounceTimer = window.setTimeout(function () {
                    overlapDebounceTimer = null;
                    void runOverlapWarn();
                }, 280);
            };

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
                    '/hr). No payment in Reservo—estimate only.';
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

            start.addEventListener('change', () => {
                refreshEndOptions();
                updateSubmitGate();
                refreshEstimate();
                scheduleOverlapCheck();
            });

            for (const el of [room, date, start, end]) {
                el.addEventListener('change', () => {
                    updateSubmitGate();
                    refreshEstimate();
                    scheduleOverlapCheck();
                });
            }

            refreshEndOptions();
            updateSubmitGate();
            refreshEstimate();
            scheduleOverlapCheck();
        })();
    </script>
@endsection


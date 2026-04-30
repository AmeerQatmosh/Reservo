@php
    /** @var \App\Models\Reservation $reservation */
    $d = \Illuminate\Support\Carbon::parse($reservation->date);
    $dateLabel = $d->isToday()
        ? __('Today')
        : ($d->isTomorrow()
            ? __('Tomorrow')
            : $d->format('D, M j'));
@endphp
<li
    class="group flex gap-3 rounded-2xl border border-gray-200/80 bg-white p-3 shadow-sm ring-1 ring-gray-900/[0.03] transition hover:border-teal-200/90 hover:shadow-md hover:ring-teal-500/10"
>
    @if ($reservation->room)
        <a
            href="{{ route('rooms.show', $reservation->room->id) }}"
            class="relative h-16 w-20 shrink-0 overflow-hidden rounded-xl bg-gray-200 ring-1 ring-gray-900/5"
        >
            @if ($reservation->room->image_url)
                <img
                    src="{{ $reservation->room->image_url }}"
                    alt=""
                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                    loading="lazy"
                >
            @else
                <span class="flex h-full items-center justify-center text-[10px] text-gray-500">{{ __('No photo') }}</span>
            @endif
        </a>
    @else
        <div class="flex h-16 w-20 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-xs text-gray-500">—</div>
    @endif
    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-baseline justify-between gap-2">
            @if ($reservation->room)
                <a
                    href="{{ route('rooms.show', $reservation->room->id) }}"
                    class="text-sm font-semibold text-gray-900 transition hover:text-teal-800"
                >{{ $reservation->room->name }}</a>
            @else
                <span class="text-sm font-semibold text-gray-900">{{ __('Room') }}</span>
            @endif
            <span
                class="shrink-0 rounded-full bg-teal-50 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-teal-800"
            >{{ $dateLabel }}</span>
        </div>
        @if ($reservation->room?->location)
            <div class="mt-0.5 line-clamp-1 text-xs text-gray-500">
                {{ $reservation->room->location }}
            </div>
        @endif
        <div class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-600">
            <span class="inline-flex items-center gap-1">
                <x-lucide name="calendar-check" class="h-3.5 w-3.5 text-gray-400" />
                {{ $d->format('M j, Y') }}
            </span>
            <span class="text-gray-300" aria-hidden="true">·</span>
            <span>{{ substr($reservation->start_time, 0, 5) }} – {{ substr($reservation->end_time, 0, 5) }}</span>
        </div>
        @if (! $reservation->isBeforeToday())
            <a
                href="{{ route('reservations.edit', $reservation->id) }}"
                class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-teal-600 transition hover:text-teal-800"
            >
                {{ __('Edit booking') }}
                <x-lucide name="chevron-right" class="h-3.5 w-3.5" />
            </a>
        @endif
    </div>
</li>

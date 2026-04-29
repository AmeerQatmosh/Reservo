@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $statStyles = [
            'indigo' => [
                'icon' => 'bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100/90',
                'card' => 'hover:border-indigo-200/90 hover:ring-indigo-500/15',
            ],
            'emerald' => [
                'icon' => 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100/90',
                'card' => 'hover:border-emerald-200/90 hover:ring-emerald-500/15',
            ],
            'amber' => [
                'icon' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-100/90',
                'card' => 'hover:border-amber-200/90 hover:ring-amber-500/15',
            ],
            'violet' => [
                'icon' => 'bg-violet-50 text-violet-600 ring-1 ring-violet-100/90',
                'card' => 'hover:border-violet-200/90 hover:ring-violet-500/15',
            ],
            'rose' => [
                'icon' => 'bg-rose-50 text-rose-600 ring-1 ring-rose-100/90',
                'card' => 'hover:border-rose-200/90 hover:ring-rose-500/15',
            ],
        ];
        $linkTones = [
            'indigo' => 'from-indigo-500/12 to-indigo-500/0 border-indigo-200/60 hover:border-indigo-300/90',
            'emerald' => 'from-emerald-500/12 to-emerald-500/0 border-emerald-200/60 hover:border-emerald-300/90',
            'amber' => 'from-amber-500/12 to-amber-500/0 border-amber-200/60 hover:border-amber-300/90',
            'violet' => 'from-violet-500/12 to-violet-500/0 border-violet-200/60 hover:border-violet-300/90',
            'rose' => 'from-rose-500/12 to-rose-500/0 border-rose-200/60 hover:border-rose-300/90',
        ];
    @endphp

    <div class="reservo-dashboard-in">
        <div class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Overview</div>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">Dashboard</h1>
        <p class="mt-2 max-w-2xl text-sm text-gray-600">
            Reservations, rooms, and shortcuts.
        </p>
    </div>

    <div
        class="reservo-dashboard-in mt-8 grid gap-4 sm:grid-cols-2 {{ count($stats) >= 5 ? 'xl:grid-cols-5' : 'xl:grid-cols-4' }}"
        style="animation-delay: 90ms"
    >
        @foreach ($stats as $stat)
            @php
                $accent = $stat['accent'] ?? 'indigo';
                $s = $statStyles[$accent] ?? $statStyles['indigo'];
            @endphp
            <a
                href="{{ $stat['href'] }}"
                class="group relative block overflow-hidden rounded-3xl border border-white/80 bg-white/95 p-6 shadow-sm ring-1 ring-gray-900/[0.04] transition duration-200 hover:-translate-y-1 hover:shadow-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/20 {{ $s['card'] }}"
            >
                <div class="flex items-start justify-between gap-3">
                    <div class="rounded-2xl p-2.5 {{ $s['icon'] }}">
                        <x-lucide name="{{ $stat['icon'] }}" class="h-5 w-5" />
                    </div>
                    <x-lucide
                        name="chevron-right"
                        class="h-4 w-4 shrink-0 text-gray-300 transition group-hover:translate-x-0.5 group-hover:text-gray-500"
                    />
                </div>
                <div class="mt-4 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ $stat['label'] }}</div>
                <div class="mt-1 text-3xl font-semibold tabular-nums tracking-tight text-gray-900">{{ $stat['value'] }}</div>
                <div
                    class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-gray-200/80 to-transparent opacity-0 transition group-hover:opacity-100"
                ></div>
            </a>
        @endforeach
    </div>

    <div class="reservo-dashboard-in mt-10 grid gap-8 lg:grid-cols-2" style="animation-delay: 140ms">
        <div class="min-w-0">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-sm font-semibold uppercase tracking-[0.16em] text-gray-500">Quick links</h2>
            </div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                @foreach ($quickLinks as $link)
                    @php $tone = $link['tone'] ?? 'indigo'; $grad = $linkTones[$tone] ?? $linkTones['indigo']; @endphp
                    <a
                        href="{{ $link['href'] }}"
                        class="group relative flex flex-col overflow-hidden rounded-2xl border bg-gradient-to-b p-4 text-left shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/20 {{ $grad }}"
                    >
                        <div class="flex items-start gap-3">
                            <span
                                class="inline-flex rounded-xl bg-white/90 p-2 text-gray-800 shadow-sm ring-1 ring-gray-900/5 transition group-hover:scale-105"
                            >
                                <x-lucide name="{{ $link['icon'] }}" class="h-5 w-5" />
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold text-gray-900">{{ $link['label'] }}</span>
                                <span class="mt-1 block text-xs leading-relaxed text-gray-600">{{ $link['description'] }}</span>
                            </span>
                            <x-lucide
                                name="chevron-right"
                                class="h-4 w-4 shrink-0 text-gray-300 transition group-hover:translate-x-0.5 group-hover:text-gray-500"
                            />
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="min-w-0">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-[0.16em] text-gray-500">Upcoming</h2>
                <a
                    href="{{ route('reservations.my') }}"
                    class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 transition hover:text-indigo-800"
                >
                    View all
                    <x-lucide name="chevron-right" class="h-3.5 w-3.5" />
                </a>
            </div>

            @if ($upcomingReservations->count() === 0)
                <div
                    class="mt-4 flex flex-col items-center justify-center rounded-3xl border border-dashed border-gray-200/90 bg-gradient-to-b from-gray-50/80 to-white px-6 py-12 text-center"
                >
                    <div class="rounded-2xl bg-gray-100 p-3 text-gray-400">
                        <x-lucide name="calendar-search" class="h-7 w-7" />
                    </div>
                    <p class="mt-3 text-sm font-medium text-gray-900">No upcoming reservations</p>
                    <p class="mt-1 max-w-xs text-xs text-gray-500">When you book a room, the next few visits will show up here.</p>
                    <a
                        href="{{ route('rooms.index') }}"
                        class="mt-4 inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2 text-xs font-medium text-white transition hover:bg-gray-800"
                    >Find a room</a>
                </div>
            @else
                <ul class="mt-4 space-y-3">
                    @foreach ($upcomingReservations as $reservation)
                        @php
                            $d = \Illuminate\Support\Carbon::parse($reservation->date);
                            $dateLabel = $d->isToday()
                                ? 'Today'
                                : ($d->isTomorrow()
                                    ? 'Tomorrow'
                                    : $d->format('D, M j'));
                        @endphp
                        <li
                            class="group flex gap-3 rounded-2xl border border-gray-200/80 bg-white p-3 shadow-sm ring-1 ring-gray-900/[0.03] transition hover:border-indigo-200/90 hover:shadow-md hover:ring-indigo-500/10"
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
                                        <span
                                            class="flex h-full items-center justify-center text-[10px] text-gray-500"
                                        >No photo</span>
                                    @endif
                                </a>
                            @else
                                <div
                                    class="flex h-16 w-20 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-xs text-gray-500"
                                >—</div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-baseline justify-between gap-2">
                                    @if ($reservation->room)
                                        <a
                                            href="{{ route('rooms.show', $reservation->room->id) }}"
                                            class="text-sm font-semibold text-gray-900 transition hover:text-indigo-800"
                                        >{{ $reservation->room->name }}</a>
                                    @else
                                        <span class="text-sm font-semibold text-gray-900">Room</span>
                                    @endif
                                    <span
                                        class="shrink-0 rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-indigo-800"
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
                                <a
                                    href="{{ route('reservations.edit', $reservation->id) }}"
                                    class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-indigo-600 transition hover:text-indigo-800"
                                >
                                    Edit booking
                                    <x-lucide name="chevron-right" class="h-3.5 w-3.5" />
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection

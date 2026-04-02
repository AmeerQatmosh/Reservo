@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Sandbox</div>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">Dashboard</h1>
            <p class="mt-2 max-w-2xl text-sm text-gray-600">
                Session controls and sandbox navigation stay in the bar above. Here: stats, quick links, and upcoming bookings.
            </p>
        </div>
        @if ($isAdmin)
            <div class="flex flex-wrap items-center gap-2 text-sm text-gray-700">
                <span class="shrink-0">Role badge:</span>
                <span
                    class="@class([
                        'rounded-full border px-3 py-1.5 text-xs font-semibold shadow-sm',
                        'bg-purple-100 text-purple-700' => $isSuperAdmin,
                        'bg-green-100 text-green-700' => ! $isSuperAdmin,
                    ])"
                >
                    {{ ucwords(str_replace('_', ' ', $role)) }}
                </span>
            </div>
        @endif
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="text-xs font-medium uppercase tracking-[0.2em] text-gray-500">{{ $stat['label'] }}</div>
                <div class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-sm sm:rounded-3xl sm:p-7">
            <div class="text-sm font-medium text-gray-900">Quick links</div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <a href="{{ route('demo.rooms') }}" class="rounded-2xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-900 transition hover:border-gray-300 hover:bg-gray-50">
                    Browse Rooms
                </a>
                @if (\App\Support\DemoState::canUser())
                    <a href="{{ route('demo.reservations.my') }}" class="rounded-2xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-900 transition hover:border-gray-300 hover:bg-gray-50">
                        My Reservations
                    </a>
                @endif
                @if ($isAdmin)
                    <a href="{{ route('demo.admin.rooms') }}" class="rounded-2xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-900 transition hover:border-gray-300 hover:bg-gray-50">
                        Manage Rooms
                    </a>
                    <a href="{{ route('demo.admin.reservations') }}" class="rounded-2xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-900 transition hover:border-gray-300 hover:bg-gray-50">
                        Manage Reservations
                    </a>
                    <a href="{{ route('demo.admin.users') }}" class="rounded-2xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-900 transition hover:border-gray-300 hover:bg-gray-50">
                        {{ $isSuperAdmin ? 'Manage Users' : 'View Users' }}
                    </a>
                @endif
            </div>
        </div>

        <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-sm sm:rounded-3xl sm:p-7">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm font-medium text-gray-900">Upcoming sandbox reservations</div>
                @if (\App\Support\DemoState::canUser())
                    <a href="{{ route('demo.reservations.my') }}" class="text-sm text-gray-700 hover:text-gray-900">View all</a>
                @endif
            </div>

            @if (count($upcomingReservations) === 0)
                <div class="mt-4 rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-5 text-sm text-gray-600">
                    No upcoming reservations in this session.
                </div>
            @else
                <ul class="mt-3 space-y-3">
                    @foreach ($upcomingReservations as $reservation)
                        @php
                            $resRoom = \App\Support\DemoState::findRoom((int) ($reservation['room_id'] ?? 0));
                        @endphp
                        <li class="flex gap-3 rounded-2xl border border-gray-200 bg-gray-50/70 p-3">
                            @if ($resRoom)
                                <a href="{{ route('demo.room.show', $resRoom['id']) }}" class="relative h-16 w-20 shrink-0 overflow-hidden rounded-xl bg-gray-200">
                                    @if (! empty($resRoom['image_url']))
                                        <img src="{{ $resRoom['image_url'] }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                    @else
                                        <span class="flex h-full items-center justify-center text-[10px] text-gray-500">No photo</span>
                                    @endif
                                </a>
                            @else
                                <div class="flex h-16 w-20 shrink-0 items-center justify-center rounded-xl bg-gray-200 text-[10px] text-gray-500">
                                    —
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $resRoom ? $resRoom['name'] : 'Room' }}</div>
                                @if ($resRoom && ! empty($resRoom['location']))
                                    <div class="mt-0.5 line-clamp-1 text-xs text-gray-500">{{ $resRoom['location'] }}</div>
                                @endif
                                <div class="mt-1 text-xs text-gray-600">
                                    {{ $reservation['date'] }} · {{ substr($reservation['start_time'], 0, 5) }}–{{ substr($reservation['end_time'], 0, 5) }}
                                </div>
                                <a href="{{ route('demo.reservations.my') }}" class="mt-2 inline-block text-xs font-medium text-gray-700 underline hover:text-gray-900">Open My reservations</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection

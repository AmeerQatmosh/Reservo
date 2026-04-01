@extends('layouts.app')

@section('title', 'Workspace booking')

@section('content')
    <div class="relative overflow-hidden rounded-2xl border border-white/70 bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 p-6 text-white shadow-xl sm:rounded-3xl sm:p-10 md:p-14">
        <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-indigo-500/25 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-cyan-400/15 blur-3xl"></div>
        <div class="relative max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-indigo-200/90">Workspace scheduling</p>
            <h1 class="mt-4 text-3xl font-semibold tracking-tight sm:text-4xl md:text-5xl">
                Book the right room, at the right time.
            </h1>
            <p class="mt-5 text-base leading-relaxed text-slate-200/90 sm:text-lg">
                Reservo is a focused reservation system for teams who share meeting rooms, labs, and event spaces.
                Real-time overlap checks mean no double bookings—ever.
            </p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                <a href="{{ route('rooms.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3.5 text-sm font-semibold text-slate-900 shadow-lg transition hover:bg-slate-100">
                    Browse rooms
                </a>
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/30 bg-white/5 px-6 py-3.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/10">
                        Create an account
                    </a>
                @else
                    <a href="{{ route('reservations.my') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/30 bg-white/5 px-6 py-3.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/10">
                        My reservations
                    </a>
                @endguest
            </div>
        </div>
    </div>

    <div class="mt-12 grid gap-6 sm:grid-cols-3">
        <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">No double bookings</div>
            <p class="mt-2 text-sm leading-6 text-gray-600">
                Overlap validation uses the standard interval rule so partial overlaps are rejected and back-to-back slots still work.
            </p>
        </div>
        <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">Built for admins</div>
            <p class="mt-2 text-sm leading-6 text-gray-600">
                Manage spaces, see every reservation, and keep history when a room is archived with soft delete.
            </p>
        </div>
        <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">Simple for everyone</div>
            <p class="mt-2 text-sm leading-6 text-gray-600">
                Blade UI, clear forms, and confirmation on destructive actions—polished enough to demo with confidence.
            </p>
        </div>
    </div>

    @if ($featuredRooms->isNotEmpty())
        <div class="mt-14">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Featured spaces</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900">Popular rooms this week</h2>
                </div>
                <a href="{{ route('rooms.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">View all rooms →</a>
            </div>
            <div class="mt-6 grid gap-6 lg:grid-cols-3">
                @foreach ($featuredRooms as $room)
                    <a href="{{ route('rooms.show', $room->id) }}" class="group overflow-hidden rounded-3xl border border-white/70 bg-white/90 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="aspect-[16/10] overflow-hidden bg-gray-100">
                            @if ($room->image_url)
                                <img
                                    src="{{ $room->image_url }}"
                                    alt="{{ $room->name }}"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                                    loading="lazy"
                                >
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 text-sm text-gray-500">
                                    No photo
                                </div>
                            @endif
                        </div>
                        <div class="p-5">
                            <div class="text-lg font-semibold text-gray-900">{{ $room->name }}</div>
                            @if ($room->location)
                                <p class="mt-1 line-clamp-1 text-xs text-gray-500">{{ $room->location }}</p>
                            @endif
                            <p class="mt-2 text-xs font-medium text-gray-600">
                                Up to {{ $room->capacity }} people
                                @if ($room->size_sqm)
                                    <span class="text-gray-400"> · </span>{{ $room->size_sqm }} m²
                                @endif
                            </p>
                            @if (is_array($room->amenities) && count($room->amenities))
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach (array_slice($room->amenities, 0, 3) as $amenity)
                                        <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-600">{{ $amenity }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <p class="mt-3 line-clamp-2 text-sm text-gray-600">{{ $room->description }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <p class="mt-12 text-center text-xs leading-relaxed text-gray-500">
        Room names, copy, and availability in this demo are sample data loaded from the app seeders.
        Photos are served from
        <a href="https://unsplash.com" class="font-medium text-gray-700 underline hover:text-gray-900" target="_blank" rel="noopener noreferrer">Unsplash</a>
        under the
        <a href="https://unsplash.com/license" class="font-medium text-gray-700 underline hover:text-gray-900" target="_blank" rel="noopener noreferrer">Unsplash License</a>
        (free to use with their guidelines).
    </p>
@endsection

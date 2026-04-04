@extends('layouts.app')

@section('title', 'Rooms')

@section('content')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Browse</div>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">Rooms</h1>
            <p class="mt-2 text-sm text-gray-600">
                Filter by text, capacity, floor area, location, amenities, or sort the list.
            </p>
        </div>
    </div>

    <div class="mt-6">
        @include('rooms.partials.filter-form', [
            'action' => route('rooms.index'),
            'resetUrl' => route('rooms.index'),
            'filters' => $filters,
            'filterOptions' => $filterOptions,
        ])
    </div>

    @if ($rooms->count() === 0)
        <div class="mt-6 rounded-3xl border border-white/70 bg-white/90 p-6 text-sm text-gray-700 shadow-sm">
            No rooms match the current filters.
        </div>
    @else
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($rooms as $room)
                <a href="{{ route('rooms.show', $room->id) }}" class="overflow-hidden rounded-3xl border border-white/70 bg-white/90 shadow-sm transition hover:-translate-y-0.5 hover:border-gray-200 hover:shadow-md">
                    <div class="aspect-[16/10] overflow-hidden bg-gray-100">
                        @if ($room->image_url)
                            <img src="{{ $room->image_url }}" alt="{{ $room->name }}" class="h-full w-full object-cover" loading="lazy">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 text-xs text-gray-500">
                                No photo
                            </div>
                        @endif
                    </div>
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="text-lg font-semibold tracking-tight text-gray-900">{{ $room->name }}</div>
                                @if ($room->location)
                                    <p class="mt-1 text-xs leading-5 text-gray-500">{{ $room->location }}</p>
                                @endif
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="inline-flex rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-medium text-gray-700">
                                        Up to {{ $room->capacity }} people
                                    </span>
                                    @if ($room->size_sqm)
                                        <span class="inline-flex rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-600">
                                            {{ $room->size_sqm }} m²
                                        </span>
                                    @endif
                                    @if ($room->hourly_rate !== null)
                                        <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-800">
                                            {{ $room->hourlyRateLabel() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if (is_array($room->amenities) && count($room->amenities))
                            <div class="mt-3 flex flex-wrap gap-1.5">
                                @foreach (array_slice($room->amenities, 0, 4) as $amenity)
                                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700">{{ $amenity }}</span>
                                @endforeach
                                @if (count($room->amenities) > 4)
                                    <span class="rounded-md bg-slate-50 px-2 py-0.5 text-[11px] text-slate-500">+{{ count($room->amenities) - 4 }} more</span>
                                @endif
                            </div>
                        @endif
                        <div class="mt-4 line-clamp-3 text-sm leading-6 text-gray-700">
                            {{ $room->description }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $rooms->links() }}
        </div>
    @endif
@endsection

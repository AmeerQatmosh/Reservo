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

    <div class="mt-6 rounded-2xl border border-white/70 bg-white/90 p-4 shadow-sm sm:p-5">
        <form method="GET" action="{{ route('rooms.index') }}" class="space-y-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:gap-4">
                <div class="w-full min-w-0 max-w-lg">
                    <label for="search" class="block text-sm font-medium text-gray-900">Search</label>
                    <input
                        id="search"
                        name="search"
                        type="text"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Name, location, description, or amenity text"
                        class="app-field"
                    >
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:gap-3 lg:shrink-0">
                    <div class="w-full min-w-0 sm:w-52 lg:w-56">
                        <label for="sort" class="block text-sm font-medium text-gray-900">Sort by</label>
                        <select id="sort" name="sort" class="app-field">
                            <option value="name" @selected(($filters['sort'] ?? 'name') === 'name')>Name (A–Z)</option>
                            <option value="capacity_asc" @selected(($filters['sort'] ?? '') === 'capacity_asc')>Capacity · low to high</option>
                            <option value="capacity_desc" @selected(($filters['sort'] ?? '') === 'capacity_desc')>Capacity · high to low</option>
                            <option value="size_asc" @selected(($filters['sort'] ?? '') === 'size_asc')>Size (m²) · small first</option>
                            <option value="size_desc" @selected(($filters['sort'] ?? '') === 'size_desc')>Size (m²) · large first</option>
                        </select>
                    </div>
                    <div class="flex flex-row flex-wrap items-end gap-2 sm:shrink-0">
                        <button
                            type="submit"
                            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/35"
                        >
                            <x-lucide name="filter" class="h-4 w-4 shrink-0" aria-hidden="true" />
                            Apply
                        </button>
                        <a
                            href="{{ route('rooms.index') }}"
                            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:border-gray-400 hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/15"
                        >
                            <x-lucide name="rotate-ccw" class="h-4 w-4 shrink-0" aria-hidden="true" />
                            Reset
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-gray-500">Refine</p>
                <div class="mt-3 flex flex-wrap items-end gap-x-2 gap-y-3 sm:gap-x-3 lg:flex-nowrap lg:gap-x-3">
                    <div class="w-[6.75rem] shrink-0 sm:w-[7.25rem]">
                        <label for="min_capacity" class="block text-sm font-medium text-gray-900">Min. capacity</label>
                        <input
                            id="min_capacity"
                            name="min_capacity"
                            type="number"
                            min="1"
                            value="{{ $filters['min_capacity'] ?? '' }}"
                            placeholder="e.g. 8"
                            class="app-field"
                        >
                    </div>
                    <div class="w-[6.75rem] shrink-0 sm:w-[7.25rem]">
                        <label for="max_capacity" class="block text-sm font-medium text-gray-900">Max. capacity</label>
                        <input
                            id="max_capacity"
                            name="max_capacity"
                            type="number"
                            min="1"
                            value="{{ $filters['max_capacity'] ?? '' }}"
                            placeholder="e.g. 20"
                            class="app-field"
                        >
                    </div>
                    <div class="w-[6.75rem] shrink-0 sm:w-[7.25rem]">
                        <label for="min_size_sqm" class="block text-sm font-medium text-gray-900">Min. size (m²)</label>
                        <input
                            id="min_size_sqm"
                            name="min_size_sqm"
                            type="number"
                            min="1"
                            value="{{ $filters['min_size_sqm'] ?? '' }}"
                            placeholder="e.g. 25"
                            class="app-field"
                        >
                    </div>
                    <div class="w-[6.75rem] shrink-0 sm:w-[7.25rem]">
                        <label for="max_size_sqm" class="block text-sm font-medium text-gray-900">Max. size (m²)</label>
                        <input
                            id="max_size_sqm"
                            name="max_size_sqm"
                            type="number"
                            min="1"
                            value="{{ $filters['max_size_sqm'] ?? '' }}"
                            placeholder="e.g. 80"
                            class="app-field"
                        >
                    </div>
                    <div class="min-w-0 w-full sm:min-w-[11rem] sm:max-w-[14rem] sm:flex-1 lg:min-w-0 lg:max-w-none lg:flex-1">
                        <label for="location" class="block text-sm font-medium text-gray-900">Location</label>
                        <select id="location" name="location" class="app-field">
                            <option value="">Any</option>
                            @foreach ($filterOptions['locations'] as $loc)
                                <option value="{{ $loc }}" @selected(($filters['location'] ?? '') === $loc)>{{ $loc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-0 w-full sm:min-w-[11rem] sm:max-w-[14rem] sm:flex-1 lg:min-w-0 lg:max-w-none lg:flex-1">
                        <label for="amenity" class="block text-sm font-medium text-gray-900">Amenity</label>
                        <select id="amenity" name="amenity" class="app-field">
                            <option value="">Any</option>
                            @foreach ($filterOptions['amenities'] as $am)
                                <option value="{{ $am }}" @selected(($filters['amenity'] ?? '') === $am)>{{ $am }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <label class="mt-4 flex cursor-pointer items-center gap-2 text-sm text-gray-800">
                    <input
                        type="checkbox"
                        name="has_photo"
                        value="1"
                        class="rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                        @checked(! empty($filters['has_photo']))
                    >
                    <span>Only rooms with a photo</span>
                </label>
            </div>
        </form>
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

@extends('layouts.app')

@section('title', 'Admin Rooms')

@section('content')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-xl font-semibold sm:text-2xl">Admin · Rooms</h1>
        <a
            href="{{ route('admin.rooms.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-center text-sm font-medium text-white transition hover:bg-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/40 sm:rounded-md sm:py-2"
        >
            <x-lucide name="plus" class="h-4 w-4 shrink-0" aria-hidden="true" />
            Add room
        </a>
    </div>

    <div class="mt-6 rounded-2xl border border-white/70 bg-white/90 p-4 shadow-sm sm:p-5">
        <form method="GET" action="{{ route('admin.rooms.index') }}" class="space-y-4">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:gap-4">
                <div class="w-full min-w-0 max-w-lg">
                    <label for="search" class="block text-sm font-medium text-gray-900">Search</label>
                    <input
                        id="search"
                        name="search"
                        type="text"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Name, location, description, amenities"
                        class="app-field"
                    >
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end sm:gap-3 xl:shrink-0">
                    <div class="w-full min-w-0 sm:w-36">
                        <label for="status" class="block text-sm font-medium text-gray-900">Status</label>
                        <select id="status" name="status" class="app-field">
                            <option value="all" @selected(($filters['status'] ?? 'all') === 'all')>All</option>
                            <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active only</option>
                            <option value="deleted" @selected(($filters['status'] ?? '') === 'deleted')>Deleted only</option>
                        </select>
                    </div>
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
                            href="{{ route('admin.rooms.index') }}"
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
        <div class="mt-6 rounded-lg border bg-white p-6 text-sm text-gray-700">
            No rooms match the current filters.
        </div>
    @else
        <div class="mt-6 overflow-hidden rounded-lg border bg-white">
            <div class="app-table-scroll">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3 w-24">Photo</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Location</th>
                        <th class="px-4 py-3">Capacity</th>
                        <th class="px-4 py-3 w-28">Rate</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($rooms as $room)
                        <tr class="@if($room->trashed()) bg-gray-50 @endif">
                            <td class="px-4 py-3 align-middle">
                                <a href="{{ route('admin.rooms.show', $room->id) }}" class="block h-14 w-[4.5rem] overflow-hidden rounded-lg bg-gray-100 ring-1 ring-gray-200/80">
                                    @if ($room->image_url)
                                        <img src="{{ $room->image_url }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                    @else
                                        <span class="flex h-full items-center justify-center text-[10px] text-gray-400">—</span>
                                    @endif
                                </a>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                <a href="{{ route('admin.rooms.show', $room->id) }}" class="hover:underline">{{ $room->name }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $room->location ?: '—' }}</td>
                            <td class="px-4 py-3">{{ $room->capacity }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $room->hourlyRateLabel() ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if ($room->trashed())
                                    <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-700">Deleted</span>
                                @else
                                    <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700">Active</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.rooms.show', $room->id) }}" class="app-table-action">View</a>
                                    <a href="{{ route('admin.rooms.edit', $room->id) }}" class="app-table-action">Edit</a>
                                @if ($room->trashed())
                                    <form method="POST" action="{{ route('admin.rooms.restore', $room->id) }}" class="inline-flex" data-confirm-message="Are you sure you want to restore this room?" data-confirm-variant="success" data-confirm-button-label="Restore">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="app-table-action app-table-action-success">
                                            Restore
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.rooms.destroy', $room->id) }}" class="inline-flex" data-confirm-message="Are you sure you want to delete this room?" data-confirm-variant="danger" data-confirm-button-label="Delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="app-table-action app-table-action-danger">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $rooms->links() }}
        </div>
    @endif
@endsection

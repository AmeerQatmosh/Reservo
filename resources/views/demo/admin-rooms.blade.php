@extends('layouts.app')

@section('title', 'Admin Rooms')

@section('content')
    @php
        $f = $filters ?? [];
        $activeAdminFilterCount = collect(
            [
                (string) ($f['min_capacity'] ?? '') !== '',
                (string) ($f['max_capacity'] ?? '') !== '',
                (string) ($f['min_size_sqm'] ?? '') !== '',
                (string) ($f['max_size_sqm'] ?? '') !== '',
                (string) ($f['min_hourly_rate'] ?? '') !== '',
                (string) ($f['max_hourly_rate'] ?? '') !== '',
                (string) ($f['location'] ?? '') !== '',
                (string) ($f['amenity'] ?? '') !== '',
                ! empty($f['has_photo']),
            ],
        )
            ->filter()
            ->count();
        if (($f['sort'] ?? 'name') !== 'name') {
            $activeAdminFilterCount++;
        }
        $roomListIsFiltered = \App\Support\FilterDisplay::roomBrowseHasNarrowing($f, false);
    @endphp
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap items-center gap-3">
            <a
                href="{{ route('demo.admin.rooms.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-teal-600 px-4 py-2.5 text-center text-sm font-medium text-white transition hover:bg-teal-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/40 sm:rounded-md sm:py-2"
            >
                <x-lucide name="plus" class="h-4 w-4 shrink-0" aria-hidden="true" />
                Add room
            </a>
            <a
                href="{{ route('demo.hub') }}"
                class="text-sm font-medium text-gray-700 hover:text-gray-900"
            >Demo home</a>
        </div>
    </div>

    <div
        class="relative mt-6 flex w-full min-w-0 flex-col gap-4 lg:grid lg:grid-cols-12 lg:items-start lg:gap-6"
        id="admin-rooms-browse-filters"
    >
        <input
            type="checkbox"
            id="demo-admin-room-browse-filters-toggle"
            class="peer pointer-events-auto absolute start-0 top-0 h-0 w-0 overflow-hidden border-0 p-0 [appearance:none] [clip:rect(0,0,0,0)] opacity-0"
            aria-label="Show or hide room filters"
            aria-controls="demo-admin-room-browse-filters-aside"
        />
        <label
            for="demo-admin-room-browse-filters-toggle"
            class="pointer-events-none fixed inset-0 z-40 cursor-default bg-slate-950/75 opacity-0 transition duration-200 ease-out max-lg:peer-checked:pointer-events-auto max-lg:peer-checked:opacity-100 lg:hidden"
            aria-label="Close room filters"
        ></label>
        <aside
            id="demo-admin-room-browse-filters-aside"
            class="min-w-0 self-start max-lg:pointer-events-none max-lg:fixed max-lg:bottom-0 max-lg:left-0 max-lg:right-0 max-lg:z-50 max-lg:flex max-lg:max-h-[min(92vh,40rem)] max-lg:w-full max-lg:translate-y-full max-lg:bg-white max-lg:opacity-0 max-lg:transition max-lg:duration-300 max-lg:ease-out max-lg:peer-checked:translate-y-0 max-lg:peer-checked:opacity-100 max-lg:peer-checked:pointer-events-auto max-lg:flex-col max-lg:overflow-hidden max-lg:rounded-t-3xl max-lg:shadow-[0_-8px_40px_rgba(15,23,42,0.18)] max-lg:ring-1 max-lg:ring-slate-200/90 max-lg:pb-[env(safe-area-inset-bottom)] lg:col-start-1 lg:col-span-3 lg:row-start-1 lg:static lg:max-h-none lg:w-auto lg:translate-y-0 lg:bg-transparent lg:opacity-100 lg:overflow-y-visible lg:pb-0 lg:shadow-none lg:ring-0 lg:peer-checked:translate-y-0 lg:sticky lg:top-[calc(5rem+env(safe-area-inset-top,0px))] lg:z-10 lg:self-start"
            aria-labelledby="demo-admin-room-browse-filters-title"
        >
            <div
                class="flex min-h-0 w-full min-w-0 flex-1 flex-col max-lg:max-h-full max-lg:overflow-hidden max-lg:bg-white lg:min-h-0"
            >
                <div
                    class="flex shrink-0 items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 py-3.5 max-lg:rounded-t-3xl lg:hidden"
                >
                    <h2
                        id="demo-admin-room-browse-filters-title"
                        class="text-base font-semibold tracking-tight text-slate-900"
                    >Filter by</h2>
                    <label
                        for="demo-admin-room-browse-filters-toggle"
                        class="inline-flex h-9 w-9 cursor-pointer select-none items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
                    >
                        <span class="sr-only">Close filters</span>
                        <x-lucide name="x" class="h-5 w-5" aria-hidden="true" />
                    </label>
                </div>
                <div
                    class="min-h-0 flex-1 overflow-y-auto overscroll-y-contain bg-white px-2 pb-2 pt-2 max-lg:max-h-full sm:px-3 lg:min-h-0 lg:overflow-visible lg:bg-transparent lg:px-0 lg:pb-0 lg:pt-0"
                >
                    <form
                        method="GET"
                        action="{{ route('demo.admin.rooms') }}"
                        id="demo-admin-rooms-filters"
                    >
                        @include('admin.rooms.partials.filters-panel', [
                            'filters' => $filters,
                            'filterOptions' => $filterOptions,
                            'resetUrl' => route('demo.admin.rooms'),
                            'includeStatus' => false,
                        ])
                    </form>
                </div>
            </div>
        </aside>
        <div
            class="flex min-w-0 flex-col gap-4 lg:col-span-9 lg:col-start-4 lg:row-start-1"
        >
            <div
                class="min-w-0 max-lg:flex max-lg:min-w-0 max-lg:items-center max-lg:gap-2 lg:pb-1"
            >
                    <div class="min-w-0 max-lg:flex-1">
                        @include('rooms.partials.room-filters-search', [
                            'filters' => $filters,
                            'filterFormId' => 'demo-admin-rooms-filters',
                        ])
                    </div>
                    <label
                        for="demo-admin-room-browse-filters-toggle"
                        class="relative inline-flex h-12 min-w-0 shrink-0 cursor-pointer select-none items-center gap-2 rounded-2xl border border-slate-200/90 bg-white px-3.5 text-sm font-medium text-slate-800 shadow-sm ring-1 ring-slate-100 transition hover:border-slate-300 hover:bg-slate-50/80 lg:hidden"
                    >
                        <x-lucide name="filter" class="h-4 w-4 shrink-0 text-slate-600" aria-hidden="true" />
                        <span class="whitespace-nowrap">Filters</span>
                        @if ($activeAdminFilterCount > 0)
                            <span
                                class="inline-flex min-h-[1.125rem] min-w-[1.125rem] items-center justify-center rounded-full bg-slate-900 px-1 text-[10px] font-semibold leading-none text-white"
                            >{{ $activeAdminFilterCount > 9 ? '9+' : $activeAdminFilterCount }}</span>
                        @endif
                    </label>
            </div>
            @if ($rooms->count() === 0)
                @include('rooms.partials.room-list-empty-state', [
                    'isFiltered' => $roomListIsFiltered,
                    'context' => 'demo-admin',
                    'resetUrl' => route('demo.admin.rooms'),
                    'addRoomUrl' => route('demo.admin.rooms.create'),
                ])
            @else
                <div
                    class="min-w-0 overflow-hidden rounded-lg border border-white/70 bg-white/90 shadow-sm"
                >
                    <div class="app-table-scroll">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                                <tr>
                                    <th class="w-24 px-4 py-3">Photo</th>
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3">Location</th>
                                    <th class="px-4 py-3">Capacity</th>
                                    <th class="w-28 px-4 py-3">Rate</th>
                                    <th class="px-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($rooms as $room)
                                    <tr>
                                        <td class="px-4 py-3 align-middle">
                                            <a
                                                href="{{ route('demo.admin.rooms.show', $room['id']) }}"
                                                class="block h-14 w-[4.5rem] overflow-hidden rounded-lg bg-gray-100 ring-1 ring-gray-200/80"
                                            >
                                                @if (! empty($room['image_url']))
                                                    <img
                                                        src="{{ $room['image_url'] }}"
                                                        alt=""
                                                        class="h-full w-full object-cover"
                                                        loading="lazy"
                                                    >
                                                @else
                                                    <span
                                                        class="flex h-full items-center justify-center text-[10px] text-gray-400"
                                                    >—</span>
                                                @endif
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            <a
                                                href="{{ route('demo.admin.rooms.show', $room['id']) }}"
                                                class="hover:underline"
                                            >{{ $room['name'] }}</a>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">{{ $room['location'] ?: '—' }}</td>
                                        <td class="px-4 py-3">{{ $room['capacity'] }}</td>
                                        <td class="px-4 py-3 text-gray-700">
                                            {{ \App\Support\DemoState::hourlyRateLabel(isset($room['hourly_rate']) ? (float) $room['hourly_rate'] : null) ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="app-table-actions">
                                                <a
                                                    href="{{ route('demo.admin.rooms.show', $room['id']) }}"
                                                    class="app-table-action"
                                                >{{ __('View') }}</a>
                                                <form
                                                    method="POST"
                                                    action="{{ route('demo.admin.rooms.destroy', $room['id']) }}"
                                                    class="inline-flex shrink-0"
                                                    data-confirm-message="Remove this sandbox room and its bookings?"
                                                    data-confirm-variant="danger"
                                                    data-confirm-button-label="Remove"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="app-table-action app-table-action-danger"
                                                    >{{ __('Delete') }}</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    {{ $rooms->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Rooms')

@section('content')
    @php
        $f = $filters ?? [];
        $activeRoomFilterCount = collect(
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
            $activeRoomFilterCount++;
        }
        $roomListIsFiltered = \App\Support\FilterDisplay::roomBrowseHasNarrowing($f, false);
        $browseView = $browseView ?? 'grid';
        $roomsIndexViewQuery = $browseView === 'list' ? ['view' => 'list'] : [];
        $roomsResetUrl = route('rooms.index', $roomsIndexViewQuery);
        $roomsViewToggleBase = request()->query();
        $roomsGridUrl = route('rooms.index', array_merge($roomsViewToggleBase, ['view' => 'grid', 'page' => 1]));
        $roomsListUrl = route('rooms.index', array_merge($roomsViewToggleBase, ['view' => 'list', 'page' => 1]));
    @endphp
    <x-page-breadcrumbs
        class="mb-4"
        :items="[
            auth()->check()
                ? ['label' => 'Dashboard', 'url' => route('dashboard')]
                : ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Rooms'],
        ]"
    />
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Browse</div>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">Rooms</h1>
            <p class="mt-2 text-sm text-gray-600">
                Search the list, then refine by people, size, price per hour, location, and amenities. On small screens, use the Filters button; on large screens, the column on the left.
            </p>
        </div>
    </div>

    <div
        class="relative mt-6 flex w-full min-w-0 flex-col gap-4 lg:grid lg:grid-cols-12 lg:items-start lg:gap-6"
    >
    <form
        method="GET"
        action="{{ route('rooms.index') }}"
        id="rooms-browse-filters"
        class="contents"
    >
        <input type="hidden" name="view" value="{{ $browseView }}">
        <input
            type="checkbox"
            id="room-browse-filters-toggle"
            class="peer pointer-events-auto absolute start-0 top-0 h-0 w-0 overflow-hidden border-0 p-0 [appearance:none] [clip:rect(0,0,0,0)] opacity-0"
            aria-label="Show or hide room filters"
            aria-controls="room-browse-filters-aside"
        />
        <div
            class="min-w-0 w-full self-start lg:col-span-9 lg:col-start-4 lg:row-start-1"
        >
            <div
                class="flex w-full min-w-0 flex-wrap items-center gap-2 sm:gap-3 lg:flex-nowrap lg:justify-between"
            >
                <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">
                    <div class="min-w-0 flex-1">
                        @include('rooms.partials.room-filters-search', [
                            'filters' => $filters,
                        ])
                    </div>
                    <label
                        for="room-browse-filters-toggle"
                        class="relative inline-flex h-12 min-w-0 shrink-0 cursor-pointer select-none items-center gap-2 rounded-2xl border border-slate-200/90 bg-white px-3.5 text-sm font-medium text-slate-800 shadow-sm ring-1 ring-slate-100 transition hover:border-slate-300 hover:bg-slate-50/80 lg:hidden"
                    >
                        <x-lucide name="filter" class="h-4 w-4 shrink-0 text-slate-600" aria-hidden="true" />
                        <span class="whitespace-nowrap">Filters</span>
                        @if ($activeRoomFilterCount > 0)
                            <span
                                class="inline-flex min-h-[1.125rem] min-w-[1.125rem] items-center justify-center rounded-full bg-slate-900 px-1 text-[10px] font-semibold leading-none text-white"
                            >{{ $activeRoomFilterCount > 9 ? '9+' : $activeRoomFilterCount }}</span>
                        @endif
                    </label>
                </div>
                @if ($rooms->count() > 0)
                    <div class="flex shrink-0 items-center max-sm:w-full max-sm:justify-end">
                        @include('rooms.partials.room-browse-view-toggle', [
                            'active' => $browseView,
                            'gridUrl' => $roomsGridUrl,
                            'listUrl' => $roomsListUrl,
                        ])
                    </div>
                @endif
            </div>
        </div>
        <label
            for="room-browse-filters-toggle"
            class="pointer-events-none fixed inset-0 z-40 cursor-default bg-slate-950/75 opacity-0 transition duration-200 ease-out max-lg:peer-checked:pointer-events-auto max-lg:peer-checked:opacity-100 lg:hidden"
            aria-label="Close room filters"
        ></label>
        <aside
            id="room-browse-filters-aside"
            class="min-w-0 self-start max-lg:pointer-events-none max-lg:fixed max-lg:bottom-0 max-lg:left-0 max-lg:right-0 max-lg:z-50 max-lg:flex max-lg:max-h-[min(92vh,40rem)] max-lg:w-full max-lg:translate-y-full max-lg:bg-white max-lg:opacity-0 max-lg:transition max-lg:duration-300 max-lg:ease-out max-lg:peer-checked:translate-y-0 max-lg:peer-checked:opacity-100 max-lg:peer-checked:pointer-events-auto max-lg:flex-col max-lg:overflow-hidden max-lg:rounded-t-3xl max-lg:shadow-[0_-8px_40px_rgba(15,23,42,0.18)] max-lg:ring-1 max-lg:ring-slate-200/90 max-lg:pb-[env(safe-area-inset-bottom)] lg:col-start-1 lg:col-span-3 lg:row-start-1 lg:row-span-2 lg:static lg:max-h-none lg:w-auto lg:translate-y-0 lg:bg-transparent lg:opacity-100 lg:overflow-y-visible lg:pb-0 lg:shadow-none lg:ring-0 lg:peer-checked:translate-y-0 lg:sticky lg:top-24 lg:z-10"
            aria-labelledby="room-browse-filters-title"
        >
            <div
                class="flex min-h-0 w-full min-w-0 flex-1 flex-col max-lg:max-h-full max-lg:overflow-hidden max-lg:bg-white lg:min-h-0"
            >
                <div
                    class="flex shrink-0 items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 py-3.5 max-lg:rounded-t-3xl lg:hidden"
                >
                    <h2
                        id="room-browse-filters-title"
                        class="text-base font-semibold tracking-tight text-slate-900"
                    >Filter by</h2>
                    <label
                        for="room-browse-filters-toggle"
                        class="inline-flex h-9 w-9 cursor-pointer select-none items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
                    >
                        <span class="sr-only">Close filters</span>
                        <x-lucide name="x" class="h-5 w-5" aria-hidden="true" />
                    </label>
                </div>
                <div
                    class="min-h-0 flex-1 overflow-y-auto overscroll-y-contain bg-white px-2 pb-2 pt-2 max-lg:max-h-full sm:px-3 lg:min-h-0 lg:overflow-visible lg:bg-transparent lg:px-0 lg:pb-0 lg:pt-0"
                >
                    @include('rooms.partials.room-filters-sidebar', [
                        'filters' => $filters,
                        'filterOptions' => $filterOptions,
                        'resetUrl' => $roomsResetUrl,
                    ])
                </div>
            </div>
        </aside>
    </form>
        <div
            class="flex min-w-0 flex-col gap-4 lg:col-span-9 lg:col-start-4 lg:row-start-2"
        >
            @if ($rooms->count() === 0)
                @include('rooms.partials.room-list-empty-state', [
                    'isFiltered' => $roomListIsFiltered,
                    'context' => 'guest',
                    'resetUrl' => $roomsResetUrl,
                ])
            @else
                <div class="min-w-0 space-y-6">
                    @if ($browseView === 'list')
                        <div class="flex min-w-0 flex-col gap-3">
                            @foreach ($rooms as $room)
                                @include('rooms.partials.room-browse-item', [
                                    'room' => $room,
                                    'layout' => 'list',
                                    'itemUrl' => route('rooms.show', $room->id),
                                    'hourlyLabel' => $room->hourlyRateLabel(),
                                    'showBrowseActions' => true,
                                    'browseDate' => $browseDate ?? now()->toDateString(),
                                    'favoriteRoomIds' => $favoriteRoomIds ?? [],
                                ])
                            @endforeach
                        </div>
                    @else
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($rooms as $room)
                                @include('rooms.partials.room-browse-item', [
                                    'room' => $room,
                                    'layout' => 'grid',
                                    'itemUrl' => route('rooms.show', $room->id),
                                    'hourlyLabel' => $room->hourlyRateLabel(),
                                    'showBrowseActions' => true,
                                    'browseDate' => $browseDate ?? now()->toDateString(),
                                    'favoriteRoomIds' => $favoriteRoomIds ?? [],
                                ])
                            @endforeach
                        </div>
                    @endif

                    <div>
                        {{ $rooms->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

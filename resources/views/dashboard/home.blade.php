@extends('layouts.app')

@section('title', __('Home'))

@section('content')
    @php
        $chipQs = fn (string $tab) => ['tab' => $tab];
        $chipBase =
            'shrink-0 snap-start rounded-full px-4 py-2 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/50 focus-visible:ring-offset-2';
        $chipOn = 'bg-gray-900 text-white shadow-sm';
        $chipOff = 'border border-gray-200/90 bg-white/90 text-gray-700 shadow-sm hover:border-gray-300 hover:bg-white';
    @endphp

    <h1 class="sr-only">{{ __('Home') }}</h1>

    <form
        method="GET"
        action="{{ route('rooms.index') }}"
        class="reservo-dashboard-in mt-6 min-w-0"
        role="search"
        aria-label="{{ __('Search rooms') }}"
    >
        <div class="relative min-w-0">
            <span
                class="pointer-events-none absolute left-3.5 top-1/2 z-[1] -translate-y-1/2 text-gray-400"
                aria-hidden="true"
            >
                <x-lucide name="search" class="h-4 w-4" />
            </span>
            <input
                type="search"
                name="search"
                value=""
                placeholder="{{ __('Search rooms') }}"
                autocomplete="off"
                enterkeyhint="search"
                class="app-field !mt-0 w-full !rounded-2xl !py-2.5 !pl-10 pr-4"
            >
        </div>
        <button type="submit" class="sr-only">{{ __('Search') }}</button>
    </form>

    <div
        class="reservo-dashboard-in mt-5 -mx-1 flex min-h-px gap-2 overflow-x-auto px-1 pb-1 [scrollbar-width:thin]"
        role="tablist"
        aria-label="{{ __('Home sections') }}"
    >
        @foreach ($homeTabs as $tabKey => $tabLabel)
            <a
                role="tab"
                aria-selected="{{ $homeTab === $tabKey ? 'true' : 'false' }}"
                href="{{ route('dashboard', $chipQs($tabKey)) }}"
                @class([$chipBase, $homeTab === $tabKey ? $chipOn : $chipOff])
            >{{ $tabLabel }}</a>
        @endforeach
    </div>

    @if ($homeTab === 'all')
        <div class="reservo-dashboard-in mt-8 space-y-10">
            <section aria-labelledby="home-upcoming-heading">
                <div class="flex items-center justify-between gap-3">
                    <h2 id="home-upcoming-heading" class="text-sm font-semibold uppercase tracking-[0.14em] text-gray-500">
                        {{ __('Upcoming') }}
                        <span class="tabular-nums font-semibold text-gray-900">{{ $upcomingCount }}</span>
                    </h2>
                    <a
                        href="{{ route('dashboard', $chipQs('upcoming')) }}"
                        class="text-xs font-medium text-teal-600 hover:text-teal-800"
                    >{{ __('See all') }}</a>
                </div>
                @if ($upcomingPreview->isEmpty())
                    <p class="mt-3 text-sm text-gray-600">{{ __('No upcoming bookings yet.') }}</p>
                @else
                    <ul class="mt-4 space-y-3">
                        @foreach ($upcomingPreview as $reservation)
                            @include('dashboard.partials.home-upcoming-card', ['reservation' => $reservation])
                        @endforeach
                    </ul>
                @endif
            </section>

            <section aria-labelledby="home-rooms-heading">
                <div class="flex items-center justify-between gap-3">
                    <h2 id="home-rooms-heading" class="text-sm font-semibold uppercase tracking-[0.14em] text-gray-500">
                        {{ __('Available rooms') }}
                        <span class="tabular-nums font-semibold text-gray-900">{{ $roomsTotal }}</span>
                    </h2>
                    <a
                        href="{{ route('dashboard', $chipQs('rooms')) }}"
                        class="text-xs font-medium text-teal-600 hover:text-teal-800"
                    >{{ __('See all') }}</a>
                </div>
                @if ($roomsPreview->isEmpty())
                    <p class="mt-3 text-sm text-gray-600">{{ __('No rooms to show right now.') }}</p>
                @else
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($roomsPreview as $room)
                            @include('rooms.partials.room-browse-item', [
                                'room' => $room,
                                'layout' => 'grid',
                                'itemUrl' => route('rooms.show', $room->id),
                                'hourlyLabel' => $room->hourlyRateLabel(),
                                'showBrowseActions' => true,
                                'browseDate' => $browseDate,
                                'favoriteRoomIds' => $favoriteRoomIds,
                            ])
                        @endforeach
                    </div>
                @endif
            </section>

            <section aria-labelledby="home-fav-heading">
                <div class="flex items-center justify-between gap-3">
                    <h2 id="home-fav-heading" class="text-sm font-semibold uppercase tracking-[0.14em] text-gray-500">
                        {{ __('Favourites') }}
                        <span class="tabular-nums font-semibold text-gray-900">{{ $favoriteRoomsCount }}</span>
                    </h2>
                    <a
                        href="{{ route('dashboard', $chipQs('favourites')) }}"
                        class="text-xs font-medium text-teal-600 hover:text-teal-800"
                    >{{ __('See all') }}</a>
                </div>
                @if ($favoritesPreview->isEmpty())
                    <p class="mt-3 text-sm text-gray-600">
                        {{ __('No favourite rooms yet—star a room from the list above or from the Rooms page.') }}
                    </p>
                @else
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($favoritesPreview as $room)
                            @include('rooms.partials.room-browse-item', [
                                'room' => $room,
                                'layout' => 'grid',
                                'itemUrl' => route('rooms.show', $room->id),
                                'hourlyLabel' => $room->hourlyRateLabel(),
                                'showBrowseActions' => true,
                                'browseDate' => $browseDate,
                                'favoriteRoomIds' => $favoriteRoomIds,
                            ])
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    @elseif ($homeTab === 'upcoming')
        <section class="reservo-dashboard-in mt-8" aria-labelledby="home-upcoming-full-heading">
            <h2 id="home-upcoming-full-heading" class="text-sm font-semibold uppercase tracking-[0.14em] text-gray-500">
                {{ __('Upcoming reservations') }}
                <span class="tabular-nums font-semibold text-gray-900">{{ $upcomingPage->total() }}</span>
            </h2>
            @if ($upcomingPage->isEmpty())
                <p class="mt-4 text-sm text-gray-600">{{ __('Nothing upcoming yet. Book a room from Available rooms.') }}</p>
            @else
                <ul class="mt-4 space-y-3">
                    @foreach ($upcomingPage as $reservation)
                        @include('dashboard.partials.home-upcoming-card', ['reservation' => $reservation])
                    @endforeach
                </ul>
                <div class="mt-6">
                    {{ $upcomingPage->links() }}
                </div>
            @endif
        </section>
    @elseif ($homeTab === 'rooms')
        <section class="reservo-dashboard-in mt-8" aria-labelledby="home-rooms-full-heading">
            <h2 id="home-rooms-full-heading" class="text-sm font-semibold uppercase tracking-[0.14em] text-gray-500">
                {{ __('Available rooms') }}
                <span class="tabular-nums font-semibold text-gray-900">{{ $roomsPage->total() }}</span>
            </h2>
            @if ($roomsPage->isEmpty())
                <p class="mt-4 text-sm text-gray-600">{{ __('No rooms to show right now.') }}</p>
            @else
                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($roomsPage as $room)
                        @include('rooms.partials.room-browse-item', [
                            'room' => $room,
                            'layout' => 'grid',
                            'itemUrl' => route('rooms.show', $room->id),
                            'hourlyLabel' => $room->hourlyRateLabel(),
                            'showBrowseActions' => true,
                            'browseDate' => $browseDate,
                            'favoriteRoomIds' => $favoriteRoomIds,
                        ])
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $roomsPage->links() }}
                </div>
            @endif
        </section>
    @else
        <section class="reservo-dashboard-in mt-8" aria-labelledby="home-fav-full-heading">
            <h2 id="home-fav-full-heading" class="text-sm font-semibold uppercase tracking-[0.14em] text-gray-500">
                {{ __('Favourite rooms') }}
                <span class="tabular-nums font-semibold text-gray-900">{{ $favoritesPage->total() }}</span>
            </h2>
            @if ($favoritesPage->isEmpty())
                <p class="mt-4 text-sm text-gray-600">{{ __('You have no favourites yet. Save rooms from Available rooms.') }}</p>
            @else
                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($favoritesPage as $room)
                        @include('rooms.partials.room-browse-item', [
                            'room' => $room,
                            'layout' => 'grid',
                            'itemUrl' => route('rooms.show', $room->id),
                            'hourlyLabel' => $room->hourlyRateLabel(),
                            'showBrowseActions' => true,
                            'browseDate' => $browseDate,
                            'favoriteRoomIds' => $favoriteRoomIds,
                        ])
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $favoritesPage->links() }}
                </div>
            @endif
        </section>
    @endif
@endsection

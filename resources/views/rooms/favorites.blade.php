@extends('layouts.app')

@section('title', __('Favourite rooms'))

@section('content')
    <x-page-breadcrumbs
        class="mb-4"
        :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => __('Favourite rooms')],
        ]"
    />
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">{{ __('Collections') }}</div>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                {{ __('Favourite rooms') }}
            </h1>
            <p class="mt-2 max-w-xl text-sm text-gray-600">
                {{ __('Rooms you saved—open one for availability or jump straight into booking.') }}
            </p>
        </div>
        <a
            href="{{ route('rooms.index') }}"
            class="inline-flex shrink-0 items-center gap-2 self-start rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-800 shadow-sm transition hover:border-gray-300 hover:bg-gray-50 sm:self-auto"
        >
            <x-lucide name="door-open" class="h-4 w-4 text-gray-500" aria-hidden="true" />
            {{ __('Browse all rooms') }}
        </a>
    </div>

    <div class="mt-10 min-w-0">
        @if ($rooms->count() === 0)
            <div
                class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-gray-200/90 bg-white/90 px-6 py-14 text-center"
            >
                <div class="rounded-2xl bg-amber-50 p-4 text-amber-700 ring-1 ring-amber-200/70">
                    <x-lucide name="bookmark" class="h-8 w-8" aria-hidden="true" />
                </div>
                <p class="mt-4 text-sm font-semibold text-gray-900">{{ __('No favourites yet') }}</p>
                <p class="mt-1 max-w-md text-xs text-gray-600">
                    {{ __('Use the bookmark on any room card to save it here for quick access.') }}
                </p>
                <a
                    href="{{ route('rooms.index') }}"
                    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-gray-800"
                >
                    {{ __('Browse rooms') }}
                    <x-lucide name="chevron-right" class="h-4 w-4" aria-hidden="true" />
                </a>
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
            <div class="mt-8">
                {{ $rooms->links() }}
            </div>
        @endif
    </div>
@endsection

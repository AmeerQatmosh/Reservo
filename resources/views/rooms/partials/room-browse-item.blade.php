{{--
    $room: Room model or demo array; $layout: grid|list; $itemUrl; $hourlyLabel: string|null;
    Optional: $showBrowseActions bool, $browseDate Y-m-d, $favoriteRoomIds int[], $demoContext bool (uses demo routes when true)
--}}
@php
    $layout = $layout ?? 'grid';
    $isArr = is_array($room);
    if ($isArr) {
        $name = (string) ($room['name'] ?? '');
        $location = $room['location'] ?? null;
        $capacity = (int) ($room['capacity'] ?? 0);
        $sizeSqm = $room['size_sqm'] ?? null;
        $imageUrl = $room['image_url'] ?? null;
        $description = (string) ($room['description'] ?? '');
        $amenities = is_array($room['amenities'] ?? null) ? $room['amenities'] : [];
        $roomPk = (int) ($room['id'] ?? 0);
    } else {
        /** @var \App\Models\Room $room */
        $name = $room->name;
        $location = $room->location;
        $capacity = (int) $room->capacity;
        $sizeSqm = $room->size_sqm;
        $imageUrl = $room->image_url;
        $description = (string) $room->description;
        $amenities = is_array($room->amenities) ? $room->amenities : [];
        $roomPk = (int) $room->id;
    }
    $hourlyLabel = $hourlyLabel ?? null;
    $photoGrad = $isArr ? 'from-slate-100 to-indigo-50' : 'from-gray-100 to-gray-200';
    $showBrowseActions = $showBrowseActions ?? false;
    $browseDate = $browseDate ?? now()->toDateString();
    $favoriteRoomIds = isset($favoriteRoomIds) && is_array($favoriteRoomIds) ? $favoriteRoomIds : [];
    $demoContext = (bool) ($demoContext ?? false);
    $isFavorited = in_array($roomPk, $favoriteRoomIds, true);
    $bookUrl = $demoContext
        ? route('demo.rooms.quickBook', ['id' => $roomPk, 'date' => $browseDate])
        : route('rooms.quickBook', ['room' => $roomPk, 'date' => $browseDate]);
@endphp

@if ($layout === 'list')
    <article
        class="group flex gap-3 rounded-2xl border border-white/70 bg-white/90 p-3 shadow-sm transition hover:border-gray-200 hover:shadow-md sm:gap-4 sm:p-4"
    >
        <a
            href="{{ $itemUrl }}"
            class="relative h-24 w-32 shrink-0 overflow-hidden rounded-xl bg-gray-100 sm:h-[7.25rem] sm:w-40"
            aria-label="{{ __('View :name', ['name' => $name]) }}"
        >
            @if ($imageUrl)
                <img
                    src="{{ $imageUrl }}"
                    alt=""
                    class="h-full w-full object-cover"
                    loading="lazy"
                >
            @else
                <div
                    class="flex h-full w-full items-center justify-center bg-gradient-to-br {{ $photoGrad }} text-xs text-gray-500"
                >
                    {{ __('No photo') }}
                </div>
            @endif
        </a>
        <div class="flex min-w-0 flex-1 flex-col justify-center">
            <div class="flex items-start justify-between gap-2">
                <a href="{{ $itemUrl }}" class="min-w-0">
                    <div class="text-base font-semibold tracking-tight text-gray-900 hover:text-gray-950 sm:text-lg">
                        {{ $name }}
                    </div>
                </a>
                @if ($showBrowseActions)
                    <div class="flex shrink-0 items-center gap-1">
                        @if ($demoContext || auth()->check())
                            <form method="post" action="{{ $demoContext ? route('demo.rooms.favorite.toggle', ['id' => $roomPk]) : route('rooms.favorite.toggle', ['room' => $roomPk]) }}" class="contents">
                                @csrf
                                <button
                                    type="submit"
                                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-gray-500 ring-1 ring-gray-200/80 transition hover:bg-amber-50 hover:text-amber-700 hover:ring-amber-200 {{ $isFavorited ? '!text-amber-700 !ring-amber-200' : '' }}"
                                    title="{{ $isFavorited ? __('Remove from favourites') : __('Add to favourites') }}"
                                    aria-label="{{ $isFavorited ? __('Remove from favourites') : __('Add to favourites') }}"
                                    aria-pressed="{{ $isFavorited ? 'true' : 'false' }}"
                                >
                                    <x-lucide
                                        name="bookmark"
                                        class="h-[1.125rem] w-[1.125rem] {{ $isFavorited ? 'fill-current' : '' }}"
                                        aria-hidden="true"
                                    />
                                </button>
                            </form>
                            <a
                                href="{{ $bookUrl }}"
                                class="inline-flex h-9 items-center whitespace-nowrap rounded-full bg-gray-900 px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-gray-800"
                            >
                                {{ __('Book') }}
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-gray-500 ring-1 ring-gray-200/80 transition hover:bg-gray-50 hover:text-gray-900"
                                title="{{ __('Sign in to save favourites') }}"
                                aria-label="{{ __('Sign in to save favourites') }}"
                            >
                                <x-lucide name="bookmark" class="h-[1.125rem] w-[1.125rem]" aria-hidden="true" />
                            </a>
                            <a
                                href="{{ $bookUrl }}"
                                class="inline-flex h-9 items-center whitespace-nowrap rounded-full bg-gray-900 px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-gray-800"
                            >
                                {{ __('Book') }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>
            @if ($location)
                <p class="mt-0.5 text-xs leading-5 text-gray-500">{{ $location }}</p>
            @endif
            <div class="mt-2 flex flex-wrap gap-2">
                <span
                    class="inline-flex rounded-full border border-gray-200 bg-gray-50 px-2.5 py-0.5 text-xs font-medium text-gray-700"
                >{{ __('Up to :n people', ['n' => $capacity]) }}</span>
                @if ($sizeSqm)
                    <span
                        class="inline-flex rounded-full border border-gray-200 bg-white px-2.5 py-0.5 text-xs font-medium text-gray-600"
                    >{{ $sizeSqm }} m²</span>
                @endif
                @if ($hourlyLabel)
                    <span
                        class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-800"
                    >{{ $hourlyLabel }}</span>
                @endif
            </div>
            @if (count($amenities))
                <div class="mt-2 flex flex-wrap gap-1.5">
                    @foreach (array_slice($amenities, 0, 3) as $amenity)
                        <span
                            class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700"
                        >{{ $amenity }}</span>
                    @endforeach
                    @if (count($amenities) > 3)
                        <span class="rounded-md bg-slate-50 px-2 py-0.5 text-[11px] text-slate-500"
                        >+{{ count($amenities) - 3 }} {{ __('more') }}</span>
                    @endif
                </div>
            @endif
            @if ($description !== '')
                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-gray-700">
                    @if ($isArr)
                        {{ \Illuminate\Support\Str::limit($description, 160) }}
                    @else
                        {{ $description }}
                    @endif
                </p>
            @endif
        </div>
    </article>
@else
    <article
        class="overflow-hidden rounded-3xl border border-white/70 bg-white/90 shadow-sm transition hover:-translate-y-0.5 hover:border-gray-200 hover:shadow-md"
    >
        <div class="relative aspect-[16/10] overflow-hidden bg-gray-100">
            <a href="{{ $itemUrl }}" class="block h-full w-full" aria-label="{{ __('View :name', ['name' => $name]) }}">
                @if ($imageUrl)
                    <img
                        src="{{ $imageUrl }}"
                        alt=""
                        class="h-full w-full object-cover"
                        loading="lazy"
                    >
                @else
                    <div
                        class="flex h-full w-full items-center justify-center bg-gradient-to-br {{ $photoGrad }} text-xs text-gray-500"
                    >
                        {{ __('No photo') }}
                    </div>
                @endif
            </a>
            @if ($showBrowseActions)
                <div
                    class="absolute right-2 top-2 z-10 flex items-center gap-1 sm:right-3 sm:top-3 sm:gap-1.5"
                >
                    @if ($demoContext || auth()->check())
                        <form method="post" action="{{ $demoContext ? route('demo.rooms.favorite.toggle', ['id' => $roomPk]) : route('rooms.favorite.toggle', ['room' => $roomPk]) }}">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/95 text-gray-600 shadow-sm ring-1 ring-gray-200/90 backdrop-blur-sm transition hover:bg-white hover:text-amber-700 hover:ring-amber-200 {{ $isFavorited ? '!text-amber-700 !ring-amber-200' : '' }}"
                                title="{{ $isFavorited ? __('Remove from favourites') : __('Add to favourites') }}"
                                aria-label="{{ $isFavorited ? __('Remove from favourites') : __('Add to favourites') }}"
                                aria-pressed="{{ $isFavorited ? 'true' : 'false' }}"
                            >
                                <x-lucide
                                    name="bookmark"
                                    class="h-[1.125rem] w-[1.125rem] {{ $isFavorited ? 'fill-current' : '' }}"
                                    aria-hidden="true"
                                />
                            </button>
                        </form>
                        <a
                            href="{{ $bookUrl }}"
                            class="inline-flex h-9 items-center rounded-full bg-gray-900 px-3 text-xs font-semibold text-white shadow-sm ring-1 ring-gray-900/10 transition hover:bg-gray-800"
                        >
                            {{ __('Book') }}
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/95 text-gray-600 shadow-sm ring-1 ring-gray-200/90 backdrop-blur-sm transition hover:bg-white hover:text-gray-900"
                            title="{{ __('Sign in to save favourites') }}"
                            aria-label="{{ __('Sign in to save favourites') }}"
                        >
                            <x-lucide name="bookmark" class="h-[1.125rem] w-[1.125rem]" aria-hidden="true" />
                        </a>
                        <a
                            href="{{ $bookUrl }}"
                            class="inline-flex h-9 items-center rounded-full bg-gray-900 px-3 text-xs font-semibold text-white shadow-sm ring-1 ring-gray-900/10 transition hover:bg-gray-800"
                        >
                            {{ __('Book') }}
                        </a>
                    @endif
                </div>
            @endif
        </div>
        <div class="p-5">
            <div class="flex items-start justify-between gap-3">
                <a href="{{ $itemUrl }}" class="min-w-0 flex-1">
                    <div class="text-lg font-semibold tracking-tight text-gray-900 hover:text-gray-950">
                        {{ $name }}
                    </div>
                </a>
            </div>
            @if ($location)
                <p class="mt-1 text-xs leading-5 text-gray-500">
                    {{ $location }}
                </p>
            @endif
            <div class="mt-2 flex flex-wrap gap-2">
                <span
                    class="inline-flex rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-medium text-gray-700"
                >
                    {{ __('Up to :n people', ['n' => $capacity]) }}
                </span>
                @if ($sizeSqm)
                    <span
                        class="inline-flex rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-600"
                    >
                        {{ $sizeSqm }} m²
                    </span>
                @endif
                @if ($hourlyLabel)
                    <span
                        class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-800"
                    >
                        {{ $hourlyLabel }}
                    </span>
                @endif
            </div>
            @if (count($amenities))
                <div class="mt-3 flex flex-wrap gap-1.5">
                    @foreach (array_slice($amenities, 0, 4) as $amenity)
                        <span
                            class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700"
                        >{{ $amenity }}</span>
                    @endforeach
                    @if (count($amenities) > 4)
                        <span
                            class="rounded-md bg-slate-50 px-2 py-0.5 text-[11px] text-slate-500"
                        >+{{ count($amenities) - 4 }} {{ __('more') }}</span>
                    @endif
                </div>
            @endif
            <div class="mt-4 line-clamp-3 text-sm leading-6 text-gray-700">
                @if ($isArr)
                    {{ \Illuminate\Support\Str::limit($description, 220) }}
                @else
                    {{ $description }}
                @endif
            </div>
        </div>
    </article>
@endif

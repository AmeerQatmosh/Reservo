{{--
    Room context for reservations: photo, metadata, amenities, description.
    @var \App\Models\Room|null $room
--}}
@php
    $showViewLink = $showViewLink ?? true;
    $hideImage = $hideImage ?? false;
    $showTitle = $showTitle ?? true;
    $titleHeading = in_array($titleHeading ?? 'h3', ['h2', 'h3'], true) ? ($titleHeading ?? 'h3') : 'h3';
    $imageWrapperClass = $imageWrapperClass ?? 'h-44 w-full sm:h-52 sm:w-52 sm:shrink-0 lg:w-60';
@endphp

@if (! $room)
    <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50/80 p-6 text-sm text-gray-600">
        This room is no longer listed or was removed. You can still change or cancel the reservation below.
    </div>
@else
    <div class="@if (! $hideImage) flex flex-col gap-5 sm:flex-row sm:items-start @endif">
        @if (! $hideImage)
            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-gray-100 {{ $imageWrapperClass }} shrink-0">
                @if ($room->image_url)
                    @if ($showViewLink && ! $room->trashed())
                        <a href="{{ route('rooms.show', $room->id) }}" class="block h-full w-full">
                            <img
                                src="{{ $room->image_url }}"
                                alt="{{ $room->name }}"
                                class="h-full w-full object-cover transition hover:opacity-95"
                                loading="lazy"
                            >
                        </a>
                    @else
                        <img
                            src="{{ $room->image_url }}"
                            alt="{{ $room->name }}"
                            class="h-full w-full object-cover"
                            loading="lazy"
                        >
                    @endif
                @else
                    <div class="flex h-full min-h-[10rem] w-full items-center justify-center text-xs text-gray-500">
                        No photo
                    </div>
                @endif
            </div>
        @endif

        <div class="min-w-0 @if (! $hideImage) flex-1 @endif">
            @if ($showTitle)
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        @if ($titleHeading === 'h2')
                            <h2 class="text-2xl font-semibold tracking-tight text-gray-900">{{ $room->name }}</h2>
                        @else
                            <h3 class="text-lg font-semibold tracking-tight text-gray-900">{{ $room->name }}</h3>
                        @endif
                        @if ($room->location)
                            <p class="mt-1 text-sm text-gray-600">{{ $room->location }}</p>
                        @endif
                    </div>
                    @if ($showViewLink && ! $room->trashed())
                        <a href="{{ route('rooms.show', $room->id) }}" class="shrink-0 text-sm font-medium text-gray-700 underline decoration-gray-300 underline-offset-2 hover:text-gray-900">
                            Full room page
                        </a>
                    @endif
                </div>
            @else
                @if ($showViewLink && ! $room->trashed())
                    <div class="flex justify-end">
                        <a href="{{ route('rooms.show', $room->id) }}" class="text-sm font-medium text-gray-700 underline decoration-gray-300 underline-offset-2 hover:text-gray-900">
                            Full room page
                        </a>
                    </div>
                @endif
            @endif

            <div class="@if ($showTitle) mt-3 @else mt-0 @endif flex flex-wrap gap-2">
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

            @if (is_array($room->amenities) && count($room->amenities))
                <ul class="mt-3 flex flex-wrap gap-1.5">
                    @foreach ($room->amenities as $amenity)
                        <li class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">{{ $amenity }}</li>
                    @endforeach
                </ul>
            @endif

            <div class="mt-4 text-sm leading-6 text-gray-700">
                <p class="whitespace-pre-line">{{ $room->description }}</p>
            </div>
        </div>
    </div>
@endif

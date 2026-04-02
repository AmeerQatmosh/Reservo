{{-- @var array<string, mixed> $room --}}
@php
    $room = \App\Support\DemoState::normalizeRoom($room);
    $amenities = is_array($room['amenities'] ?? null) ? $room['amenities'] : [];
@endphp

<div class="flex flex-col gap-5 sm:flex-row sm:items-start">
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-gray-100 h-44 w-full sm:h-52 sm:w-52 sm:shrink-0 lg:w-60 shrink-0">
        @if (! empty($room['image_url']))
            <a href="{{ route('demo.room.show', $room['id']) }}" class="block h-full w-full">
                <img src="{{ $room['image_url'] }}" alt="{{ $room['name'] }}" class="h-full w-full object-cover transition hover:opacity-95" loading="lazy">
            </a>
        @else
            <div class="flex h-full min-h-[10rem] w-full items-center justify-center text-xs text-gray-500">
                No photo
            </div>
        @endif
    </div>

    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">{{ $room['name'] }}</h3>
                @if (! empty($room['location']))
                    <p class="mt-1 text-sm text-gray-600">{{ $room['location'] }}</p>
                @endif
            </div>
            <a href="{{ route('demo.room.show', $room['id']) }}" class="shrink-0 text-sm font-medium text-gray-700 underline decoration-gray-300 underline-offset-2 hover:text-gray-900">
                Full room page
            </a>
        </div>

        <div class="mt-3 flex flex-wrap gap-2">
            <span class="inline-flex rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-medium text-gray-700">
                Up to {{ $room['capacity'] }} people
            </span>
            @if (! empty($room['size_sqm']))
                <span class="inline-flex rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-600">
                    {{ $room['size_sqm'] }} m²
                </span>
            @endif
            @if (\App\Support\DemoState::hourlyRateLabel(isset($room['hourly_rate']) ? (float) $room['hourly_rate'] : null))
                <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-800">
                    {{ \App\Support\DemoState::hourlyRateLabel(isset($room['hourly_rate']) ? (float) $room['hourly_rate'] : null) }}
                </span>
            @endif
        </div>

        @if (count($amenities))
            <ul class="mt-3 flex flex-wrap gap-1.5">
                @foreach ($amenities as $amenity)
                    <li class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">{{ $amenity }}</li>
                @endforeach
            </ul>
        @endif

        <div class="mt-4 text-sm leading-6 text-gray-700">
            <p class="whitespace-pre-line">{{ $room['description'] ?? '' }}</p>
        </div>
    </div>
</div>

@extends('layouts.app')

@section('title', 'Admin Rooms: '.$room['name'])

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('demo.admin.rooms') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">← Admin Rooms</a>
            <div class="mt-3 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">{{ $room['name'] }}</h1>
                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">Sandbox</span>
            </div>
            <p class="mt-2 text-sm text-gray-600">Session-only room record (same layout idea as production admin).</p>
        </div>
        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:flex-wrap">
            <a href="{{ route('demo.room.show', $room['id']) }}" class="inline-flex w-full items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm hover:bg-gray-50 sm:w-auto">
                Public room page
            </a>
            <form method="POST" action="{{ route('demo.admin.rooms.destroy', $room['id']) }}" class="w-full sm:w-auto" data-confirm-message="Remove this sandbox room and its bookings?" data-confirm-variant="danger" data-confirm-button-label="Remove">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-medium text-red-900 hover:bg-red-100 sm:w-auto">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="mt-8 overflow-hidden rounded-3xl border border-white/70 bg-white shadow-sm">
        <div class="aspect-[21/9] max-h-[22rem] w-full overflow-hidden bg-gray-100 sm:aspect-[2.4/1]">
            @if (! empty($room['image_url']))
                <img src="{{ $room['image_url'] }}" alt="{{ $room['name'] }}" class="h-full w-full object-cover" loading="eager">
            @else
                <div class="flex h-full min-h-[12rem] w-full items-center justify-center text-sm text-gray-500">
                    No photo
                </div>
            @endif
        </div>
    </div>

    <div class="mt-8 rounded-3xl border border-white/70 bg-white/90 p-7 shadow-sm">
        <h2 class="text-sm font-semibold uppercase tracking-[0.15em] text-gray-500">Details</h2>
        <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
            <div>
                <dt class="text-gray-500">Location</dt>
                <dd class="font-medium text-gray-900">{{ $room['location'] ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Capacity</dt>
                <dd class="font-medium text-gray-900">{{ $room['capacity'] }} people</dd>
            </div>
            <div>
                <dt class="text-gray-500">Size</dt>
                <dd class="font-medium text-gray-900">{{ $room['size_sqm'] ? $room['size_sqm'].' m²' : '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Rate</dt>
                <dd class="font-medium text-gray-900">{{ \App\Support\DemoState::hourlyRateLabel(isset($room['hourly_rate']) ? (float) $room['hourly_rate'] : null) ?? '—' }}</dd>
            </div>
        </dl>
        @php
            $amenities = is_array($room['amenities'] ?? null) ? $room['amenities'] : [];
        @endphp
        @if (count($amenities))
            <h3 class="mt-6 text-xs font-semibold uppercase tracking-[0.15em] text-gray-500">Amenities</h3>
            <ul class="mt-2 flex flex-wrap gap-2">
                @foreach ($amenities as $amenity)
                    <li class="rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-sm text-gray-800">{{ $amenity }}</li>
                @endforeach
            </ul>
        @endif
        <h3 class="mt-6 text-xs font-semibold uppercase tracking-[0.15em] text-gray-500">Description</h3>
        <p class="mt-2 whitespace-pre-line text-sm text-gray-800">{{ $room['description'] ?? '' }}</p>
    </div>
@endsection

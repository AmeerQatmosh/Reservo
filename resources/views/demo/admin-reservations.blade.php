@extends('layouts.app')

@section('title', 'Admin Reservations')

@section('content')
    <div class="flex justify-end">
        <a href="{{ route('demo.hub') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Sandbox home</a>
    </div>

    <div class="mt-6 rounded-2xl border border-white/70 bg-white/90 p-5 shadow-sm">
        <form method="GET" action="{{ route('demo.admin.reservations') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-[minmax(220px,1fr)_minmax(200px,0.9fr)_minmax(0,1.2fr)_auto] xl:items-end">
            <div>
                <label for="room_id" class="block text-sm font-medium text-gray-900">Room</label>
                <select id="room_id" name="room_id" class="app-field">
                    <option value="">All rooms</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room['id'] }}" @selected((string) ($filters['room_id'] ?? '') === (string) $room['id'])>{{ $room['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="date" class="block text-sm font-medium text-gray-900">Date</label>
                <input id="date" name="date" type="date" value="{{ $filters['date'] ?? '' }}" class="app-field">
            </div>

            <div>
                <label for="guest" class="block text-sm font-medium text-gray-900">Guest label</label>
                <input id="guest" name="guest" type="text" value="{{ $filters['guest'] ?? '' }}" placeholder="e.g. Demo guest" class="app-field">
            </div>

            <div class="flex flex-row flex-wrap items-end gap-2 xl:flex-nowrap xl:justify-end">
                <button
                    type="submit"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-teal-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-teal-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/35"
                >
                    <x-lucide name="filter" class="h-4 w-4 shrink-0" aria-hidden="true" />
                    Filter
                </button>
                <a
                    href="{{ route('demo.admin.reservations') }}"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:border-gray-400 hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/15"
                >
                    <x-lucide name="rotate-ccw" class="h-4 w-4 shrink-0" aria-hidden="true" />
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if ($reservations->count() === 0)
        <div class="mt-6 rounded-lg border border-white/70 bg-white/90 p-6 text-sm text-gray-700 shadow-sm">
            No reservations match the current filters.
        </div>
    @else
        <div class="mt-6 overflow-hidden rounded-lg border border-white/70 bg-white/90 shadow-sm">
            <div class="app-table-scroll app-table-scroll--wide">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                        <tr>
                            <th class="w-24 px-4 py-3">Room</th>
                            <th class="px-4 py-3">Room details</th>
                            <th class="px-4 py-3">Guest label</th>
                            <th class="whitespace-nowrap px-4 py-3">{{ __('Date & time') }}</th>
                            <th class="w-24 px-4 py-3">Est.</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($reservations as $r)
                            @php
                                $rid = (int) ($r['room_id'] ?? 0);
                                $dRoom = $roomById[$rid] ?? null;
                            @endphp
                            <tr class="align-top">
                                <td class="px-4 py-3">
                                    @if ($dRoom)
                                        <a href="{{ route('demo.room.show', $rid) }}" class="block h-14 w-[4.5rem] overflow-hidden rounded-lg bg-gray-100 ring-1 ring-gray-200/80">
                                            @if (! empty($dRoom['image_url']))
                                                <img src="{{ $dRoom['image_url'] }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                            @else
                                                <span class="flex h-full items-center justify-center text-[10px] text-gray-400">—</span>
                                            @endif
                                        </a>
                                    @else
                                        <div class="flex h-14 w-[4.5rem] items-center justify-center rounded-lg bg-gray-100 text-[10px] text-gray-400">—</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    @if ($dRoom)
                                        <a href="{{ route('demo.room.show', $rid) }}" class="font-semibold hover:underline">{{ $dRoom['name'] }}</a>
                                        @if (! empty($dRoom['location']))
                                            <div class="mt-1 max-w-xs text-xs font-normal text-gray-600">{{ $dRoom['location'] }}</div>
                                        @endif
                                        <div class="mt-1 text-xs font-normal text-gray-500">
                                            {{ $dRoom['capacity'] }} people
                                            @if (! empty($dRoom['size_sqm']))
                                                · {{ $dRoom['size_sqm'] }} m²
                                            @endif
                                        </div>
                                    @else
                                        {{ $roomNames[$rid] ?? '—' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-800">{{ $r['label'] ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <div class="text-gray-900">{{ $r['date'] }}</div>
                                    <div class="text-xs tabular-nums text-gray-600">{{ substr($r['start_time'], 0, 5) }}–{{ substr($r['end_time'], 0, 5) }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ \App\Support\DemoState::reservationEstimateLabel($r, $dRoom) ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <div class="app-table-actions">
                                        <form method="POST" action="{{ route('demo.reservations.destroy', $r['id']) }}" class="inline-flex shrink-0" data-confirm-message="Remove this sandbox reservation?" data-confirm-variant="danger" data-confirm-button-label="Remove">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="app-table-action app-table-action-danger">{{ __('Cancel') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $reservations->links() }}
        </div>
    @endif
@endsection

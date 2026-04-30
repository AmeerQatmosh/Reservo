@extends('layouts.app')

@section('title', 'Admin Reservations')

@section('content')
    @php
        $adminResRoomId = (string) ($filters['room_id'] ?? '');
        $adminResDate = (string) ($filters['date'] ?? '');
    @endphp
    <x-page-breadcrumbs
        class="mb-6"
        :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Admin'],
            ['label' => 'Reservations'],
        ]"
    />

    <div class="flex flex-col gap-8 lg:flex-row lg:items-start">
        <div
            class="w-full min-w-0 lg:w-[24rem] lg:flex-none lg:sticky lg:top-[calc(5rem+env(safe-area-inset-top,0px))] lg:z-10 lg:self-start"
        >
            <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-sm">
                <div class="mb-5">
                    <h2 class="text-base font-semibold text-gray-900">{{ __('Search & filters') }}</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Narrow the list, then use Filter. Reset clears all fields.') }}</p>
                </div>

                <form method="GET" action="{{ route('admin.reservations.index') }}" class="space-y-5">
                    <x-reservo-form-select
                        name="room_id"
                        hidden-id="room_id"
                        trigger-id="admin_res_room_trigger"
                        listbox-id="admin_res_room_listbox"
                        label="Room"
                        placeholder="All rooms"
                        :value="$adminResRoomId"
                    >
                        <button
                            type="button"
                            role="option"
                            data-value=""
                            class="reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm text-gray-500 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2"
                            aria-selected="{{ $adminResRoomId === '' ? 'true' : 'false' }}"
                        >All rooms</button>
                        @foreach ($rooms as $room)
                            <button
                                type="button"
                                role="option"
                                data-value="{{ $room->id }}"
                                @class([
                                    'reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm break-words hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2',
                                    'bg-gray-100 font-medium text-gray-900' => $adminResRoomId === (string) $room->id,
                                    'text-gray-900' => $adminResRoomId !== (string) $room->id,
                                ])
                                aria-selected="{{ $adminResRoomId === (string) $room->id ? 'true' : 'false' }}"
                            >{{ $room->name }}</button>
                        @endforeach
                    </x-reservo-form-select>

                    <div>
                        <label for="user" class="block text-sm font-medium text-gray-900">{{ __('User') }}</label>
                        <input
                            id="user"
                            name="user"
                            type="text"
                            value="{{ $filters['user'] ?? '' }}"
                            placeholder="{{ __('Name or email') }}"
                            class="app-field mt-1.5 w-full"
                        >
                    </div>

                    <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-b from-slate-50/90 to-white p-4 shadow-sm ring-1 ring-slate-950/[0.03]">
                        <div class="space-y-1">
                            <h3 class="text-sm font-semibold text-gray-900">{{ __('Date') }}</h3>
                            <p class="text-xs leading-snug text-gray-500">{{ __('Pick a day or use Any date for all days.') }}</p>
                        </div>
                        <div class="mt-4 w-full min-w-0">
                            <x-reservation-date-mini-calendar
                                class="!rounded-2xl !p-3 shadow-sm ring-1 ring-slate-950/[0.04] w-full max-w-none"
                                :value="$adminResDate"
                                min="2000-01-01"
                                :bookings="$miniCalendarBookings ?? []"
                                :allow-empty="true"
                            />
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-stretch">
                        <button
                            type="submit"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-2xl bg-teal-600 px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-teal-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/35"
                        >
                            <x-lucide name="filter" class="h-4 w-4 shrink-0" aria-hidden="true" />
                            {{ __('Filter') }}
                        </button>
                        <a
                            href="{{ route('admin.reservations.index') }}"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-sm transition hover:border-gray-400 hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/15"
                        >
                            <x-lucide name="rotate-ccw" class="h-4 w-4 shrink-0" aria-hidden="true" />
                            {{ __('Reset') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl">{{ __('User Reservations') }}</h1>
                </div>
            </div>

            @if ($reservations->count() === 0)
                <div class="mt-6 rounded-3xl border border-white/70 bg-white/90 p-6 text-sm text-gray-700 shadow-sm">
                    {{ __('No reservations match the current filters.') }}
                </div>
            @else
                <div class="mt-6 overflow-hidden rounded-lg border border-white/70 bg-white/90 shadow-sm">
                    <div class="app-table-scroll app-table-scroll--wide">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                                <tr>
                                    <th class="w-24 px-4 py-3">Room</th>
                                    <th class="px-4 py-3">Room details</th>
                                    <th class="px-4 py-3">User</th>
                                    <th class="whitespace-nowrap px-4 py-3">{{ __('Date & time') }}</th>
                                    <th class="w-24 px-4 py-3">Est.</th>
                                    <th class="px-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($reservations as $reservation)
                                    <tr class="align-top">
                                        <td class="px-4 py-3">
                                            @if ($reservation->room)
                                                <a href="{{ route('admin.rooms.show', $reservation->room->id) }}" class="block h-14 w-[4.5rem] overflow-hidden rounded-lg bg-gray-100 ring-1 ring-gray-200/80">
                                                    @if ($reservation->room->image_url)
                                                        <img src="{{ $reservation->room->image_url }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                                    @else
                                                        <span class="flex h-full items-center justify-center text-[10px] text-gray-400">—</span>
                                                    @endif
                                                </a>
                                            @else
                                                <div class="flex h-14 w-[4.5rem] items-center justify-center rounded-lg bg-gray-100 text-[10px] text-gray-400">—</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            @if ($reservation->room)
                                                <a href="{{ route('admin.rooms.show', $reservation->room->id) }}" class="font-semibold hover:underline">{{ $reservation->room->name }}</a>
                                                @if ($reservation->room->location)
                                                    <div class="mt-1 max-w-xs text-xs font-normal text-gray-600">{{ $reservation->room->location }}</div>
                                                @endif
                                                <div class="mt-1 text-xs font-normal text-gray-500">
                                                    {{ $reservation->room->capacity }} people
                                                    @if ($reservation->room->size_sqm)
                                                        · {{ $reservation->room->size_sqm }} m²
                                                    @endif
                                                </div>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-gray-900">{{ $reservation->user?->name ?? '—' }}</div>
                                            <div class="text-xs text-gray-600">{{ $reservation->user?->email ?? '' }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-gray-900">{{ $reservation->date }}</div>
                                            <div class="text-xs tabular-nums text-gray-600">{{ substr($reservation->start_time, 0, 5) }}–{{ substr($reservation->end_time, 0, 5) }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">{{ $reservation->estimatedTotalLabel() ?? '—' }}</td>
                                        <td class="px-4 py-3">
                                            <div class="app-table-actions">
                                                <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="app-table-action">
                                                    {{ __('Edit') }}
                                                </a>
                                                <form method="POST" action="{{ route('admin.reservations.destroy', $reservation->id) }}" class="inline-flex shrink-0" data-confirm-message="Are you sure you want to cancel this reservation?" data-confirm-variant="danger" data-confirm-button-label="Cancel reservation">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="app-table-action app-table-action-danger">
                                                        {{ __('Cancel') }}
                                                    </button>
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
        </div>
    </div>
@endsection

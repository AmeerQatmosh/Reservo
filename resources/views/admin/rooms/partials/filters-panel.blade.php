@php
    $f = $filters ?? [];
    $includeStatus = $includeStatus ?? true;
    $resetUrl = $resetUrl ?? route('admin.rooms.index');
@endphp

<div
    class="rounded-2xl border border-white/70 bg-white/90 p-4 shadow-sm ring-1 ring-gray-900/[0.04] sm:p-5 max-lg:rounded-none max-lg:border-0 max-lg:bg-white max-lg:shadow-none max-lg:ring-0 max-lg:p-3 sm:max-lg:p-4"
>
    <p
        class="hidden text-xs font-semibold uppercase tracking-[0.12em] text-gray-500 lg:block"
    >Refine</p>

    @if ($includeStatus)
        <div class="mt-3 min-w-0 max-lg:mt-0">
            @php
                $st = (string) ($f['status'] ?? 'all');
            @endphp
            <x-reservo-form-select
                name="status"
                hidden-id="status"
                trigger-id="status_trigger"
                listbox-id="status_listbox"
                label="Status"
                placeholder="Status"
                :value="$st"
            >
                <button
                    type="button"
                    role="option"
                    data-value="all"
                    @class([
                        'reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2',
                        'bg-gray-100 font-medium text-gray-900' => $st === 'all',
                        'text-gray-900' => $st !== 'all',
                    ])
                    aria-selected="{{ $st === 'all' ? 'true' : 'false' }}"
                >All</button>
                <button
                    type="button"
                    role="option"
                    data-value="active"
                    @class([
                        'reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2',
                        'bg-gray-100 font-medium text-gray-900' => $st === 'active',
                        'text-gray-900' => $st !== 'active',
                    ])
                    aria-selected="{{ $st === 'active' ? 'true' : 'false' }}"
                >Active only</button>
                <button
                    type="button"
                    role="option"
                    data-value="deleted"
                    @class([
                        'reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2',
                        'bg-gray-100 font-medium text-gray-900' => $st === 'deleted',
                        'text-gray-900' => $st !== 'deleted',
                    ])
                    aria-selected="{{ $st === 'deleted' ? 'true' : 'false' }}"
                >Deleted only</button>
            </x-reservo-form-select>
        </div>
    @endif

    <div class="mt-3 max-lg:mt-0 grid grid-cols-2 gap-x-3 gap-y-3 sm:gap-x-4 sm:gap-y-3.5">
        <div class="min-w-0 col-span-1">
            <label for="min_capacity" class="block text-xs font-medium text-gray-700">Min. people</label>
            <input
                id="min_capacity"
                name="min_capacity"
                type="number"
                min="1"
                value="{{ $f['min_capacity'] ?? '' }}"
                class="app-field"
            >
        </div>
        <div class="min-w-0 col-span-1">
            <label for="max_capacity" class="block text-xs font-medium text-gray-700">Max. people</label>
            <input
                id="max_capacity"
                name="max_capacity"
                type="number"
                min="1"
                value="{{ $f['max_capacity'] ?? '' }}"
                class="app-field"
            >
        </div>
        <div class="min-w-0 col-span-1">
            <label for="min_size_sqm" class="block text-xs font-medium text-gray-700">Min. size (m²)</label>
            <input
                id="min_size_sqm"
                name="min_size_sqm"
                type="number"
                min="1"
                value="{{ $f['min_size_sqm'] ?? '' }}"
                class="app-field"
            >
        </div>
        <div class="min-w-0 col-span-1">
            <label for="max_size_sqm" class="block text-xs font-medium text-gray-700">Max. size (m²)</label>
            <input
                id="max_size_sqm"
                name="max_size_sqm"
                type="number"
                min="1"
                value="{{ $f['max_size_sqm'] ?? '' }}"
                class="app-field"
            >
        </div>
        <div class="col-span-2 border-t border-gray-200/80 pt-3">
            <p
                class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-500"
            >Price</p>
            <p class="mt-0.5 text-[11px] text-gray-500">Hourly rate range ($/hr). Leave blank for no limit.</p>
            <div
                class="mt-2.5 grid grid-cols-2 gap-x-3 gap-y-3 sm:gap-x-4 sm:gap-y-3.5"
            >
                <div class="min-w-0 col-span-1">
                    <label for="min_hourly_rate" class="block text-xs font-medium text-gray-700">Min. $/hr</label>
                    <input
                        id="min_hourly_rate"
                        name="min_hourly_rate"
                        type="number"
                        min="0"
                        inputmode="decimal"
                        step="0.01"
                        value="{{ $f['min_hourly_rate'] ?? '' }}"
                        class="app-field"
                    >
                </div>
                <div class="min-w-0 col-span-1">
                    <label for="max_hourly_rate" class="block text-xs font-medium text-gray-700">Max. $/hr</label>
                    <input
                        id="max_hourly_rate"
                        name="max_hourly_rate"
                        type="number"
                        min="0"
                        inputmode="decimal"
                        step="0.01"
                        value="{{ $f['max_hourly_rate'] ?? '' }}"
                        class="app-field"
                    >
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 space-y-3">
        @php
            $locVal = (string) ($f['location'] ?? '');
            $amVal = (string) ($f['amenity'] ?? '');
        @endphp
        <div class="min-w-0 w-full">
            <x-reservo-form-select
                name="location"
                hidden-id="location"
                trigger-id="admin_room_location_trigger"
                listbox-id="admin_room_location_listbox"
                label="Location"
                placeholder="Any"
                :value="$locVal"
            >
                <button
                    type="button"
                    role="option"
                    data-value=""
                    class="reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm text-gray-500 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2"
                    aria-selected="{{ $locVal === '' ? 'true' : 'false' }}"
                >Any</button>
                @foreach ($filterOptions['locations'] as $loc)
                    <button
                        type="button"
                        role="option"
                        data-value="{{ $loc }}"
                        title="{{ $loc }}"
                        @class([
                            'reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm break-words hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2',
                            'bg-gray-100 font-medium text-gray-900' => $locVal === $loc,
                            'text-gray-900' => $locVal !== $loc,
                        ])
                        aria-selected="{{ $locVal === $loc ? 'true' : 'false' }}"
                    >{{ \App\Support\FilterDisplay::locationLabel($loc) }}</button>
                @endforeach
            </x-reservo-form-select>
        </div>
        <div class="min-w-0 w-full">
            <x-reservo-form-select
                name="amenity"
                hidden-id="amenity"
                trigger-id="admin_room_amenity_trigger"
                listbox-id="admin_room_amenity_listbox"
                label="Amenity"
                placeholder="Any"
                :value="$amVal"
            >
                <button
                    type="button"
                    role="option"
                    data-value=""
                    class="reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm text-gray-500 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2"
                    aria-selected="{{ $amVal === '' ? 'true' : 'false' }}"
                >Any</button>
                @foreach ($filterOptions['amenities'] as $am)
                    <button
                        type="button"
                        role="option"
                        data-value="{{ $am }}"
                        title="{{ $am }}"
                        @class([
                            'reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm break-words hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2',
                            'bg-gray-100 font-medium text-gray-900' => $amVal === $am,
                            'text-gray-900' => $amVal !== $am,
                        ])
                        aria-selected="{{ $amVal === $am ? 'true' : 'false' }}"
                    >{{ \App\Support\FilterDisplay::amenityLabel($am) }}</button>
                @endforeach
            </x-reservo-form-select>
        </div>
    </div>

    <div class="mt-4">
        <label
            class="flex cursor-pointer items-center gap-2.5 rounded-lg py-0.5 text-sm text-gray-800"
        >
            <input
                type="checkbox"
                name="has_photo"
                value="1"
                class="h-4 w-4 shrink-0 rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                @checked(! empty($f['has_photo']))
            >
            <span class="leading-snug">Only rooms with a photo</span>
        </label>
    </div>

    <div
        class="mt-4 flex flex-col gap-3 border-t border-gray-200/90 pt-4 max-lg:sticky max-lg:bottom-0 max-lg:z-20 max-lg:rounded-b-none max-lg:border-t max-lg:border-slate-200 max-lg:bg-white max-lg:px-0.5 max-lg:py-2.5 max-lg:pb-[max(0.5rem,env(safe-area-inset-bottom))] max-lg:pt-3 max-lg:shadow-[0_-8px_24px_-8px_rgba(15,23,42,0.06)]"
    >
        <div class="min-w-0 w-full">
            @php
                $sortVal = (string) ($f['sort'] ?? 'name');
                $sortLabels = [
                    'name' => 'Name (A–Z)',
                    'capacity_asc' => 'People · low to high',
                    'capacity_desc' => 'People · high to low',
                    'size_asc' => 'Size (m²) · small first',
                    'size_desc' => 'Size (m²) · large first',
                    'hourly_asc' => 'Price · low to high',
                    'hourly_desc' => 'Price · high to low',
                ];
            @endphp
            <x-reservo-form-select
                name="sort"
                hidden-id="sort"
                trigger-id="sort_trigger"
                listbox-id="sort_listbox"
                label="Sort by"
                placeholder="Sort by"
                :value="$sortVal"
            >
                @foreach ($sortLabels as $val => $lbl)
                    <button
                        type="button"
                        role="option"
                        data-value="{{ $val }}"
                        @class([
                            'reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2',
                            'bg-gray-100 font-medium text-gray-900' => $sortVal === $val,
                            'text-gray-900' => $sortVal !== $val,
                        ])
                        aria-selected="{{ $sortVal === $val ? 'true' : 'false' }}"
                    >{{ $lbl }}</button>
                @endforeach
            </x-reservo-form-select>
        </div>
        <div class="flex w-full flex-col gap-2 sm:flex-row sm:items-stretch">
            <button
                type="submit"
                class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-teal-600 px-4 text-sm font-medium text-white shadow-sm transition hover:bg-teal-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/35 sm:min-h-10"
            >
                <x-lucide name="check" class="h-4 w-4 shrink-0 opacity-90" aria-hidden="true" />
                Apply
            </button>
            <a
                href="{{ $resetUrl }}"
                class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 text-sm font-medium text-gray-700 shadow-sm transition hover:border-gray-300 hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/15 sm:min-h-10"
            >
                <x-lucide name="rotate-ccw" class="h-4 w-4 shrink-0 text-gray-500" aria-hidden="true" />
                Reset
            </a>
        </div>
    </div>
</div>

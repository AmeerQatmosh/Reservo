{{-- Expects: $filters, $filterOptions, $action (for form context — buttons use parent form) --}}
@php
    $f = $filters ?? [];
@endphp

<div class="rounded-2xl border border-white/70 bg-white/90 p-4 shadow-sm ring-1 ring-gray-900/[0.04] sm:p-5 max-lg:rounded-none max-lg:border-0 max-lg:bg-white max-lg:shadow-none max-lg:ring-0 max-lg:p-3 sm:max-lg:p-4">
    <p class="hidden text-xs font-semibold uppercase tracking-[0.12em] text-gray-500 lg:block">Refine</p>

    <div class="mt-3 max-lg:mt-0 grid grid-cols-2 gap-x-3 gap-y-3 sm:gap-x-4 sm:gap-y-3.5">
        <div class="min-w-0 col-span-1">
            <label for="min_capacity" class="block text-xs font-medium text-gray-700">Min. people</label>
            <input
                id="min_capacity"
                name="min_capacity"
                type="number"
                min="1"
                inputmode="numeric"
                value="{{ $f['min_capacity'] ?? '' }}"
                placeholder="1"
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
                inputmode="numeric"
                value="{{ $f['max_capacity'] ?? '' }}"
                placeholder="20"
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
                inputmode="numeric"
                value="{{ $f['min_size_sqm'] ?? '' }}"
                placeholder="20"
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
                inputmode="numeric"
                value="{{ $f['max_size_sqm'] ?? '' }}"
                placeholder="100"
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
                        placeholder="0"
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
                        placeholder="80"
                        class="app-field"
                    >
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 space-y-3">
        <x-room-filter-combobox
            class="min-w-0"
            name="location"
            id="location"
            label="Location"
            optionKind="location"
            placeholder="Area or address…"
            :value="$f['location'] ?? ''"
            :options="$filterOptions['locations']"
        />
        <x-room-filter-combobox
            class="min-w-0"
            name="amenity"
            id="amenity"
            label="Amenity"
            optionKind="amenity"
            placeholder="e.g. Wi‑Fi, TV…"
            :value="$f['amenity'] ?? ''"
            :options="$filterOptions['amenities']"
        />
    </div>

    <div class="mt-4">
        <label class="flex cursor-pointer items-center gap-2.5 rounded-lg py-0.5 text-sm text-gray-800">
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
        class="mt-4 flex flex-col gap-3 border-t border-gray-200/90 pt-4 max-lg:sticky max-lg:bottom-0 max-lg:z-20 max-lg:-mx-0 max-lg:rounded-b-none max-lg:border-t max-lg:border-slate-200 max-lg:bg-white max-lg:px-0.5 max-lg:py-2.5 max-lg:pb-[max(0.5rem,env(safe-area-inset-bottom))] max-lg:pt-3 max-lg:shadow-[0_-8px_24px_-8px_rgba(15,23,42,0.06)]"
    >
        <div class="min-w-0 w-full">
            <label for="sort" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">
                <x-lucide name="arrow-up-down" class="h-3.5 w-3.5 shrink-0 text-gray-400" aria-hidden="true" />
                Sort by
            </label>
            <select id="sort" name="sort" class="app-field mt-1.5">
                <option value="name" @selected(($f['sort'] ?? 'name') === 'name')>Name (A–Z)</option>
                <option value="capacity_asc" @selected(($f['sort'] ?? '') === 'capacity_asc')>People · low → high</option>
                <option value="capacity_desc" @selected(($f['sort'] ?? '') === 'capacity_desc')>People · high → low</option>
                <option value="size_asc" @selected(($f['sort'] ?? '') === 'size_asc')>Size (m²) · small first</option>
                <option value="size_desc" @selected(($f['sort'] ?? '') === 'size_desc')>Size (m²) · large first</option>
                <option value="hourly_asc" @selected(($f['sort'] ?? '') === 'hourly_asc')>Price · low → high</option>
                <option value="hourly_desc" @selected(($f['sort'] ?? '') === 'hourly_desc')>Price · high → low</option>
            </select>
        </div>
        <div class="flex w-full flex-col gap-2 sm:flex-row sm:items-stretch">
            <button
                type="submit"
                class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/35 sm:min-h-10"
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

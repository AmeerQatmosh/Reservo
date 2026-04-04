{{--
    Mobile-first room filters: search → refine (2×2 + row + checkbox) → sort + apply/reset.
    Expects: $action, $resetUrl, $filters, $filterOptions
--}}
@php
    $f = $filters ?? [];
@endphp
<div class="rounded-2xl border border-white/70 bg-white/90 p-4 shadow-sm ring-1 ring-gray-900/[0.04] sm:p-5">
    <form method="GET" action="{{ $action }}" class="flex flex-col gap-5">
        {{-- Search --}}
        <div class="w-full min-w-0">
            <label for="search" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">
                <x-lucide name="search" class="h-3.5 w-3.5 shrink-0 text-gray-400" aria-hidden="true" />
                Search
            </label>
            <input
                id="search"
                name="search"
                type="search"
                enterkeyhint="search"
                autocomplete="off"
                value="{{ $f['search'] ?? '' }}"
                placeholder="Name, location, description, amenities…"
                class="app-field mt-1.5"
            >
        </div>

        {{-- Refine --}}
        <div class="overflow-visible rounded-xl border border-gray-100 bg-gradient-to-b from-gray-50/90 to-white px-3 py-3.5 sm:px-4 sm:py-4">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">Refine</p>

            {{-- Mobile: 2×2 metrics + full-width location/amenity. lg: one row of four metrics, then one row of two halves. --}}
            <div class="mt-3 grid grid-cols-2 gap-x-3 gap-y-3 sm:gap-x-4 sm:gap-y-3.5 lg:grid-cols-12 lg:gap-x-4 lg:gap-y-3">
                <div class="min-w-0 lg:col-span-3">
                    <label for="min_capacity" class="block text-xs font-medium text-gray-700">Min. capacity</label>
                    <input
                        id="min_capacity"
                        name="min_capacity"
                        type="number"
                        min="1"
                        inputmode="numeric"
                        value="{{ $f['min_capacity'] ?? '' }}"
                        placeholder="8"
                        class="app-field"
                    >
                </div>
                <div class="min-w-0 lg:col-span-3">
                    <label for="max_capacity" class="block text-xs font-medium text-gray-700">Max. capacity</label>
                    <input
                        id="max_capacity"
                        name="max_capacity"
                        type="number"
                        min="1"
                        inputmode="numeric"
                        value="{{ $f['max_capacity'] ?? '' }}"
                        placeholder="24"
                        class="app-field"
                    >
                </div>
                <div class="min-w-0 lg:col-span-3">
                    <label for="min_size_sqm" class="block text-xs font-medium text-gray-700">Min. size (m²)</label>
                    <input
                        id="min_size_sqm"
                        name="min_size_sqm"
                        type="number"
                        min="1"
                        inputmode="numeric"
                        value="{{ $f['min_size_sqm'] ?? '' }}"
                        placeholder="25"
                        class="app-field"
                    >
                </div>
                <div class="min-w-0 lg:col-span-3">
                    <label for="max_size_sqm" class="block text-xs font-medium text-gray-700">Max. size (m²)</label>
                    <input
                        id="max_size_sqm"
                        name="max_size_sqm"
                        type="number"
                        min="1"
                        inputmode="numeric"
                        value="{{ $f['max_size_sqm'] ?? '' }}"
                        placeholder="80"
                        class="app-field"
                    >
                </div>

                <x-room-filter-combobox
                    class="min-w-0 col-span-2 lg:col-span-6"
                    name="location"
                    id="location"
                    label="Location"
                    :value="$f['location'] ?? ''"
                    :options="$filterOptions['locations']"
                />
                <x-room-filter-combobox
                    class="min-w-0 col-span-2 lg:col-span-6"
                    name="amenity"
                    id="amenity"
                    label="Amenity"
                    :value="$f['amenity'] ?? ''"
                    :options="$filterOptions['amenities']"
                />
            </div>

            <div class="mt-4 flex justify-start">
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
        </div>

        {{-- Sort + actions (bottom; mobile-friendly) --}}
        <div class="flex flex-col gap-3 border-t border-gray-200/90 pt-4 sm:flex-row sm:items-end sm:justify-between sm:gap-4">
            <div class="min-w-0 sm:max-w-[14rem] sm:flex-1">
                <label for="sort" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">
                    <x-lucide name="arrow-up-down" class="h-3.5 w-3.5 shrink-0 text-gray-400" aria-hidden="true" />
                    Sort by
                </label>
                <select id="sort" name="sort" class="app-field mt-1.5">
                    <option value="name" @selected(($f['sort'] ?? 'name') === 'name')>Name (A–Z)</option>
                    <option value="capacity_asc" @selected(($f['sort'] ?? '') === 'capacity_asc')>Capacity · low → high</option>
                    <option value="capacity_desc" @selected(($f['sort'] ?? '') === 'capacity_desc')>Capacity · high → low</option>
                    <option value="size_asc" @selected(($f['sort'] ?? '') === 'size_asc')>Size (m²) · small first</option>
                    <option value="size_desc" @selected(($f['sort'] ?? '') === 'size_desc')>Size (m²) · large first</option>
                </select>
            </div>
            <div class="flex w-full gap-2 sm:w-auto sm:shrink-0">
                <button
                    type="submit"
                    class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/35 sm:min-h-10 sm:flex-none sm:px-5"
                >
                    <x-lucide name="check" class="h-4 w-4 shrink-0 opacity-90" aria-hidden="true" />
                    Apply
                </button>
                <a
                    href="{{ $resetUrl }}"
                    class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 text-sm font-medium text-gray-700 shadow-sm transition hover:border-gray-300 hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/15 sm:min-h-10 sm:flex-none sm:px-5"
                >
                    <x-lucide name="rotate-ccw" class="h-4 w-4 shrink-0 text-gray-500" aria-hidden="true" />
                    Reset
                </a>
            </div>
        </div>
    </form>
</div>

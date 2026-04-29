@php
    $f = $filters ?? [];
@endphp

<div class="min-w-0 w-full">
    <label for="search" class="sr-only">Search rooms by name, location, description, or amenities</label>
    <div class="relative">
        <span
            class="pointer-events-none absolute left-3.5 top-1/2 z-[1] -translate-y-1/2 text-slate-400"
            aria-hidden="true"
        >
            <x-lucide name="search" class="h-4 w-4" />
        </span>
        <input
            id="search"
            name="search"
            type="search"
            enterkeyhint="search"
            autocomplete="off"
            value="{{ $f['search'] ?? '' }}"
            placeholder="Name, location, description, amenities…"
            class="app-field !mt-0 !box-border !h-12 !min-h-12 w-full !rounded-2xl !border-white/80 !bg-white py-2.5 pl-10 pr-4 shadow-sm ring-1 ring-gray-900/[0.04] transition hover:!border-slate-200/70 focus:!border-slate-300 focus:!bg-white sm:!min-h-12"
        >
    </div>
</div>

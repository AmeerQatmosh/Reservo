@php
    $active = $active ?? 'grid';
@endphp

<div class="flex flex-wrap items-center gap-2" role="group" aria-label="Layout">
    <a
        href="{{ $gridUrl }}"
        @if ($active === 'grid') aria-current="page" @endif
        class="inline-flex h-12 min-h-12 shrink-0 items-center justify-center gap-2 rounded-2xl border px-3.5 text-sm font-medium shadow-sm ring-1 ring-gray-900/[0.04] transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/20 {{ $active === 'grid' ? 'border-gray-900 bg-gray-900 text-white hover:bg-gray-800' : 'border-slate-200/90 bg-white text-slate-800 hover:border-slate-300 hover:bg-slate-50/80' }}"
    >
        <x-lucide name="layout-grid" class="h-4 w-4 shrink-0 opacity-90" aria-hidden="true" />
        <span>Grid</span>
    </a>
    <a
        href="{{ $listUrl }}"
        @if ($active === 'list') aria-current="page" @endif
        class="inline-flex h-12 min-h-12 shrink-0 items-center justify-center gap-2 rounded-2xl border px-3.5 text-sm font-medium shadow-sm ring-1 ring-gray-900/[0.04] transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/20 {{ $active === 'list' ? 'border-gray-900 bg-gray-900 text-white hover:bg-gray-800' : 'border-slate-200/90 bg-white text-slate-800 hover:border-slate-300 hover:bg-slate-50/80' }}"
    >
        <x-lucide name="list" class="h-4 w-4 shrink-0 opacity-90" aria-hidden="true" />
        <span>List</span>
    </a>
</div>

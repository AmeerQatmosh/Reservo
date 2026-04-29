@props([
    /** @var 'list'|'calendar' */
    'viewMode' => 'list',
    'historyUrl',
    'calendarUrl',
])

<div
    {{ $attributes->merge([
        'class' => 'inline-flex rounded-2xl border border-gray-200 bg-gray-50/90 p-1 shadow-inner',
        'role' => 'tablist',
        'aria-label' => __('Reservation views'),
    ]) }}
>
    <a
        href="{{ $historyUrl }}"
        role="tab"
        aria-selected="{{ $viewMode === 'list' ? 'true' : 'false' }}"
        class="{{ $viewMode === 'list' ? 'bg-white shadow-sm ring-1 ring-gray-900/10' : 'text-gray-600 hover:text-gray-900' }} inline-flex shrink-0 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition"
    >
        <x-lucide name="list" class="h-4 w-4 shrink-0 opacity-80" aria-hidden="true" />
        {{ __('History') }}
    </a>
    <a
        href="{{ $calendarUrl }}"
        role="tab"
        aria-selected="{{ $viewMode === 'calendar' ? 'true' : 'false' }}"
        class="{{ $viewMode === 'calendar' ? 'bg-white shadow-sm ring-1 ring-gray-900/10' : 'text-gray-600 hover:text-gray-900' }} inline-flex shrink-0 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition"
    >
        <x-lucide name="calendar" class="h-4 w-4 shrink-0 opacity-80" aria-hidden="true" />
        {{ __('Calendar') }}
    </a>
</div>

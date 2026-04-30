@props([
    'name',
    'hiddenId',
    'triggerId',
    'listboxId',
    'label',
    'placeholder' => 'Select',
    'value' => '',
    /** Optional: initial `data-hourly-rate` on hidden (room picker). */
    'hourlyRate' => null,
    /** When false, no visible label (use an external <label for="triggerId">). */
    'showLabel' => true,
])
@php
    $labelFor = $triggerId;
@endphp
<div
    {{ $attributes->class(['relative min-w-0']) }}
    data-reservo-form-select
    data-placeholder="{{ $placeholder }}"
>
    @if ($showLabel)
        <label for="{{ $labelFor }}" class="block text-sm font-medium text-gray-900">{{ $label }}</label>
    @endif
    <input
        type="hidden"
        name="{{ $name }}"
        id="{{ $hiddenId }}"
        value="{{ $value }}"
        data-reservo-form-select-input
        @if ($hourlyRate !== null)
            data-hourly-rate="{{ $hourlyRate }}"
        @endif
    >
    <button
        type="button"
        id="{{ $triggerId }}"
        @class([
            'app-field flex min-h-11 w-full min-w-0 items-center justify-between gap-2 rounded-xl py-2.5 pl-3.5 pr-3.5 text-left text-base text-gray-900 sm:min-h-0 sm:py-2 sm:text-sm',
            'mt-1.5' => $showLabel,
        ])
        data-reservo-form-select-trigger
        aria-haspopup="listbox"
        aria-expanded="false"
        aria-controls="{{ $listboxId }}"
    >
        <span data-reservo-form-select-label class="min-w-0 truncate"></span>
        <x-lucide
            name="chevron-down"
            class="reservo-combobox-chevron h-4 w-4 shrink-0 text-gray-500 transition-transform duration-200"
            aria-hidden="true"
        />
    </button>
    <div
        id="{{ $listboxId }}"
        role="listbox"
        class="reservo-combobox-panel absolute left-0 right-0 z-[60] mt-1 max-h-[min(55vh,18rem)] overflow-y-auto overscroll-contain rounded-xl border border-gray-200 bg-white p-1 shadow-lg ring-1 ring-black/[0.06]"
        data-reservo-form-select-panel
        hidden
    >
        {{ $slot }}
    </div>
</div>

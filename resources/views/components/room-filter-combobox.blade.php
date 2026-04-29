@props([
    'name',
    'id',
    'label',
    'value' => '',
    'options',
    'placeholder' => 'Type or pick a suggestion…',
    /** "location" | "amenity" | null — shows shortened labels, full value in data-value + title */
    'optionKind' => null,
])
<div {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <label for="{{ $id }}" class="block text-xs font-medium text-gray-700">{{ $label }}</label>
    <div class="relative mt-1.5" data-reservo-combobox>
        <div class="relative flex">
            <input
                id="{{ $id }}"
                name="{{ $name }}"
                type="text"
                value="{{ $value }}"
                placeholder="{{ $placeholder }}"
                autocomplete="off"
                class="app-field mt-0 min-h-11 w-full min-w-0 rounded-xl py-2.5 pr-11 text-base sm:min-h-0 sm:py-2 sm:text-sm"
                data-reservo-combobox-input
            >
            <button
                type="button"
                class="absolute inset-y-px right-px z-[1] flex w-11 items-center justify-center rounded-r-[0.7rem] text-gray-500 transition hover:bg-gray-100 hover:text-gray-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-0 focus-visible:outline-gray-900/20"
                data-reservo-combobox-toggle
                tabindex="-1"
                aria-expanded="false"
                aria-controls="{{ $id }}-listbox"
                aria-label="Open {{ $label }} suggestions"
            >
                <x-lucide
                    name="chevron-down"
                    class="reservo-combobox-chevron h-4 w-4 shrink-0 transition-transform duration-200"
                    aria-hidden="true"
                />
            </button>
        </div>
        <div
            id="{{ $id }}-listbox"
            role="listbox"
            class="reservo-combobox-panel absolute left-0 right-0 z-[60] mt-1 max-h-[min(55vh,18rem)] overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg ring-1 ring-black/[0.06]"
            data-reservo-combobox-panel
            hidden
        >
            <div class="border-b border-gray-100 bg-gray-50/95 px-2 py-2">
                <input
                    type="search"
                    class="app-field mt-0 min-h-10 w-full py-2 text-base sm:min-h-0 sm:text-sm"
                    placeholder="Search suggestions…"
                    data-reservo-combobox-filter
                    aria-label="Filter {{ $label }} suggestions"
                    tabindex="-1"
                >
            </div>
            <div
                class="max-h-[min(48vh,14rem)] overflow-y-auto overscroll-contain p-1"
                data-reservo-combobox-options
            >
                <button
                    type="button"
                    role="option"
                    data-value=""
                    class="reservo-combobox__opt w-full rounded-lg px-3 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2"
                >
                    Any · no filter
                </button>
                @foreach ($options as $opt)
                    <button
                        type="button"
                        role="option"
                        data-value="{{ $opt }}"
                        title="{{ $opt }}"
                        class="reservo-combobox__opt w-full rounded-lg px-3 py-2.5 text-left text-sm break-words text-gray-900 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2"
                    >
                        @if ($optionKind === 'location')
                            {{ \App\Support\FilterDisplay::locationLabel($opt) }}
                        @elseif ($optionKind === 'amenity')
                            {{ \App\Support\FilterDisplay::amenityLabel($opt) }}
                        @else
                            {{ $opt }}
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

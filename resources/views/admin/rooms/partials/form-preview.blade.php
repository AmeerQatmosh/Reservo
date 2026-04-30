{{-- Live preview for admin room form; updated via resources/js/blade-ui.ts --}}
<div
    id="admin-room-preview"
    class="rounded-3xl border border-white/70 bg-white/90 p-5 shadow-sm ring-1 ring-gray-900/[0.03]"
    aria-label="{{ __('Room card preview') }}"
    data-default-name="{{ e(__('Room name')) }}"
    data-up-to-template="{{ e(__('Up to :n people', ['n' => '{n}'])) }}"
    data-more-label="{{ e(__('more')) }}"
>
    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-gray-500">
        {{ __('Live preview') }}
    </p>
    <p class="mt-1 text-xs text-gray-500">
        {{ __('Approximates how this room appears in the browse grid.') }}
    </p>

    <article
        class="mt-4 overflow-hidden rounded-2xl border border-white/80 bg-white/95 shadow-sm ring-1 ring-gray-900/[0.04]"
    >
        <div class="relative aspect-[16/10] overflow-hidden bg-gray-100">
            <img
                id="admin-room-preview-img"
                src=""
                alt=""
                class="hidden h-full w-full object-cover"
            >
            <div
                id="admin-room-preview-photo-placeholder"
                class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-100 to-teal-50 text-xs font-medium text-gray-500"
            >
                {{ __('No photo') }}
            </div>
        </div>
        <div class="p-4">
            <h2
                id="admin-room-preview-name"
                class="text-lg font-semibold tracking-tight text-gray-900"
            >{{ __('Room name') }}</h2>
            <p id="admin-room-preview-location" class="mt-0.5 hidden text-xs text-gray-500"></p>
            <div id="admin-room-preview-meta" class="mt-2 flex flex-wrap gap-2"></div>
            <div id="admin-room-preview-amenities" class="mt-2 hidden flex flex-wrap gap-1.5"></div>
            <p id="admin-room-preview-description" class="mt-2 hidden line-clamp-3 text-sm leading-relaxed text-gray-700"></p>
        </div>
    </article>
</div>

{{-- Admin-only: primary navigation when nav_layout is vertical (lg+). Expects nav link class variables from layouts.app --}}
<aside
    id="reservo-admin-sidebar"
    class="reservo-admin-sidebar hidden min-h-0 shrink-0 flex-col overflow-visible border-r border-gray-200/90 bg-white shadow-[1px_0_0_rgba(15,23,42,0.06)] transition-[width] duration-200 ease-out lg:sticky lg:top-0 lg:z-[35] lg:flex lg:h-dvh lg:max-h-dvh lg:self-start"
    aria-label="{{ __('Main navigation') }}"
>
    <div
        class="admin-sidebar-header flex shrink-0 items-center gap-2 border-b border-gray-200/80 px-3 py-3 pt-[max(0.75rem,env(safe-area-inset-top))]"
    >
        <a
            href="{{ route('dashboard') }}"
            class="admin-sidebar-brand flex min-w-0 flex-1 items-center gap-2 rounded-lg py-0.5 transition hover:opacity-90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900"
            aria-label="{{ config('app.name') }} — {{ __('Dashboard') }}"
            title="{{ config('app.name') }} — {{ __('Dashboard') }}"
            data-sidebar-tooltip="{{ config('app.name') }} — {{ __('Dashboard') }}"
        >
            <span class="flex h-5 shrink-0 items-center" aria-hidden="true">
                <img
                    src="{{ asset('images/reservo-logo-colored.svg') }}"
                    alt=""
                    width="277"
                    height="332"
                    class="h-full w-auto max-h-full object-contain object-left"
                    draggable="false"
                />
            </span>
            <span class="admin-sidebar-label font-brand min-w-0 translate-y-px truncate text-lg font-bold leading-none tracking-[-0.03em] text-[#101828]">
                {{ config('app.name') }}
            </span>
        </a>
    </div>

    <nav class="flex min-h-0 flex-1 flex-col gap-1 overflow-y-auto overscroll-contain px-3 py-4">
        @include('layouts.partials.nav-app-links', [
            'navLinkClass' => $navLinkClass,
            'navActiveClass' => $navActiveClass,
            'navInactiveClass' => $navInactiveClass,
            'variant' => 'sidebar',
        ])
    </nav>

    <div class="mt-auto shrink-0 border-t border-gray-200/80 bg-slate-50/60 px-3 py-3 pb-[max(0.75rem,env(safe-area-inset-bottom))]">
        <div class="admin-sidebar-footer-inner flex items-center justify-end gap-2 border-t-0 pt-0">
            <button
                type="button"
                id="reservo-admin-sidebar-toggle"
                class="flex h-8 w-8 shrink-0 cursor-pointer items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 shadow-sm transition outline-none hover:border-gray-300 hover:bg-gray-50 hover:text-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900/25 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                aria-controls="reservo-admin-sidebar"
                aria-expanded="true"
                aria-label="{{ __('Collapse navigation') }}"
                data-sidebar-label-expand="{{ __('Expand navigation') }}"
                data-sidebar-label-collapse="{{ __('Collapse navigation') }}"
                data-sidebar-tooltip="{{ __('Collapse navigation') }}"
            >
                <x-lucide name="menu" class="h-4 w-4" aria-hidden="true" />
            </button>
            @include('layouts.partials.nav-layout-toggle', ['variant' => 'sidebar'])
            @include('layouts.partials.account-menu', ['variant' => 'desktop', 'sidebarRail' => true])
        </div>
    </div>
</aside>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh w-full bg-[#f8fafc]">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="turbo-prefetch" content="false">

        @hasSection('title')
            <title>{{ config('app.name') }} - @yield('title')</title>
        @else
            <title>{{ config('app.name') }}</title>
        @endif

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="icon" href="{{ asset('favicon-light.svg') }}" type="image/svg+xml" media="(prefers-color-scheme: light)">
        <link rel="icon" href="{{ asset('favicon-dark.svg') }}" type="image/svg+xml" media="(prefers-color-scheme: dark)">
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        {{-- Before paint on navigated pages: same key as blade-ui startNavProgress() --}}
        <script>
            try {
                if (sessionStorage.getItem('reservo_nav') === '1') {
                    document.documentElement.classList.add('reservo-skeleton-enter');
                }
            } catch (e) {}
        </script>

        @auth
            @if (auth()->user()->usesVerticalNav())
        <script>
            try {
                if (localStorage.getItem('reservo_admin_sidebar_rail') === '1') {
                    document.documentElement.classList.add('admin-sidebar-rail-collapsed');
                    document.addEventListener('DOMContentLoaded', function () {
                        var t = document.getElementById('reservo-admin-sidebar-toggle');
                        if (t) {
                            t.setAttribute('aria-expanded', 'false');
                            var ex = t.getAttribute('data-sidebar-label-expand');
                            if (ex) {
                                t.setAttribute('aria-label', ex);
                            }
                        }
                    });
                }
            } catch (e) {}
        </script>
            @endif
        @endauth

        @vite(['resources/css/app.css', 'resources/js/blade-ui.ts'])
    </head>
    <body class="flex min-h-dvh w-full max-w-none flex-col bg-[radial-gradient(circle_at_top,rgba(15,23,42,0.04),transparent_32%),linear-gradient(to_bottom,#f8fafc,#eef2ff_42%,#f8fafc)] text-gray-900 antialiased [color-scheme:light] @auth @if (auth()->user()->usesVerticalNav()) lg:flex-row lg:items-start @endif @endauth">
        <div id="reservo-progress" data-state="idle" aria-hidden="true"></div>
        @include('layouts.partials.page-skeleton')
        @php
            $navLinkClass = 'nav-link inline-flex items-center gap-2 whitespace-nowrap px-3 py-2 text-sm font-semibold transition';
            $navInactiveClass = 'text-gray-500 hover:text-gray-900';
            $navActiveClass = 'text-gray-900 active text-lg';
            $adminVerticalNav = auth()->check() && auth()->user()->usesVerticalNav();
            $headerNavRowClass = 'flex w-full items-center justify-between gap-x-2 gap-y-2 sm:gap-x-3 lg:items-center lg:gap-x-6';
            if ($adminVerticalNav) {
                $headerNavRowClass .= ' lg:justify-end';
            } else {
                $headerNavRowClass .= ' lg:grid lg:grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)]';
            }
        @endphp
        @if ($adminVerticalNav)
            @include('layouts.partials.nav-admin-sidebar', [
                'navLinkClass' => $navLinkClass,
                'navActiveClass' => $navActiveClass,
                'navInactiveClass' => $navInactiveClass,
            ])
            <div class="flex min-h-dvh min-w-0 flex-1 flex-col">
        @endif
        <header @class([
            'sticky top-0 z-40 overflow-visible shadow-[0_1px_0_rgba(15,23,42,0.06)]',
            'lg:shadow-none' => $adminVerticalNav,
        ])>
        {{-- z-30: stack above demo strip. Vertical nav (lg+): rail holds nav; keep this row for mobile only. --}}
        <nav @class([
            'relative z-30 overflow-visible border-b border-gray-200',
            'lg:hidden' => $adminVerticalNav,
        ])>
            <div
                class="pointer-events-none absolute inset-0 -z-10 bg-white/90 backdrop-blur-xl supports-[backdrop-filter]:bg-white/85"
                aria-hidden="true"
            ></div>
            <div class="relative mx-auto max-w-[min(100%,85rem)] px-2.5 sm:px-3.5 lg:px-6 py-3 sm:py-3.5">
                {{-- Mobile: logo | menu. Desktop: 1fr | auto | 1fr so primary nav stays visually centered. --}}
                <div class="{{ $headerNavRowClass }}">
                    <a
                        href="{{ auth()->check() ? route('dashboard') : route('home') }}"
                        @class([
                            'inline-flex min-w-0 max-w-[min(100%,16rem)] shrink-0 items-center gap-2.5 rounded-lg border-0 bg-transparent py-0.5 shadow-none transition hover:opacity-90 focus-visible:rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900 sm:max-w-none sm:gap-3 lg:justify-self-start',
                            'lg:hidden' => $adminVerticalNav,
                        ])
                        aria-label="{{ config('app.name') }} — home"
                    >
                        {{-- SVG is very tall (332 viewBox); cap height to match wordmark, not vice versa --}}
                        <span class="flex h-5 shrink-0 items-center sm:h-6" aria-hidden="true">
                            <img
                                src="{{ asset('images/reservo-logo-colored.svg') }}"
                                alt=""
                                width="277"
                                height="332"
                                class="h-full w-auto max-h-full object-contain object-left"
                                draggable="false"
                            />
                        </span>
                        <span class="font-brand min-w-0 translate-y-px truncate text-xl font-bold leading-none tracking-[-0.03em] text-[#101828] sm:text-2xl sm:tracking-[-0.02em]">
                            {{ config('app.name') }}
                        </span>
                    </a>

                    <div
                        @class([
                            'hidden min-w-0 max-w-full justify-self-center lg:flex',
                            'lg:hidden' => $adminVerticalNav,
                        ])
                    >
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            @include('layouts.partials.nav-app-links', [
                                'navLinkClass' => $navLinkClass,
                                'navActiveClass' => $navActiveClass,
                                'navInactiveClass' => $navInactiveClass,
                                'variant' => 'desktop',
                            ])
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center justify-end gap-2 overflow-visible lg:justify-self-end">
                        @if (! $adminVerticalNav)
                            <div class="hidden items-center gap-2 lg:flex">
                                @include('layouts.partials.nav-auth-links', [
                                    'navLinkClass' => $navLinkClass,
                                    'navActiveClass' => $navActiveClass,
                                    'navInactiveClass' => $navInactiveClass,
                                    'variant' => 'desktop',
                                ])
                            </div>
                        @endif

                        <details class="reservo-details reservo-mobile-menu group relative shrink-0 overflow-visible lg:hidden">
                        <summary
                            class="relative flex h-11 w-11 min-h-[2.75rem] min-w-[2.75rem] cursor-pointer list-none select-none items-center justify-center overflow-hidden rounded-2xl border border-slate-200/90 bg-white/90 text-slate-800 shadow-sm ring-1 ring-slate-900/[0.04] backdrop-blur-sm transition [touch-action:manipulation] outline-none supports-[backdrop-filter]:bg-white/80 hover:border-slate-300/90 hover:bg-white focus-visible:ring-2 focus-visible:ring-slate-400/35 focus-visible:ring-offset-2 focus-visible:ring-offset-[#f8fafc] active:scale-[0.97] [&::-webkit-details-marker]:hidden"
                            aria-label="Open menu"
                            aria-controls="reservo-mobile-menu-panel"
                        >
                            <x-lucide
                                name="menu"
                                class="h-5 w-5 text-slate-700"
                            />
                        </summary>
                        <div
                            class="reservo-mobile-menu__backdrop"
                            aria-hidden="true"
                        ></div>
                        <div
                            id="reservo-mobile-menu-panel"
                            class="reservo-dropdown-panel reservo-mobile-menu__panel"
                            role="dialog"
                            aria-modal="true"
                            aria-label="Site menu"
                        >
                            <div
                                class="reservo-mobile-menu__sheet flex h-full min-h-0 w-full min-w-0 max-w-full flex-col overflow-hidden rounded-l-2xl border-l border-slate-200/60 bg-white shadow-[-10px_0_48px_-16px_rgba(15,23,42,0.2)]"
                            >
                                <div
                                    class="reservo-mobile-menu__header flex shrink-0 items-center justify-between gap-2 border-b border-slate-200/80 bg-slate-50/80 px-3 pb-2.5 pl-3.5 pr-2.5 pt-[max(0.6rem,env(safe-area-inset-top))]"
                                >
                                    <span class="font-brand min-w-0 flex-1 truncate text-base font-bold tracking-[-0.02em] text-slate-900">
                                        {{ config('app.name') }}
                                    </span>
                                    <div class="flex shrink-0 items-center gap-1.5">
                                        @auth
                                            @if (auth()->user()->isAdmin())
                                                @include('layouts.partials.nav-layout-toggle', ['variant' => 'mobile-drawer'])
                                            @endif
                                        @endauth
                                        <button
                                            type="button"
                                            class="reservo-mobile-menu__close -mr-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-slate-600 transition [touch-action:manipulation] duration-200 ease-out hover:bg-slate-200/60 hover:text-slate-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-400/40 active:scale-[0.97]"
                                            aria-label="Close menu"
                                        >
                                            <x-lucide name="x" class="h-5 w-5" />
                                        </button>
                                    </div>
                                </div>
                                <div class="reservo-mobile-menu__body flex min-h-0 flex-1 flex-col">
                                    @auth
                                        <div
                                            class="reservo-mobile-menu__top shrink-0 border-b border-slate-200/90 bg-slate-50/40 px-3 pb-4 pt-3"
                                        >
                                            @include('layouts.partials.account-menu', ['variant' => 'mobile'])
                                        </div>
                                    @endauth
                                    <div
                                        class="reservo-mobile-menu__scroll w-full min-h-0 max-h-[min(70dvh,calc(100dvh-7.5rem))] flex-1 overflow-y-auto overscroll-contain px-3 @auth pt-4 pb-3 @else py-3 @endauth"
                                    >
                                        <div>
                                            <p
                                                class="mb-2.5 text-[0.7rem] font-semibold uppercase tracking-[0.12em] text-slate-400"
                                            >
                                                @auth
                                                    Main
                                                @else
                                                    Menu
                                                @endauth
                                            </p>
                                            <nav class="flex flex-col gap-1.5" aria-label="Primary">
                                                @include('layouts.partials.nav-app-links', [
                                                    'navLinkClass' => $navLinkClass,
                                                    'navActiveClass' => $navActiveClass,
                                                    'navInactiveClass' => $navInactiveClass,
                                                    'variant' => 'mobile',
                                                ])
                                            </nav>
                                        </div>
                                    </div>
                                    <div
                                        class="reservo-mobile-menu__footer mt-auto shrink-0 border-t border-slate-200/80 bg-slate-50/60 px-3 pt-3.5 pb-[max(0.75rem,env(safe-area-inset-bottom))]"
                                    >
                                        @include('layouts.partials.nav-auth-links', [
                                            'navLinkClass' => $navLinkClass,
                                            'navActiveClass' => $navActiveClass,
                                            'navInactiveClass' => $navInactiveClass,
                                            'variant' => 'mobile',
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                        </details>
                    </div>
                </div>
            </div>
        </nav>
        @if (config('reservo.demo_enabled') && \App\Support\DemoState::active() && request()->routeIs('demo.*'))
            @include('layouts.partials.demo-sandbox-bar')
        @endif
        </header>

        <main class="mx-auto w-full min-w-0 max-w-[min(100%,85rem)] flex-1 px-2.5 py-6 sm:px-3.5 sm:py-8 lg:px-6">
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-green-200 bg-green-50/90 px-4 py-3 text-sm text-green-800 shadow-sm sm:px-5 sm:py-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50/90 px-4 py-3 text-sm text-amber-900 shadow-sm sm:px-5 sm:py-4">
                    {{ session('warning') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50/90 px-4 py-3 text-sm text-red-800 shadow-sm sm:px-5 sm:py-4">
                    <div class="font-medium">Please fix the following:</div>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        @include('layouts.partials.footer')

        @if ($adminVerticalNav)
        </div>
        @endif

        <div id="confirmModal" class="fixed inset-0 z-50 hidden p-3 sm:p-4">
            <div class="absolute inset-0 bg-black/40" data-confirm-close></div>

            <div class="flex min-h-full items-end justify-center pb-[max(0.75rem,env(safe-area-inset-bottom))] sm:items-center sm:pb-0">
                <div class="relative w-full max-w-md rounded-2xl border border-gray-100 bg-white p-5 shadow-2xl sm:rounded-3xl sm:p-7">
                    <div class="text-base font-semibold text-gray-900 sm:text-lg">Confirm action</div>
                    <p id="confirmModalMessage" class="mt-2 text-sm text-gray-700">
                        Are you sure you want to continue?
                    </p>

                    <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end sm:gap-3">
                        <button
                            type="button"
                            id="confirmModalCancel"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto sm:rounded-md sm:py-2"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            id="confirmModalConfirm"
                            class="w-full rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-700 sm:w-auto sm:rounded-md sm:py-2"
                        >
                            Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const modal = document.getElementById('confirmModal');
                const message = document.getElementById('confirmModalMessage');
                const confirmButton = document.getElementById('confirmModalConfirm');
                const cancelButton = document.getElementById('confirmModalCancel');
                let pendingForm = null;

                if (!modal || !message || !confirmButton || !cancelButton) return;

                const closeModal = () => {
                    modal.classList.add('hidden');
                    pendingForm = null;
                };

                const openModal = (form) => {
                    pendingForm = form;
                    message.textContent = form.dataset.confirmMessage || 'Are you sure you want to continue?';
                    confirmButton.textContent = form.dataset.confirmButtonLabel || 'Confirm';

                    confirmButton.className = 'w-full rounded-xl px-4 py-2.5 text-sm font-medium text-white sm:w-auto sm:rounded-md sm:py-2';

                    switch (form.dataset.confirmVariant) {
                        case 'success':
                            confirmButton.classList.add('bg-green-600', 'hover:bg-green-700');
                            break;
                        case 'warning':
                            confirmButton.classList.add('bg-amber-600', 'hover:bg-amber-700');
                            break;
                        default:
                            confirmButton.classList.add('bg-red-600', 'hover:bg-red-700');
                            break;
                    }

                    modal.classList.remove('hidden');
                };

                document.addEventListener('submit', (event) => {
                    const form = event.target;

                    if (!(form instanceof HTMLFormElement)) return;
                    if (form.dataset.confirmed === 'true') return;
                    if (!form.dataset.confirmMessage) return;

                    event.preventDefault();
                    openModal(form);
                });

                confirmButton.addEventListener('click', () => {
                    if (!pendingForm) return;

                    pendingForm.dataset.confirmed = 'true';
                    pendingForm.requestSubmit();
                    closeModal();
                });

                cancelButton.addEventListener('click', closeModal);

                modal.querySelectorAll('[data-confirm-close]').forEach((element) => {
                    element.addEventListener('click', closeModal);
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                        closeModal();
                    }
                });
            })();
        </script>
    </body>
</html>

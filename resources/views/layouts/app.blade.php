<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh w-full bg-[#f8fafc]">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

        @hasSection('title')
            <title>{{ config('app.name') }} - @yield('title')</title>
        @else
            <title>{{ config('app.name') }}</title>
        @endif

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-dvh w-full max-w-none bg-[radial-gradient(circle_at_top,rgba(15,23,42,0.04),transparent_32%),linear-gradient(to_bottom,#f8fafc,#eef2ff_42%,#f8fafc)] text-gray-900 antialiased [color-scheme:light]">
        @php
            $navLinkClass = 'inline-flex items-center gap-2 whitespace-nowrap rounded-full px-3 py-2 text-sm font-medium transition';
            $navInactiveClass = 'text-gray-700 hover:bg-gray-100 hover:text-gray-900';
            $navActiveClass = 'bg-gray-900 text-white shadow-sm';
        @endphp
        <header class="sticky top-0 z-40 overflow-visible shadow-[0_1px_0_rgba(15,23,42,0.06)]">
        <nav class="overflow-visible border-b border-white/60 bg-white/90 backdrop-blur-xl supports-[backdrop-filter]:bg-white/85">
            <div class="mx-auto max-w-7xl px-3 sm:px-4 lg:px-8 py-3 sm:py-3.5">
                <div class="flex items-center gap-x-2 gap-y-2 sm:gap-x-3">
                    <a
                        href="{{ auth()->check() ? route('dashboard') : route('home') }}"
                        class="flex shrink-0 items-center gap-2 rounded-lg border-0 bg-transparent py-1 shadow-none transition hover:opacity-85 focus-visible:rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900 sm:gap-2.5"
                        aria-label="{{ config('app.name') }} — home"
                    >
                        <img
                            src="{{ asset('images/reservo-logo-colored.svg') }}"
                            alt=""
                            width="189"
                            height="294"
                            class="h-7 w-auto max-h-[1.75rem] shrink-0 object-contain object-center sm:h-8 sm:max-h-[2rem]"
                            draggable="false"
                        />
                        <span class="hidden truncate text-sm font-semibold tracking-tight text-gray-900 sm:inline">
                            {{ config('app.name') }}
                        </span>
                    </a>

                    <div class="hidden min-w-0 flex-1 items-center lg:flex">
                        <div class="flex flex-wrap items-center gap-2">
                            @include('layouts.partials.nav-app-links', [
                                'navLinkClass' => $navLinkClass,
                                'navActiveClass' => $navActiveClass,
                                'navInactiveClass' => $navInactiveClass,
                                'variant' => 'desktop',
                            ])
                        </div>
                    </div>

                    <div class="ml-auto hidden shrink-0 items-center gap-2 overflow-visible lg:flex">
                        @include('layouts.partials.nav-auth-links', [
                            'navLinkClass' => $navLinkClass,
                            'navActiveClass' => $navActiveClass,
                            'navInactiveClass' => $navInactiveClass,
                            'variant' => 'desktop',
                        ])
                    </div>

                    <details class="reservo-details group relative ml-auto shrink-0 overflow-visible lg:hidden">
                        <summary
                            class="relative flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-xl bg-gray-900 text-white shadow-md transition outline-none focus-visible:ring-2 focus-visible:ring-white/40 focus-visible:ring-offset-2 focus-visible:ring-offset-[#f8fafc] hover:bg-gray-800 active:scale-[0.98] [&::-webkit-details-marker]:hidden"
                            aria-label="Menu"
                        >
                            <x-lucide
                                name="menu"
                                class="h-[18px] w-[18px] transition-opacity duration-150 group-open:pointer-events-none group-open:opacity-0"
                            />
                            <x-lucide
                                name="x"
                                class="pointer-events-none absolute h-[18px] w-[18px] opacity-0 transition-opacity duration-150 group-open:pointer-events-auto group-open:opacity-100"
                            />
                        </summary>
                        <div class="reservo-dropdown-panel absolute left-auto right-0 z-50 mt-1.5 max-h-[min(28rem,78dvh)] w-[min(16rem,calc(100vw-1rem))] overflow-y-auto overscroll-contain rounded-lg border border-gray-200 bg-white py-1 shadow-[0_12px_40px_-10px_rgba(15,23,42,0.22)]">
                            <div class="flex flex-col gap-px px-1">
                                @include('layouts.partials.nav-app-links', [
                                    'navLinkClass' => $navLinkClass,
                                    'navActiveClass' => $navActiveClass,
                                    'navInactiveClass' => $navInactiveClass,
                                    'variant' => 'mobile',
                                ])
                            </div>
                            <div class="mx-1.5 my-1 border-t border-gray-100"></div>
                            <div class="flex flex-col gap-px px-1">
                                @include('layouts.partials.nav-auth-links', [
                                    'navLinkClass' => $navLinkClass,
                                    'navActiveClass' => $navActiveClass,
                                    'navInactiveClass' => $navInactiveClass,
                                    'variant' => 'mobile',
                                ])
                            </div>
                        </div>
                    </details>
                </div>
            </div>
        </nav>
        @if (config('reservo.demo_enabled') && \App\Support\DemoState::active() && request()->routeIs('demo.*'))
            @include('layouts.partials.demo-sandbox-bar')
        @endif
        </header>

        <main class="mx-auto w-full min-w-0 max-w-7xl px-3 py-6 sm:px-4 sm:py-8 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-green-200 bg-green-50/90 px-4 py-3 text-sm text-green-800 shadow-sm sm:px-5 sm:py-4">
                    {{ session('success') }}
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

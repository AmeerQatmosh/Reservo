{{-- Shown only when html.reservo-skeleton-enter (set via inline head script after internal navigation).
     Mirrors layouts/app chrome: frosted nav, lg 3-column grid, auth-aware placeholders. --}}
@php
    $demoGuestLink = config('reservo.demo_enabled');
    $isAdmin = auth()->check() && auth()->user()->isAdmin();
@endphp
<div
    id="reservo-page-skeleton"
    class="reservo-page-skeleton-root fixed inset-0 z-[85] flex min-h-0 flex-col overflow-hidden bg-[#f8fafc] [background-image:radial-gradient(circle_at_top,rgba(15,23,42,0.04),transparent_32%),linear-gradient(to_bottom,#f8fafc,#eef2ff_42%,#f8fafc)]"
    aria-hidden="true"
>
    {{-- Header: match layouts/app sticky bar (frost + border-gray-200) --}}
    <header class="sticky top-0 z-40 shrink-0 shadow-[0_1px_0_rgba(15,23,42,0.06)]">
        <nav class="relative z-30 overflow-visible border-b border-gray-200">
            <div
                class="pointer-events-none absolute inset-0 -z-10 bg-white/90 backdrop-blur-xl supports-[backdrop-filter]:bg-white/85"
                aria-hidden="true"
            ></div>
            <div class="relative mx-auto max-w-[min(100%,85rem)] px-2.5 py-3 sm:px-3.5 sm:py-3.5 lg:px-6">
                <div
                    class="flex w-full items-center justify-between gap-x-2 gap-y-2 sm:gap-x-3 lg:grid lg:grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)] lg:items-center lg:gap-x-6"
                >
                    {{-- Logo + wordmark --}}
                    <div class="flex min-w-0 max-w-[min(100%,16rem)] shrink-0 items-center gap-2.5 lg:justify-self-start sm:gap-3">
                        <div class="h-5 w-5 shrink-0 animate-pulse rounded-lg bg-slate-200/90 sm:h-6 sm:w-6"></div>
                        <div class="h-6 w-[7.25rem] animate-pulse rounded-md bg-slate-200/85 sm:h-7 sm:w-36"></div>
                    </div>

                    {{-- Primary nav (desktop): chip widths approximate Dashboard / Rooms / My reservations / Admin --}}
                    <div class="hidden min-w-0 max-w-full justify-center gap-2 lg:flex lg:flex-wrap">
                        @guest
                            <div class="h-9 w-[3.35rem] animate-pulse rounded-lg bg-slate-200/80"></div>
                            <div class="h-9 w-[3.6rem] animate-pulse rounded-lg bg-slate-200/78"></div>
                            @if ($demoGuestLink)
                                <div class="h-9 w-[6.75rem] animate-pulse rounded-xl bg-slate-200/75"></div>
                            @endif
                        @else
                            <div class="h-9 w-[5.85rem] animate-pulse rounded-lg bg-slate-200/82"></div>
                            <div class="h-9 w-[3.6rem] animate-pulse rounded-lg bg-slate-200/78"></div>
                            <div class="h-9 w-[7.85rem] animate-pulse rounded-lg bg-slate-200/76"></div>
                            @if ($isAdmin)
                                <div class="h-9 w-[6.85rem] animate-pulse rounded-lg bg-slate-200/74"></div>
                                <div class="h-9 w-[9.5rem] animate-pulse rounded-lg bg-slate-200/72"></div>
                                <div class="h-9 w-[5.85rem] animate-pulse rounded-lg bg-slate-200/74"></div>
                            @endif
                        @endguest
                    </div>

                    {{-- Auth actions --}}
                    <div class="flex shrink-0 items-center justify-end gap-2 lg:justify-self-end">
                        @guest
                            <div class="hidden h-9 w-[3.85rem] animate-pulse rounded-lg bg-slate-200/80 lg:block"></div>
                            <div
                                class="hidden h-9 w-[5.85rem] animate-pulse rounded-xl bg-sky-200/95 lg:block"
                            ></div>
                            <div
                                class="flex h-11 w-11 animate-pulse rounded-2xl border border-slate-200/90 bg-white/90 shadow-sm ring-1 ring-slate-900/[0.04] lg:hidden"
                            ></div>
                        @else
                            <div
                                class="hidden h-9 w-9 shrink-0 animate-pulse rounded-full border border-slate-200/90 bg-white shadow-sm ring-1 ring-slate-900/[0.06] lg:block"
                            ></div>
                            <div
                                class="flex h-11 w-11 animate-pulse rounded-2xl border border-slate-200/90 bg-white/90 shadow-sm ring-1 ring-slate-900/[0.04] lg:hidden"
                            ></div>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>
    </header>

    {{-- Main: max-w + padding match layouts/app <main> --}}
    <div
        class="mx-auto w-full min-h-0 min-w-0 max-w-[min(100%,85rem)] flex-1 space-y-8 overflow-y-auto overscroll-contain px-2.5 py-6 sm:space-y-10 sm:px-3.5 sm:py-8 lg:px-6"
    >
        {{-- Breadcrumbs (common on rooms, admin, reservations) --}}
        <div class="flex flex-wrap items-center gap-x-2 gap-y-1.5">
            <div class="h-3.5 w-20 animate-pulse rounded-md bg-slate-200/75"></div>
            <span class="text-slate-300" aria-hidden="true">&gt;</span>
            <div class="h-3.5 w-[5.75rem] animate-pulse rounded-md bg-slate-200/70"></div>
            <span class="text-slate-300 max-sm:hidden" aria-hidden="true">&gt;</span>
            <div class="hidden h-3.5 w-36 animate-pulse rounded-md bg-slate-200/65 sm:block"></div>
        </div>

        {{-- Page title block (Overview / Dashboard style) --}}
        <div class="space-y-3">
            <div class="h-3 w-24 animate-pulse rounded-lg bg-slate-200/70 sm:w-28"></div>
            <div class="h-8 w-[min(100%,22rem)] max-w-xl animate-pulse rounded-xl bg-slate-200/82 sm:h-9"></div>
            <div class="h-4 w-full max-w-2xl animate-pulse rounded-lg bg-slate-200/65"></div>
            <div class="h-4 w-full max-w-lg animate-pulse rounded-lg bg-slate-200/55"></div>
        </div>

        {{-- Stat cards strip (dashboard + many hub pages use a similar grid) --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @for ($i = 0; $i < 4; $i++)
                <div class="rounded-3xl border border-white/80 bg-white/90 p-6 shadow-sm ring-1 ring-gray-900/[0.04]">
                    <div class="flex justify-between gap-3">
                        <div class="h-10 w-10 animate-pulse rounded-2xl bg-slate-200/80"></div>
                        <div class="h-4 w-4 shrink-0 rounded bg-slate-200/55"></div>
                    </div>
                    <div class="mt-4 h-3 w-28 animate-pulse rounded-md bg-slate-200/60"></div>
                    <div class="mt-2 h-10 w-[4.5rem] animate-pulse rounded-lg bg-slate-200/75"></div>
                </div>
            @endfor
        </div>

        {{-- Two-column: quick links + list / detail (matches dashboard lower section) --}}
        <div class="grid gap-8 lg:grid-cols-2">
            <div class="min-w-0 space-y-4">
                <div class="h-3.5 w-32 animate-pulse rounded-md bg-slate-200/70"></div>
                <div class="grid gap-3 sm:grid-cols-2">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="h-28 animate-pulse rounded-2xl border border-slate-200/60 bg-gradient-to-b from-slate-100/90 to-white/60 p-4 shadow-sm"></div>
                    @endfor
                </div>
            </div>
            <div class="min-w-0 space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="h-3.5 w-28 animate-pulse rounded-md bg-slate-200/70"></div>
                    <div class="h-4 w-16 animate-pulse rounded-md bg-slate-200/55"></div>
                </div>
                <div class="space-y-3">
                    @for ($i = 0; $i < 3; $i++)
                        <div class="flex gap-3 rounded-2xl border border-slate-200/80 bg-white p-3 shadow-sm ring-1 ring-gray-900/[0.03]">
                            <div class="h-16 w-20 shrink-0 animate-pulse rounded-xl bg-slate-200/75"></div>
                            <div class="min-w-0 flex-1 space-y-2 pt-0.5">
                                <div class="h-4 w-3/5 max-w-[12rem] animate-pulse rounded-md bg-slate-200/78"></div>
                                <div class="h-3 w-full max-w-[10rem] animate-pulse rounded-md bg-slate-200/60"></div>
                                <div class="h-3 w-32 animate-pulse rounded-md bg-slate-200/55"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>

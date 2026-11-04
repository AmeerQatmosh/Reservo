@extends('layouts.app')

@section('title', 'Book rooms & meeting spaces')

@section('content')
    <style>
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 6rem;
        }
        [id]:target {
            animation: reservo-anchor-arrive 0.65s cubic-bezier(0.22, 1, 0.36, 1);
        }
        a[href="#features"],
        a[href="#plans"],
        a[href="#how-it-works"],
        a[href="#about"],
        a[href="#faq"] {
            transition: transform 160ms ease;
        }
        a[href="#features"]:active,
        a[href="#plans"]:active,
        a[href="#how-it-works"]:active,
        a[href="#about"]:active,
        a[href="#faq"]:active {
            transform: scale(0.97);
        }
        @keyframes reservo-anchor-arrive {
            from {
                opacity: 0.72;
                transform: translateY(0.75rem);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @media (prefers-reduced-motion: no-preference) {
            [data-reservo-reveal]:not(.reservo-reveal--visible) {
                opacity: 0;
                transform: translateY(1.35rem);
            }
            [data-reservo-reveal] {
                transition:
                    opacity 0.75s cubic-bezier(0.22, 1, 0.36, 1),
                    transform 0.75s cubic-bezier(0.22, 1, 0.36, 1);
            }
            [data-reservo-reveal].reservo-reveal--visible {
                opacity: 1;
                transform: translateY(0);
            }
            [data-reservo-reveal-stagger]:not(.reservo-reveal--visible) {
                opacity: 0;
                transform: translateY(1.35rem);
            }
            [data-reservo-reveal-stagger] {
                transition:
                    opacity 0.75s cubic-bezier(0.22, 1, 0.36, 1),
                    transform 0.75s cubic-bezier(0.22, 1, 0.36, 1);
            }
            [data-reservo-reveal-stagger].reservo-reveal--visible {
                opacity: 1;
                transform: translateY(0);
            }
            [data-reservo-reveal-stagger]:not(.reservo-reveal--visible) > [data-reservo-reveal-item] {
                opacity: 0;
                transform: translateY(1rem);
            }
            [data-reservo-reveal-stagger] > [data-reservo-reveal-item] {
                transition:
                    opacity 0.6s cubic-bezier(0.22, 1, 0.36, 1),
                    transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
            }
            [data-reservo-reveal-stagger].reservo-reveal--visible > [data-reservo-reveal-item]:nth-child(1) {
                transition-delay: 0.05s;
            }
            [data-reservo-reveal-stagger].reservo-reveal--visible > [data-reservo-reveal-item]:nth-child(2) {
                transition-delay: 0.12s;
            }
            [data-reservo-reveal-stagger].reservo-reveal--visible > [data-reservo-reveal-item]:nth-child(3) {
                transition-delay: 0.19s;
            }
            [data-reservo-reveal-stagger].reservo-reveal--visible > [data-reservo-reveal-item] {
                opacity: 1;
                transform: translateY(0);
            }
            [data-reservo-reveal='eager']:not(.reservo-reveal--visible) .reservo-landing-hero-col {
                opacity: 0;
                transform: translateY(1.15rem);
            }
            [data-reservo-reveal='eager'] .reservo-landing-hero-col {
                transition:
                    opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1),
                    transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
            }
            [data-reservo-reveal='eager'].reservo-reveal--visible .reservo-landing-hero-col:nth-child(1) {
                transition-delay: 0.06s;
            }
            [data-reservo-reveal='eager'].reservo-reveal--visible .reservo-landing-hero-col:nth-child(2) {
                transition-delay: 0.14s;
            }
            [data-reservo-reveal='eager'].reservo-reveal--visible .reservo-landing-hero-col {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }
            [id]:target {
                animation: none;
            }
            [data-reservo-reveal],
            [data-reservo-reveal-stagger],
            [data-reservo-reveal-stagger] > [data-reservo-reveal-item],
            [data-reservo-reveal='eager'] .reservo-landing-hero-col {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
        }
    </style>

    {{-- Hero: split layout — message + instant “this is scheduling” visual --}}
    <div
        class="relative overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 p-6 text-white shadow-[0_20px_50px_-15px_rgba(15,23,42,0.45)] sm:rounded-3xl sm:p-10 md:p-12 lg:p-14"
        data-reservo-reveal="eager"
    >
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_100%_60%_at_0%_0%,rgba(255,255,255,0.07),transparent_55%)]"></div>
        {{-- Soft halos without filter:blur — Firefox GPU cost on large filtered layers --}}
        <div class="pointer-events-none absolute -right-20 -top-16 h-80 w-80 rounded-full bg-indigo-500/[0.12]"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-16 h-72 w-72 rounded-full bg-cyan-400/[0.08]"></div>
        <div class="pointer-events-none absolute right-1/4 top-1/2 h-48 w-48 -translate-y-1/2 rounded-full bg-violet-500/[0.07]"></div>

        <div class="reservo-landing-hero-grid relative grid items-center gap-10 lg:grid-cols-[minmax(0,1fr)_minmax(0,26rem)] lg:gap-12 xl:gap-16">
            <div class="reservo-landing-hero-col relative max-w-xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/12 bg-white/[0.09] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-indigo-100/95 shadow-sm shadow-slate-950/20">
                    <x-lucide name="building-2" class="h-3.5 w-3.5 text-cyan-200/90" />
                    Room &amp; space reservations
                </div>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight sm:text-4xl md:text-5xl lg:text-[2.75rem] lg:leading-[1.1]">
                    Find a room.<br class="hidden sm:block" />
                    Lock the time.<br class="hidden sm:block" />
                    No double bookings.
                </h1>
                <p class="mt-5 text-base leading-relaxed text-slate-200/95 sm:text-lg">
                    <span class="font-medium text-white">Reservo</span> is for teams sharing meeting rooms, studios, and event spaces.
                    Pick a slot and the app enforces the schedule so two groups never collide in the same room.
                </p>
                <ul class="mt-6 flex flex-col gap-2 text-sm text-indigo-100/85 sm:flex-row sm:flex-wrap sm:gap-x-6 sm:gap-y-2">
                    <li class="flex items-center gap-2">
                        <x-lucide name="calendar-check" class="h-4 w-4 shrink-0 text-emerald-300/90" />
                        Real-time availability
                    </li>
                    <li class="flex items-center gap-2">
                        <x-lucide name="shield" class="h-4 w-4 shrink-0 text-cyan-200/80" />
                        Overlap conflicts blocked
                    </li>
                </ul>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                    @unless (config('reservo.portfolio_mode'))
                    <a href="{{ route('rooms.index') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-6 py-3.5 text-sm font-semibold text-slate-900 shadow-lg transition hover:bg-slate-100">
                        <x-lucide name="search" class="h-4 w-4" />
                        Browse rooms
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/30 bg-white/12 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-white/18">
                            Create an account
                        </a>
                    @else
                        <a href="{{ route('reservations.my') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/30 bg-white/12 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-white/18">
                            My reservations
                        </a>
                    @endguest
                    @endunless
                </div>
            </div>

            {{-- Interactive schedule board: drag bookings; overlaps mirror Reservo’s interval rule --}}
            <div class="reservo-landing-hero-col relative mx-auto w-full max-w-md lg:mx-0 lg:max-w-none">
                <div class="absolute -inset-px rounded-[1.15rem] bg-gradient-to-br from-indigo-400/35 via-cyan-400/14 to-transparent opacity-80 sm:rounded-3xl"></div>
                <div
                    class="relative overflow-hidden rounded-[1.15rem] border border-white/22 bg-gradient-to-b from-white/[0.16] to-white/[0.08] p-5 shadow-[0_24px_48px_-12px_rgba(15,23,42,0.55)] sm:rounded-3xl sm:p-6"
                    data-reservo-schedule-demo
                    role="region"
                    aria-label="Interactive demo: drag booking blocks. Overlapping times highlight in red, like blocked saves in the app."
                >
                    <div class="pointer-events-none absolute inset-0 rounded-[inherit] bg-[radial-gradient(ellipse_120%_80%_at_0%_-20%,rgba(255,255,255,0.14),transparent_50%)]"></div>
                    <style>
                        [data-reservo-schedule-demo] .reservo-demo-header-label {
                            letter-spacing: 0.22em;
                        }
                        [data-reservo-schedule-demo] .reservo-hero-track {
                            position: relative;
                            margin-top: 0.625rem;
                            height: 2.5rem;
                            padding: 0.375rem;
                            border-radius: 0.625rem;
                            background-color: rgba(15, 23, 42, 0.52);
                            background-image: repeating-linear-gradient(
                                to right,
                                transparent 0,
                                transparent calc(10% - 1px),
                                rgba(255, 255, 255, 0.04) calc(10% - 1px),
                                rgba(255, 255, 255, 0.04) 10%
                            );
                            box-shadow:
                                inset 0 1px 2px rgba(0, 0, 0, 0.28),
                                0 0 0 1px rgba(255, 255, 255, 0.07);
                        }
                        [data-reservo-schedule-demo] .reservo-hero-room-row {
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            gap: 0.75rem;
                            font-size: 0.75rem;
                            font-weight: 600;
                            color: rgb(224 231 255 / 0.92);
                        }
                        [data-reservo-schedule-demo] .reservo-hero-room-dot {
                            flex-shrink: 0;
                            width: 0.5rem;
                            height: 0.5rem;
                            border-radius: 9999px;
                            box-shadow:
                                0 0 0 2px rgba(255, 255, 255, 0.12),
                                0 1px 2px rgba(0, 0, 0, 0.2);
                        }
                        [data-reservo-schedule-demo] .reservo-hero-room-dot--a {
                            background: linear-gradient(145deg, rgb(103 232 249), rgb(6 182 212));
                        }
                        [data-reservo-schedule-demo] .reservo-hero-room-dot--b {
                            background: linear-gradient(145deg, rgb(196 181 253), rgb(139 92 246));
                        }
                        [data-reservo-schedule-demo] .reservo-hero-room-dot--c {
                            background: linear-gradient(145deg, rgb(253 224 71), rgb(245 158 11));
                        }
                        [data-reservo-schedule-demo] .reservo-hero-track-meta {
                            font-size: 0.6875rem;
                            font-weight: 500;
                            font-variant-numeric: tabular-nums;
                            letter-spacing: 0.06em;
                            color: rgb(199 210 254 / 0.55);
                            white-space: nowrap;
                        }
                        [data-reservo-schedule-demo] [data-reservo-block] {
                            transition:
                                box-shadow 0.15s ease,
                                filter 0.15s ease,
                                transform 0.12s ease;
                        }
                        [data-reservo-schedule-demo] [data-reservo-block]:active {
                            transform: scale(0.992);
                        }
                        [data-reservo-schedule-demo] .reservo-hero-slot {
                            position: absolute;
                            top: 0.375rem;
                            bottom: 0.375rem;
                            cursor: grab;
                            touch-action: none;
                            user-select: none;
                            border-radius: 0.4375rem;
                            border: 1px solid rgba(255, 255, 255, 0.16);
                            box-shadow:
                                0 2px 8px rgba(0, 0, 0, 0.32),
                                inset 0 1px 0 rgba(255, 255, 255, 0.2);
                        }
                        [data-reservo-schedule-demo] .reservo-hero-slot:active {
                            cursor: grabbing;
                        }
                        [data-reservo-schedule-demo] .reservo-hero-slot--indigo-strong {
                            background: linear-gradient(180deg, rgb(165 180 252 / 0.96), rgb(99 102 241 / 0.9));
                        }
                        [data-reservo-schedule-demo] .reservo-hero-slot--indigo-soft {
                            background: linear-gradient(180deg, rgb(199 210 254 / 0.92), rgb(129 140 248 / 0.78));
                        }
                        [data-reservo-schedule-demo] .reservo-hero-slot--violet {
                            background: linear-gradient(180deg, rgb(196 181 253 / 0.94), rgb(124 58 237 / 0.82));
                        }
                        [data-reservo-schedule-demo] .reservo-hero-slot--slate {
                            background: linear-gradient(180deg, rgb(148 163 184 / 0.72), rgb(71 85 105 / 0.62));
                            border-color: rgba(255, 255, 255, 0.12);
                        }
                        [data-reservo-schedule-demo] .reservo-hero-slot--emerald {
                            background: linear-gradient(180deg, rgb(52 211 153 / 0.52), rgb(5 150 105 / 0.4));
                            border-color: rgba(110 231 183, 0.38);
                            box-shadow:
                                0 2px 10px rgba(16, 185, 129, 0.18),
                                inset 0 1px 0 rgba(255, 255, 255, 0.22);
                        }
                        [data-reservo-schedule-demo] .reservo-hero-slot--indigo-muted {
                            background: linear-gradient(180deg, rgb(129 140 248 / 0.55), rgb(79 70 229 / 0.42));
                            border-color: rgba(255, 255, 255, 0.14);
                        }
                        [data-reservo-schedule-demo] [data-reservo-block].reservo-hero-block--conflict {
                            box-shadow:
                                0 0 0 2px rgb(251 113 133 / 0.98),
                                0 4px 18px rgb(0 0 0 / 0.4),
                                inset 0 1px 0 rgba(255, 255, 255, 0.18);
                            filter: saturate(1.08) brightness(1.05);
                            z-index: 2;
                        }
                        [data-reservo-schedule-demo] [data-reservo-track-status] {
                            line-height: 1.35;
                        }
                        [data-reservo-schedule-demo] [data-reservo-track-status].reservo-track-status--overlap {
                            color: rgb(254 205 211 / 0.96);
                        }
                        [data-reservo-schedule-demo] [data-reservo-track-status].reservo-track-status--clear-indigo {
                            color: rgb(215 219 255 / 0.88);
                        }
                        [data-reservo-schedule-demo] [data-reservo-track-status].reservo-track-status--clear-emerald {
                            color: rgb(167 243 208 / 0.9);
                        }
                    </style>
                    <div class="relative flex items-start justify-between gap-3">
                        <div>
                            <p class="reservo-demo-header-label text-[10px] font-semibold uppercase text-indigo-100/80">Today’s bookings</p>
                            <p class="mt-1 text-sm font-semibold tracking-tight text-white">Three rooms · same building</p>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-emerald-400/35 bg-emerald-400/15 py-1 pl-2 pr-2.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-50 shadow-sm shadow-emerald-950/20">
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-300 opacity-40"></span>
                                <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                            </span>
                            Live
                        </span>
                    </div>
                    <div class="relative mt-4 flex gap-3 rounded-xl border border-white/10 bg-slate-950/35 px-3 py-2.5 sm:px-3.5 sm:py-3">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-indigo-500/25 text-indigo-50 shadow-inner shadow-indigo-950/30">
                            <x-lucide name="arrow-up-down" class="h-4 w-4 opacity-90" />
                        </span>
                        <p class="text-[11px] leading-relaxed text-indigo-100/88 sm:text-[12px]">
                            <span class="font-semibold text-white">Try it</span>
                            — drag any booking. Overlaps glow red — the same collision rule the app uses when you save.
                        </p>
                    </div>

                    <div class="relative mt-5 space-y-0">
                        <div
                            data-reservo-track-wrap
                            data-tone="indigo"
                            data-msg-clear="No overlap — both bookings fit this room."
                            data-msg-overlap="Conflict — overlapping times in the same room aren’t allowed."
                        >
                            <div class="reservo-hero-room-row">
                                <span class="inline-flex min-w-0 items-center gap-2">
                                    <span class="reservo-hero-room-dot reservo-hero-room-dot--a" aria-hidden="true"></span>
                                    <span class="truncate">Focus Room A</span>
                                </span>
                                <span class="reservo-hero-track-meta">9:00–17:00</span>
                            </div>
                            <div data-reservo-track class="reservo-hero-track">
                                <div
                                    data-reservo-block
                                    data-left-pct="6"
                                    data-width-pct="34"
                                    class="reservo-hero-slot reservo-hero-slot--indigo-strong"
                                    title="Drag to reschedule"
                                ></div>
                                <div
                                    data-reservo-block
                                    data-left-pct="54"
                                    data-width-pct="28"
                                    class="reservo-hero-slot reservo-hero-slot--indigo-soft"
                                    title="Drag to reschedule"
                                ></div>
                            </div>
                            <p data-reservo-track-status class="reservo-track-status mt-2 min-h-[1.35rem] text-[11px] leading-snug"></p>
                        </div>

                        <div class="my-4 h-px bg-gradient-to-r from-transparent via-white/12 to-transparent" aria-hidden="true"></div>

                        <div
                            data-reservo-track-wrap
                            data-tone="indigo"
                            data-msg-clear="No clash in Lab South — schedule looks good."
                            data-msg-overlap="Conflict — second booking here isn’t allowed."
                        >
                            <div class="reservo-hero-room-row">
                                <span class="inline-flex min-w-0 items-center gap-2">
                                    <span class="reservo-hero-room-dot reservo-hero-room-dot--b" aria-hidden="true"></span>
                                    <span class="truncate">Lab South</span>
                                </span>
                                <span class="reservo-hero-track-meta">9:00–17:00</span>
                            </div>
                            <div data-reservo-track class="reservo-hero-track">
                                <div
                                    data-reservo-block
                                    data-left-pct="8"
                                    data-width-pct="30"
                                    class="reservo-hero-slot reservo-hero-slot--violet"
                                    title="Drag to reschedule"
                                ></div>
                                <div
                                    data-reservo-block
                                    data-left-pct="62"
                                    data-width-pct="26"
                                    class="reservo-hero-slot reservo-hero-slot--slate"
                                    title="Drag to reschedule"
                                ></div>
                            </div>
                            <p data-reservo-track-status class="reservo-track-status mt-2 min-h-[1.35rem] text-[11px] leading-snug"></p>
                        </div>

                        <div class="my-4 h-px bg-gradient-to-r from-transparent via-white/12 to-transparent" aria-hidden="true"></div>

                        <div
                            data-reservo-track-wrap
                            data-tone="emerald"
                            data-msg-clear="Open slot — reserve it before someone else grabs it."
                            data-msg-overlap="Those times overlap — only one group can hold the room."
                        >
                            <div class="reservo-hero-room-row">
                                <span class="inline-flex min-w-0 items-center gap-2">
                                    <span class="reservo-hero-room-dot reservo-hero-room-dot--c" aria-hidden="true"></span>
                                    <span class="truncate">Town Hall</span>
                                </span>
                                <span class="reservo-hero-track-meta">9:00–17:00</span>
                            </div>
                            <div data-reservo-track class="reservo-hero-track">
                                <div
                                    data-reservo-block
                                    data-left-pct="10"
                                    data-width-pct="38"
                                    class="reservo-hero-slot reservo-hero-slot--emerald"
                                    title="Drag to reschedule"
                                ></div>
                                <div
                                    data-reservo-block
                                    data-left-pct="58"
                                    data-width-pct="24"
                                    class="reservo-hero-slot reservo-hero-slot--indigo-muted"
                                    title="Drag to reschedule"
                                ></div>
                            </div>
                            <p data-reservo-track-status class="reservo-track-status mt-2 min-h-[1.35rem] text-[11px] leading-snug"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- How it works --}}
    <section
        id="how-it-works"
        class="relative mt-10 scroll-mt-24 overflow-hidden rounded-2xl border border-indigo-200/40 bg-gradient-to-b from-white via-white to-indigo-50/50 p-6 shadow-[0_20px_40px_-20px_rgba(79,70,229,0.15)] sm:mt-14 sm:rounded-3xl sm:p-8 md:p-10"
        data-reservo-reveal
        aria-labelledby="reservo-how-it-works-heading"
    >
        <div class="pointer-events-none absolute -right-16 top-0 h-52 w-52 rounded-full bg-indigo-400/[0.09]"></div>
        <div class="pointer-events-none absolute -left-12 bottom-0 h-44 w-44 rounded-full bg-cyan-400/[0.08]"></div>
        <div class="relative text-center">
            <p class="text-[11px] font-semibold uppercase tracking-[0.26em] text-indigo-600/80">How it works</p>
            <h2 id="reservo-how-it-works-heading" class="mt-3 text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl md:text-[1.65rem] md:leading-tight">
                From browse to booked in three steps
            </h2>
            <p class="mx-auto mt-2 max-w-lg text-sm leading-relaxed text-slate-600 sm:text-[0.9375rem]">
                Follow a simple path from discovering a space to seeing your reservation on the calendar.
            </p>
        </div>

        <div class="relative mx-auto mt-9 max-w-6xl space-y-5 sm:mt-11 sm:space-y-6">
            <div class="pointer-events-none absolute bottom-8 left-7 top-8 hidden w-px bg-gradient-to-b from-indigo-200 via-violet-200 to-emerald-200 sm:block" aria-hidden="true"></div>
            <article class="relative grid overflow-hidden rounded-2xl border border-slate-200/90 bg-white/95 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-md sm:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
                <div class="relative min-h-48 overflow-hidden bg-slate-100 sm:min-h-64">
                    <img src="{{ asset('images/demo/rooms/space-view.png') }}" alt="Example view of a shared creative workspace" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                    <span class="absolute bottom-3 left-3 rounded-full border border-white/60 bg-white/90 px-3 py-1 text-[11px] font-semibold text-slate-700 shadow-sm">Example workspace view</span>
                </div>
                <div class="flex flex-col justify-center p-5 sm:p-8">
                    <span class="inline-flex w-fit items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-indigo-700">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600 text-xs text-white">1</span>
                        Browse spaces
                    </span>
                    <h3 class="mt-4 text-lg font-semibold tracking-tight text-slate-900 sm:text-xl">Find a space that fits</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">Explore room details, capacity, and amenities to find the right setting for your next meeting.</p>
                </div>
            </article>
            <article class="relative grid overflow-hidden rounded-2xl border border-slate-200/90 bg-white/95 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-md sm:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
                <div class="relative min-h-48 overflow-hidden bg-slate-100 sm:min-h-120">
                    <img src="{{ asset('images/demo/rooms/booking-view.png') }}" alt="Example boardroom ready for a scheduled meeting" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                    <span class="absolute bottom-3 left-3 rounded-full border border-white/60 bg-white/90 px-3 py-1 text-[11px] font-semibold text-slate-700 shadow-sm">Example booking view</span>
                </div>
                <div class="flex flex-col justify-center p-5 sm:p-8">
                    <span class="inline-flex w-fit items-center gap-2 rounded-full bg-violet-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-violet-700">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-violet-600 text-xs text-white">2</span>
                        Choose a time
                    </span>
                    <h3 class="mt-4 text-lg font-semibold tracking-tight text-slate-900 sm:text-xl">Pick your reservation window</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">Choose a start and end time. Reservo checks availability and helps prevent scheduling conflicts.</p>
                </div>
            </article>
            <article class="relative grid overflow-hidden rounded-2xl border border-slate-200/90 bg-white/95 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-md sm:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
                <div class="relative min-h-48 overflow-hidden bg-slate-100 sm:min-h-64">
                    <img src="{{ asset('images/demo/rooms/reservation-sync.png') }}" alt="Example quiet room available for focused work" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                    <span class="absolute bottom-3 left-3 rounded-full border border-white/60 bg-white/90 px-3 py-1 text-[11px] font-semibold text-slate-700 shadow-sm">Example confirmation view</span>
                </div>
                <div class="flex flex-col justify-center p-5 sm:p-8">
                    <span class="inline-flex w-fit items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-emerald-700">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-600 text-xs text-white">3</span>
                        You’re confirmed
                    </span>
                    <h3 class="mt-4 text-lg font-semibold tracking-tight text-slate-900 sm:text-xl">Keep your team in sync</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">Your reservation is easy to find later, while your team can see which spaces are booked.</p>
                </div>
            </article>
        </div>
    </section>

    <section id="features" class="mt-10 scroll-mt-24 sm:mt-14" aria-labelledby="reservo-features-heading">
        <div class="mb-6 text-center">
            <p class="text-[11px] font-semibold uppercase tracking-[0.26em] text-indigo-600/80">Features</p>
            <h2 id="reservo-features-heading" class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">
                Everything your team needs to stay in sync
            </h2>
            <p class="mx-auto mt-2 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base">
                Keep room schedules clear, make reservations easy to manage, and give everyone a better view of shared spaces.
            </p>
        </div>
        <div class="grid gap-5 sm:grid-cols-3 sm:gap-6" data-reservo-reveal-stagger>
            <div
                data-reservo-reveal-item
                class="rounded-3xl border border-slate-200/90 bg-white/90 p-6 shadow-sm transition duration-300 ease-out hover:-translate-y-0.5 hover:border-slate-300/90 hover:shadow-md"
            >
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white shadow-md">
                    <x-lucide name="circle-x" class="h-5 w-5" />
                </div>
                <div class="mt-4 text-sm font-semibold text-gray-900">No double bookings</div>
                <p class="mt-2 text-sm leading-6 text-gray-600">
                    If an interval overlaps an existing reservation, the save is blocked—partial overlaps included, back-to-back still fine.
                </p>
            </div>
            <div
                data-reservo-reveal-item
                class="rounded-3xl border border-slate-200/90 bg-white/90 p-6 shadow-sm transition duration-300 ease-out hover:-translate-y-0.5 hover:border-slate-300/90 hover:shadow-md"
            >
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white shadow-md">
                    <x-lucide name="clipboard-list" class="h-5 w-5" />
                </div>
                <div class="mt-4 text-sm font-semibold text-gray-900">Built for admins</div>
                <p class="mt-2 text-sm leading-6 text-gray-600">
                    Manage spaces, review reservations, and archive rooms cleanly with soft delete when history matters.
                </p>
            </div>
            <div
                data-reservo-reveal-item
                class="rounded-3xl border border-slate-200/90 bg-white/90 p-6 shadow-sm transition duration-300 ease-out hover:-translate-y-0.5 hover:border-slate-300/90 hover:shadow-md"
            >
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white shadow-md">
                    <x-lucide name="users" class="h-5 w-5" />
                </div>
                <div class="mt-4 text-sm font-semibold text-gray-900">Simple for the whole team</div>
                <p class="mt-2 text-sm leading-6 text-gray-600">
                    Straightforward forms and navigation—easy to adopt without training docs.
                </p>
            </div>
        </div>
    </section>

    <section
        id="plans"
        class="mt-14 scroll-mt-24 sm:mt-16"
        aria-labelledby="reservo-plans-heading"
    >
        <div class="text-center" data-reservo-reveal>
            <p class="text-[11px] font-semibold uppercase tracking-[0.26em] text-indigo-600/80">Plans &amp; pricing</p>
            <h2 id="reservo-plans-heading" class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">
                A plan for every kind of team
            </h2>
            <p class="mx-auto mt-2 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base">
                Sample plans to show how Reservo could scale with your spaces and your team.
            </p>
            <p class="mt-2 text-xs font-medium text-slate-500">Illustrative pricing for demonstration purposes.</p>
        </div>
        <div class="mt-7 grid gap-5 md:grid-cols-3 md:gap-6" data-reservo-reveal-stagger>
            <article data-reservo-reveal-item class="flex flex-col rounded-3xl border border-slate-200/90 bg-white/90 p-6 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-md sm:p-7">
                <p class="text-sm font-semibold text-slate-900">Starter</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">$0<span class="text-sm font-medium text-slate-500"> / month</span></p>
                <p class="mt-2 min-h-10 text-sm leading-relaxed text-slate-600">A simple way for a small team to get organized.</p>
                <ul class="mt-5 space-y-3 text-sm text-slate-700">
                    <li class="flex gap-2"><x-lucide name="check" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" />Up to 3 spaces</li>
                    <li class="flex gap-2"><x-lucide name="check" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" />Shared availability</li>
                    <li class="flex gap-2"><x-lucide name="check" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" />Basic reservations</li>
                </ul>
                <a href="{{ route('login') }}" class="mt-7 inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 transition hover:bg-slate-50">Explore Reservo</a>
            </article>
            <article data-reservo-reveal-item class="relative flex flex-col rounded-3xl border border-indigo-500 bg-slate-950 p-6 text-white shadow-xl shadow-indigo-950/15 transition duration-300 hover:-translate-y-0.5 sm:p-7">
                <span class="absolute right-5 top-5 rounded-full bg-indigo-400/15 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-indigo-200">Popular</span>
                <p class="text-sm font-semibold text-indigo-100">Team</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight">$29<span class="text-sm font-medium text-slate-400"> / month</span></p>
                <p class="mt-2 min-h-10 text-sm leading-relaxed text-slate-300">More room to grow for busy teams.</p>
                <ul class="mt-5 space-y-3 text-sm text-slate-200">
                    <li class="flex gap-2"><x-lucide name="check" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-300" />Up to 15 spaces</li>
                    <li class="flex gap-2"><x-lucide name="check" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-300" />Team reservation views</li>
                    <li class="flex gap-2"><x-lucide name="check" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-300" />Admin management tools</li>
                </ul>
                <a href="{{ route('login') }}" class="mt-7 inline-flex items-center justify-center rounded-xl bg-white px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-indigo-50">Choose Team</a>
            </article>
            <article data-reservo-reveal-item class="flex flex-col rounded-3xl border border-slate-200/90 bg-white/90 p-6 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-md sm:p-7">
                <p class="text-sm font-semibold text-slate-900">Business</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">$79<span class="text-sm font-medium text-slate-500"> / month</span></p>
                <p class="mt-2 min-h-10 text-sm leading-relaxed text-slate-600">Flexible space management for larger organizations.</p>
                <ul class="mt-5 space-y-3 text-sm text-slate-700">
                    <li class="flex gap-2"><x-lucide name="check" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" />Unlimited spaces</li>
                    <li class="flex gap-2"><x-lucide name="check" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" />Advanced administration</li>
                    <li class="flex gap-2"><x-lucide name="check" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" />Priority support</li>
                </ul>
                <a href="{{ route('login') }}" class="mt-7 inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 transition hover:bg-slate-50">Contact us</a>
            </article>
        </div>
    </section>

    <section
        id="about"
        class="mt-14 scroll-mt-24 overflow-hidden rounded-3xl border border-slate-200/80 bg-white/90 shadow-sm sm:mt-16"
        aria-labelledby="reservo-about-heading"
        data-reservo-reveal
    >
        <div class="grid sm:grid-cols-2 sm:items-stretch">
            <div class="relative min-h-64 overflow-hidden bg-slate-100 sm:min-w-128 sm:min-h-96">
                <img src="{{ asset('images/demo/rooms/about.png') }}" alt="Bright shared meeting room with a long conference table" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                <span class="absolute bottom-4 left-4 rounded-full border border-white/60 bg-white/90 px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm">Spaces made easier to share</span>
            </div>
            <div class="flex flex-col justify-center p-6 sm:p-10">
                <p class="text-[11px] font-semibold uppercase tracking-[0.26em] text-indigo-600/80">About Reservo</p>
                <h2 id="reservo-about-heading" class="mt-3 text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">
                    Better use of every shared space
                </h2>
                <p class="mt-4 text-sm leading-7 text-slate-600 sm:text-base">
                    Reservo helps teams coordinate shared rooms and workspaces with a clear, reliable booking experience. Browse the spaces that fit, check availability, and keep everyone on the same schedule—all from one simple place.
                </p>
                @unless (config('reservo.portfolio_mode'))
                    <a href="{{ route('rooms.index') }}" class="mt-6 inline-flex w-fit items-center gap-2 text-sm font-semibold text-indigo-700 transition hover:text-indigo-900">
                        Explore available rooms
                        <x-lucide name="arrow-right" class="h-4 w-4" />
                    </a>
                @endunless
            </div>
        </div>
    </section>

    <section
        id="faq"
        class="mt-14 scroll-mt-24 sm:mt-16"
        aria-labelledby="reservo-faq-heading"
        data-reservo-reveal
    >
        <div class="text-center">
            <p class="text-[11px] font-semibold uppercase tracking-[0.26em] text-indigo-600/80">FAQ</p>
            <h2 id="reservo-faq-heading" class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">
                A few things you might be wondering
            </h2>
        </div>
        <div class="mx-auto mt-7 grid max-w-3xl gap-4">
            <details class="group rounded-2xl border border-slate-200/90 bg-white/90 p-5 shadow-sm transition hover:border-indigo-200 hover:shadow-md sm:p-6">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-left text-sm font-semibold text-slate-900 [&::-webkit-details-marker]:hidden">
                    How do I find an available room?
                    <x-lucide name="chevron-down" class="h-4 w-4 shrink-0 text-slate-500 transition group-open:rotate-180" />
                </summary>
                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-600">
                    Browse the available spaces and review their details to find a room that works for your group and schedule.
                </p>
            </details>
            <details class="group rounded-2xl border border-slate-200/90 bg-white/90 p-5 shadow-sm transition hover:border-indigo-200 hover:shadow-md sm:p-6">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-left text-sm font-semibold text-slate-900 [&::-webkit-details-marker]:hidden">
                    Can two teams book the same room at once?
                    <x-lucide name="chevron-down" class="h-4 w-4 shrink-0 text-slate-500 transition group-open:rotate-180" />
                </summary>
                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-600">
                    Reservo checks for overlapping reservations and prevents conflicting time slots for the same room.
                </p>
            </details>
            <details class="group rounded-2xl border border-slate-200/90 bg-white/90 p-5 shadow-sm transition hover:border-indigo-200 hover:shadow-md sm:p-6">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-left text-sm font-semibold text-slate-900 [&::-webkit-details-marker]:hidden">
                    Where can I see my reservations?
                    <x-lucide name="chevron-down" class="h-4 w-4 shrink-0 text-slate-500 transition group-open:rotate-180" />
                </summary>
                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-600">
                    Sign in to view and manage your reservations from your account.
                </p>
            </details>
        </div>
    </section>

    @if ($featuredRooms->isNotEmpty())
        <div class="mt-14" data-reservo-reveal>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Start exploring</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900">Rooms you can book right now</h2>
                    <p class="mt-1 max-w-xl text-sm text-gray-600">Tap a card to see details and grab a time slot.</p>
                </div>
                <a href="{{ route('rooms.index') }}" class="shrink-0 text-sm font-medium text-indigo-700 hover:text-indigo-900">View all rooms →</a>
            </div>
            <div class="mt-6 grid gap-6 lg:grid-cols-3">
                @foreach ($featuredRooms as $room)
                    <a href="{{ route('rooms.show', $room->id) }}" class="group overflow-hidden rounded-3xl border border-white/70 bg-white/90 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="aspect-[16/10] overflow-hidden bg-gray-100">
                            @if ($room->image_url)
                                <img
                                    src="{{ $room->image_url }}"
                                    alt="{{ $room->name }}"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                                    loading="lazy"
                                >
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 text-sm text-gray-500">
                                    No photo
                                </div>
                            @endif
                        </div>
                        <div class="p-5">
                            <div class="text-lg font-semibold text-gray-900">{{ $room->name }}</div>
                            @if ($room->location)
                                <p class="mt-1 line-clamp-1 text-xs text-gray-500">{{ $room->location }}</p>
                            @endif
                            <p class="mt-2 text-xs font-medium text-gray-600">
                                Up to {{ $room->capacity }} people
                                @if ($room->size_sqm)
                                    <span class="text-gray-400"> · </span>{{ $room->size_sqm }} m²
                                @endif
                            </p>
                            @if (is_array($room->amenities) && count($room->amenities))
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach (array_slice($room->amenities, 0, 3) as $amenity)
                                        <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-600">{{ $amenity }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <p class="mt-3 line-clamp-2 text-sm text-gray-600">{{ $room->description }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <section
        class="relative mt-14 overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 px-6 py-10 text-center text-white shadow-[0_24px_54px_-18px_rgba(15,23,42,0.5)] sm:mt-16 sm:px-10 sm:py-14"
        aria-labelledby="reservo-final-cta-heading"
        data-reservo-reveal
    >
        <div class="pointer-events-none absolute -right-10 -top-20 h-56 w-56 rounded-full bg-indigo-400/15"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-12 h-56 w-56 rounded-full bg-cyan-300/10"></div>
        <div class="relative mx-auto max-w-2xl">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-200">Make room for what matters</p>
            <h2 id="reservo-final-cta-heading" class="mt-3 text-2xl font-semibold tracking-tight sm:text-3xl">
                Ready to make room bookings simpler?
            </h2>
            <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-indigo-100/85 sm:text-base">
                Find a space, check the schedule, and bring your team together with Reservo.
            </p>
            <div class="mt-7 flex flex-col justify-center gap-3 sm:flex-row">
                @unless (config('reservo.portfolio_mode'))
                    <a href="{{ route('rooms.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-indigo-50">
                        Browse spaces
                        <x-lucide name="arrow-right" class="h-4 w-4" />
                    </a>
                @endunless
                @guest
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-xl border border-white/25 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:border-white/40 hover:bg-white/10">
                        Sign in
                    </a>
                @endguest
            </div>
        </div>
    </section>

    <script>
        document.querySelectorAll('a[href^="#"]').forEach(function (link) {
            link.addEventListener('click', function () {
                var menu = document.querySelector('.reservo-mobile-menu');
                if (menu && menu.open) {
                    menu.removeAttribute('open');
                }
            });
        });

        (function () {
            function trackInnerWidthPx(track) {
                var s = window.getComputedStyle(track);
                var pl = parseFloat(s.paddingLeft) || 0;
                var pr = parseFloat(s.paddingRight) || 0;
                return Math.max(1, track.clientWidth - pl - pr);
            }

            function applyBlockGeometry(block) {
                block.style.left = block.dataset.leftPct + '%';
                block.style.width = block.dataset.widthPct + '%';
            }

            function intervalsOverlap(leftA, wA, leftB, wB) {
                var rightA = leftA + wA;
                var rightB = leftB + wB;
                return leftA < rightB && rightA > leftB;
            }

            function updateTrack(track) {
                var blocks = Array.prototype.slice.call(track.querySelectorAll('[data-reservo-block]'));
                var metrics = blocks.map(function (el) {
                    return {
                        el: el,
                        left: parseFloat(el.dataset.leftPct),
                        w: parseFloat(el.dataset.widthPct),
                    };
                });
                var conflictEls = new Set();
                for (var i = 0; i < metrics.length; i++) {
                    for (var j = i + 1; j < metrics.length; j++) {
                        var a = metrics[i];
                        var b = metrics[j];
                        if (intervalsOverlap(a.left, a.w, b.left, b.w)) {
                            conflictEls.add(a.el);
                            conflictEls.add(b.el);
                        }
                    }
                }
                blocks.forEach(function (b) {
                    var on = conflictEls.has(b);
                    b.classList.toggle('reservo-hero-block--conflict', on);
                    b.style.zIndex = on ? '2' : '';
                });

                var wrap = track.closest('[data-reservo-track-wrap]');
                if (!wrap) return;
                var status = wrap.querySelector('[data-reservo-track-status]');
                if (!status) return;
                var overlap = conflictEls.size > 0;
                var clearMsg = wrap.dataset.msgClear || '';
                var overlapMsg = wrap.dataset.msgOverlap || '';
                var tone = wrap.dataset.tone || 'indigo';
                status.textContent = overlap ? overlapMsg : clearMsg;
                status.classList.remove('reservo-track-status--overlap', 'reservo-track-status--clear-indigo', 'reservo-track-status--clear-emerald');
                if (overlap) {
                    status.classList.add('reservo-track-status--overlap');
                } else if (tone === 'emerald') {
                    status.classList.add('reservo-track-status--clear-emerald');
                } else {
                    status.classList.add('reservo-track-status--clear-indigo');
                }
            }

            var dragByBlock = new WeakMap();

            function onPointerMove(e) {
                var block = e.currentTarget;
                var st = dragByBlock.get(block);
                if (!st || e.pointerId !== st.pointerId) return;
                var innerW = trackInnerWidthPx(st.track);
                var dx = e.clientX - st.startClientX;
                var dPct = (dx / innerW) * 100;
                var w = st.widthPct;
                var next = st.startLeftPct + dPct;
                if (next < 0) next = 0;
                if (next > 100 - w) next = 100 - w;
                var rounded = Math.round(next * 10000) / 10000;
                block.dataset.leftPct = String(rounded);
                block.style.left = block.dataset.leftPct + '%';
                updateTrack(st.track);
            }

            function endDrag(e) {
                var block = e.currentTarget;
                var st = dragByBlock.get(block);
                if (!st || e.pointerId !== st.pointerId) return;
                dragByBlock.delete(block);
                try {
                    block.releasePointerCapture(e.pointerId);
                } catch (err) {}
                block.removeEventListener('pointermove', onPointerMove);
                block.removeEventListener('pointerup', endDrag);
                block.removeEventListener('pointercancel', endDrag);
            }

            function onPointerDown(e) {
                if (e.button !== undefined && e.button !== 0) return;
                var block = e.currentTarget;
                var track = block.closest('[data-reservo-track]');
                if (!track) return;
                e.preventDefault();
                dragByBlock.set(block, {
                    pointerId: e.pointerId,
                    startClientX: e.clientX,
                    startLeftPct: parseFloat(block.dataset.leftPct),
                    widthPct: parseFloat(block.dataset.widthPct),
                    track: track,
                });
                try {
                    block.setPointerCapture(e.pointerId);
                } catch (err) {}
                block.addEventListener('pointermove', onPointerMove);
                block.addEventListener('pointerup', endDrag);
                block.addEventListener('pointercancel', endDrag);
            }

            function init(root) {
                root.querySelectorAll('[data-reservo-track]').forEach(function (track) {
                    track.querySelectorAll('[data-reservo-block]').forEach(function (block) {
                        applyBlockGeometry(block);
                        block.addEventListener('pointerdown', onPointerDown);
                    });
                    updateTrack(track);
                });
            }

            document.querySelectorAll('[data-reservo-schedule-demo]').forEach(init);
        })();

        (function () {
            function initLandingReveal() {
                var reduceMq = window.matchMedia('(prefers-reduced-motion: reduce)');
                var reduce = reduceMq.matches;

                function revealAll() {
                    document.querySelectorAll('[data-reservo-reveal], [data-reservo-reveal-stagger]').forEach(function (el) {
                        el.classList.add('reservo-reveal--visible');
                    });
                }

                if (reduce) {
                    revealAll();
                    return;
                }

                document.querySelectorAll('[data-reservo-reveal="eager"]').forEach(function (el) {
                    requestAnimationFrame(function () {
                        requestAnimationFrame(function () {
                            el.classList.add('reservo-reveal--visible');
                        });
                    });
                });

                var observer = new IntersectionObserver(
                    function (entries) {
                        entries.forEach(function (entry) {
                            if (!entry.isIntersecting) return;
                            entry.target.classList.add('reservo-reveal--visible');
                            observer.unobserve(entry.target);
                        });
                    },
                    { root: null, rootMargin: '0px 0px -7% 0px', threshold: 0.06 }
                );

                document.querySelectorAll('[data-reservo-reveal]').forEach(function (el) {
                    if (el.getAttribute('data-reservo-reveal') === 'eager') return;
                    observer.observe(el);
                });
                document.querySelectorAll('[data-reservo-reveal-stagger]').forEach(function (el) {
                    observer.observe(el);
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initLandingReveal);
            } else {
                initLandingReveal();
            }
        })();
    </script>
@endsection

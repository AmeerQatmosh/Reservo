{{-- Expects: $variant: 'desktop'|'mobile' --}}
@php
    $isMobile = ($variant ?? 'desktop') === 'mobile';
    $iconDesktop = 'h-4 w-4 shrink-0 text-gray-500 transition-colors group-hover:text-gray-800 group-focus-visible:text-gray-800';
    $accountOpen = request()->routeIs('settings.index', 'profile.*', 'security.*');

    $itemClass = 'group box-border flex w-full min-w-0 items-center gap-2.5 rounded-lg px-2.5 py-1.5 text-left text-[15px] font-medium text-gray-900 transition hover:bg-gray-100 focus-visible:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-gray-900/20';
    $itemClassOn = 'group box-border flex w-full min-w-0 items-center gap-2.5 rounded-lg bg-gray-900 px-2.5 py-1.5 text-left text-[15px] font-medium text-white transition hover:bg-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-white/35';

    $iconMobile = 'h-[18px] w-[18px] shrink-0 text-gray-500 transition-colors group-hover:text-gray-800 group-focus-visible:text-gray-800';
    $iconMobileOn = 'h-[18px] w-[18px] shrink-0 text-white opacity-90 transition-opacity group-hover:opacity-100';

    $itemClassDesk = 'group box-border flex w-full min-w-0 items-center gap-2 rounded-md px-2.5 py-1.5 text-sm text-gray-700 transition hover:bg-gray-50 focus-visible:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-gray-900/18';
    $itemClassDeskOn = 'group box-border flex w-full min-w-0 items-center gap-2 rounded-md bg-gray-100 px-2.5 py-1.5 text-sm font-medium text-gray-900 transition hover:bg-gray-200/80 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-gray-900/22';
@endphp

@if ($isMobile)
    <details class="app-account-menu reservo-details mb-2 border-b border-gray-100 pb-3">
        <summary
            class="flex w-full cursor-pointer list-none items-center gap-3 rounded-xl border px-3 py-2 text-left shadow-sm transition outline-none focus-visible:ring-2 focus-visible:ring-gray-900/20 [&::-webkit-details-marker]:hidden @if ($accountOpen) border-gray-900/20 bg-gray-900/[0.04] ring-2 ring-gray-900/10 @else border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50 @endif"
            aria-label="Account and settings"
        >
            <span class="relative shrink-0 overflow-visible">
                <span class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 bg-gray-50 text-gray-600">
                    <x-lucide name="user" class="h-[18px] w-[18px]" />
                </span>
                @if (auth()->user()->isSuperAdmin())
                    <span
                        class="absolute bottom-0 right-0 flex h-4 min-w-4 translate-x-px translate-y-px items-center justify-center rounded-full border-2 border-white bg-violet-600 px-0.5 text-[8px] font-bold leading-none text-white shadow-sm"
                        title="Super admin"
                    >S</span>
                @elseif (auth()->user()->isAdmin())
                    <span
                        class="absolute bottom-0 right-0 flex h-4 min-w-4 translate-x-px translate-y-px items-center justify-center rounded-full border-2 border-white bg-emerald-600 px-0.5 text-[8px] font-bold leading-none text-white shadow-sm"
                        title="Admin"
                    >A</span>
                @endif
            </span>
            <span class="min-w-0 flex-1">
                <span class="block truncate text-sm font-semibold text-gray-900">Account</span>
                <span class="block truncate text-xs text-gray-500">{{ auth()->user()->email }}</span>
            </span>
        </summary>
        <div class="reservo-dropdown-panel mt-1.5 flex flex-col gap-px rounded-lg border border-gray-200 bg-white p-1 shadow-sm">
            <a
                href="{{ route('settings.index') }}"
                class="{{ request()->routeIs('settings.index') ? $itemClassOn : $itemClass }}"
            >
                <x-lucide name="settings" :class="request()->routeIs('settings.index') ? $iconMobileOn : $iconMobile" />
                Settings
            </a>
            <a
                href="{{ route('profile.edit') }}"
                class="{{ request()->routeIs('profile.*') ? $itemClassOn : $itemClass }}"
            >
                <x-lucide name="user" :class="request()->routeIs('profile.*') ? $iconMobileOn : $iconMobile" />
                Profile
            </a>
            <a
                href="{{ route('security.edit') }}"
                class="{{ request()->routeIs('security.*') ? $itemClassOn : $itemClass }}"
            >
                <x-lucide name="lock" :class="request()->routeIs('security.*') ? $iconMobileOn : $iconMobile" />
                Security
            </a>
        </div>
    </details>
@else
    <details class="app-account-menu reservo-details relative shrink-0 overflow-visible">
        <summary
            class="flex h-8 w-8 cursor-pointer list-none items-center justify-center overflow-visible rounded-full border shadow-sm transition outline-none focus-visible:ring-2 focus-visible:ring-gray-900/25 focus-visible:ring-offset-2 focus-visible:ring-offset-white [&::-webkit-details-marker]:hidden @if ($accountOpen) border-gray-900/30 bg-gray-100 ring-2 ring-gray-900/10 @else border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50 @endif"
            aria-label="Account and settings"
        >
            <span class="relative flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-gray-600">
                <x-lucide name="user" class="h-4 w-4" />
                @if (auth()->user()->isSuperAdmin())
                    <span
                        class="absolute -bottom-1 -right-1 flex h-3.5 min-w-3.5 items-center justify-center rounded-full border border-white bg-violet-600 px-px text-[7px] font-bold leading-none text-white"
                        title="Super admin"
                    >S</span>
                @elseif (auth()->user()->isAdmin())
                    <span
                        class="absolute -bottom-1 -right-1 flex h-3.5 min-w-3.5 items-center justify-center rounded-full border border-white bg-emerald-600 px-px text-[7px] font-bold leading-none text-white"
                        title="Admin"
                    >A</span>
                @endif
            </span>
        </summary>
        <div
            class="reservo-dropdown-panel absolute left-auto right-0 top-full z-[60] mt-1 min-w-[11.5rem] max-w-[min(12rem,calc(100vw-1.25rem))] overflow-hidden rounded-lg border border-gray-200 bg-white py-0.5 shadow-[0_12px_40px_-10px_rgba(15,23,42,0.22)]"
            role="menu"
        >
            <div class="border-b border-gray-100 px-2.5 py-1.5">
                <p class="truncate text-xs font-semibold leading-tight text-gray-900">{{ auth()->user()->name }}</p>
                <p class="mt-0.5 truncate text-[11px] leading-tight text-gray-500">{{ auth()->user()->email }}</p>
            </div>
            <a
                href="{{ route('settings.index') }}"
                class="{{ request()->routeIs('settings.index') ? $itemClassDeskOn : $itemClassDesk }}"
                role="menuitem"
            >
                <x-lucide name="settings" :class="$iconDesktop" />
                Settings
            </a>
            <a
                href="{{ route('profile.edit') }}"
                class="{{ request()->routeIs('profile.*') ? $itemClassDeskOn : $itemClassDesk }}"
                role="menuitem"
            >
                <x-lucide name="user" :class="$iconDesktop" />
                Profile
            </a>
            <a
                href="{{ route('security.edit') }}"
                class="{{ request()->routeIs('security.*') ? $itemClassDeskOn : $itemClassDesk }}"
                role="menuitem"
            >
                <x-lucide name="lock" :class="$iconDesktop" />
                Security
            </a>
        </div>
    </details>
@endif

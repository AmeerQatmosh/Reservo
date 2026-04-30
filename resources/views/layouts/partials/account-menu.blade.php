{{-- Expects: $variant: 'desktop'|'mobile' --}}
@php
    $isMobile = ($variant ?? 'desktop') === 'mobile';
    $sidebarRail = ! empty($sidebarRail) && ! $isMobile;
    $iconDesktop = 'h-4 w-4 shrink-0 text-gray-500 transition-colors group-hover:text-gray-800 group-focus-visible:text-gray-800';
    $accountOpen = request()->routeIs('settings.index', 'profile.*', 'security.*');

    // Dropdown items: one style only (no route bg — looked like stuck focus). Sidebar shows current section.
    $itemClass = 'group box-border flex w-full min-w-0 items-center gap-2.5 rounded-lg px-2.5 py-1.5 text-left text-[15px] font-medium text-gray-900 transition hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-gray-900/20';

    $iconMobile = 'h-[18px] w-[18px] shrink-0 text-gray-500 transition-colors group-hover:text-gray-800 group-focus-visible:text-gray-800';
    $itemClassMobile = 'group box-border flex w-full min-w-0 items-center gap-2.5 rounded-xl px-2.5 py-2.5 text-left text-[15px] font-medium text-slate-900 transition hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-slate-900/15';
    $itemClassMobileOn = 'bg-slate-900 text-white ring-1 ring-slate-900/20 hover:bg-slate-800 focus-visible:ring-white/25';
    $itemClassMobileOff = 'text-slate-800';

    // No focus-visible:bg-* so a focused row does not look selected when reopening the menu.
    $itemClassDesk = 'group box-border flex w-full min-w-0 items-center gap-2 rounded-md px-2.5 py-1.5 text-sm text-gray-700 transition hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-gray-900/18';
@endphp

@if ($isMobile)
    <section
        class="app-account-menu flex flex-col gap-4"
        aria-label="Account and settings"
    >
        <div
            class="flex min-w-0 items-start gap-3 rounded-2xl border border-slate-200/90 bg-slate-50/80 px-3 py-3 ring-1 ring-slate-900/[0.04]"
        >
            <span class="relative shrink-0">
                <span
                    class="flex h-12 w-12 items-center justify-center rounded-full border border-slate-200/90 bg-white text-slate-600 shadow-sm"
                >
                    <x-lucide name="user" class="h-6 w-6" />
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
            <div class="min-w-0 flex-1 pt-0.5">
                <p
                    class="text-[0.7rem] font-semibold uppercase tracking-[0.12em] text-slate-500"
                >
                    Account
                </p>
                <p class="mt-0.5 truncate text-sm font-semibold leading-tight text-slate-900">
                    {{ auth()->user()->name }}
                </p>
                <p class="mt-0.5 truncate text-xs text-slate-500">
                    {{ auth()->user()->email }}
                </p>
            </div>
        </div>
        <div class="flex flex-col gap-1.5">
            <a
                href="{{ route('profile.edit') }}"
                class="{{ $itemClassMobile.' '.(request()->routeIs('profile.*') ? $itemClassMobileOn : $itemClassMobileOff) }}"
                @if (request()->routeIs('profile.*')) aria-current="page" @endif
            >
                <x-lucide
                    name="user"
                    :class="request()->routeIs('profile.*') ? 'h-[18px] w-[18px] shrink-0 text-white' : $iconMobile"
                />
                Profile
            </a>
            <a
                href="{{ route('settings.index') }}"
                class="{{ $itemClassMobile.' '.(request()->routeIs('settings.index') ? $itemClassMobileOn : $itemClassMobileOff) }}"
                @if (request()->routeIs('settings.index')) aria-current="page" @endif
            >
                <x-lucide
                    name="settings"
                    :class="request()->routeIs('settings.index') ? 'h-[18px] w-[18px] shrink-0 text-white' : $iconMobile"
                />
                Settings
            </a>
            <a
                href="{{ route('security.edit') }}"
                class="{{ $itemClassMobile.' '.(request()->routeIs('security.*') ? $itemClassMobileOn : $itemClassMobileOff) }}"
                @if (request()->routeIs('security.*')) aria-current="page" @endif
            >
                <x-lucide
                    name="lock"
                    :class="request()->routeIs('security.*') ? 'h-[18px] w-[18px] shrink-0 text-white' : $iconMobile"
                />
                Security
            </a>
        </div>
    </section>
@else
    <details @class(['app-account-menu reservo-details relative shrink-0 overflow-visible', 'z-[70] admin-sidebar-account-menu' => $sidebarRail])>
        <summary
            class="flex h-8 w-8 cursor-pointer list-none items-center justify-center overflow-visible rounded-full border shadow-sm transition outline-none focus-visible:ring-2 focus-visible:ring-gray-900/25 focus-visible:ring-offset-2 focus-visible:ring-offset-white [&::-webkit-details-marker]:hidden @if ($accountOpen) border-gray-900/30 bg-gray-100 ring-2 ring-gray-900/10 @else border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50 @endif"
            aria-label="{{ __('Account and settings') }}"
            @if ($sidebarRail) title="{{ __('Account and settings') }}" data-sidebar-tooltip="{{ __('Account and settings') }}" @endif
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
            @class([
                'reservo-dropdown-panel min-w-[11.5rem] max-w-[min(12rem,calc(100vw-1.25rem))] overflow-hidden rounded-lg border border-gray-200 bg-white px-1.5 py-1.5 shadow-[0_12px_40px_-10px_rgba(15,23,42,0.22)]',
                'reservo-sidebar-account-panel' => $sidebarRail,
                'absolute z-[60] left-auto right-0 top-full mt-1' => ! $sidebarRail,
            ])
            role="menu"
        >
            <div class="border-b border-gray-100 px-2.5 py-1.5">
                <p class="truncate text-xs font-semibold leading-tight text-gray-900">{{ auth()->user()->name }}</p>
                <p class="mt-0.5 truncate text-[11px] leading-tight text-gray-500">{{ auth()->user()->email }}</p>
            </div>
            <a
                href="{{ route('settings.index') }}"
                class="{{ $itemClassDesk }}"
                role="menuitem"
                @if (request()->routeIs('settings.index')) aria-current="page" @endif
            >
                <x-lucide name="settings" :class="$iconDesktop" />
                Settings
            </a>
            <a
                href="{{ route('profile.edit') }}"
                class="{{ $itemClassDesk }}"
                role="menuitem"
                @if (request()->routeIs('profile.*')) aria-current="page" @endif
            >
                <x-lucide name="user" :class="$iconDesktop" />
                Profile
            </a>
            <a
                href="{{ route('security.edit') }}"
                class="{{ $itemClassDesk }}"
                role="menuitem"
                @if (request()->routeIs('security.*')) aria-current="page" @endif
            >
                <x-lucide name="lock" :class="$iconDesktop" />
                Security
            </a>

            <div class="mt-1 border-t border-gray-100 pt-1">
                <form method="POST" action="{{route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="group flex w-full min-w-0 items-center gap-2 rounded-lg cursor-pointer px-2.5 py-1.5 text-left text-[15px] font-medium text-yellow-500 transition hover:bg-yellow-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-red-200"
                        data-no-loading
                        >
                        <x-lucide name="log-out" class="h-[18px] w-[18px] shrink-0 text-yellow-500" />
                        Sign out 
                    </button>
                </form> 
            </div>
        </div>
    </details>
@endif

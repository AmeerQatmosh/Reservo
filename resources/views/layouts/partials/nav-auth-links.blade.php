{{-- Expects: $navLinkClass, $navActiveClass, $navInactiveClass, $variant: 'desktop'|'mobile' --}}
@php
    $isMobile = ($variant ?? 'desktop') === 'mobile';
    $iconMobile = 'h-[18px] w-[18px] shrink-0 text-gray-600 transition-colors group-hover:text-gray-900 group-focus-visible:text-gray-900';
    $iconMobileOn = 'h-[18px] w-[18px] shrink-0 text-white/90 transition-colors group-hover:text-white group-focus-visible:text-white';
    $iconMobileLight = 'h-[18px] w-[18px] shrink-0 text-white/90 transition-opacity group-hover:opacity-100';
    $iconDesktop = 'h-4 w-4 shrink-0 opacity-90';
    $m = 'group flex w-full items-center gap-2.5 rounded-md px-2.5 py-2 text-[15px] font-medium transition outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-gray-900/15';
    $mOn = $m.' bg-gray-900 text-white focus-visible:ring-white/25';
    $mOff = $m.' text-gray-900 hover:bg-gray-100';
@endphp

@auth
    @if ($isMobile)
        {{-- Account block lives in `layouts.app` mobile tray (first); this section is sign out only. --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="group flex w-full items-center gap-2.5 cursor-pointer rounded-xl px-2.5 py-2.5 text-left text-[15px] font-medium text-yellow-600 transition hover:bg-yellow-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-yellow-200" data-no-loading>
                <x-lucide name="log-out" class="h-[18px] w-[18px] shrink-0 text-yellow-600 transition-colors group-hover:text-yellow-600 group-focus-visible:text-yellow-600" />
                Sign out
            </button>
        </form>
    @else
        @include('layouts.partials.account-menu', ['variant' => 'desktop'])

        <!-- <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 whitespace-nowrap rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:border-gray-300 hover:bg-gray-50" data-no-loading>
                <x-lucide name="log-out" :class="$iconDesktop" />
                Logout
            </button>
        </form> -->
    @endif
@else
    @if ($isMobile)
        <div class="flex flex-col gap-2">
            <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? $mOn : $mOff }}">
                <x-lucide name="log-in" :class="request()->routeIs('login') ? $iconMobileOn : $iconMobile" />
                Log in
            </a>
            <a href="{{ route('register') }}" class="group flex w-full items-center justify-center gap-2 rounded-md bg-gray-900 px-2.5 py-2 text-[15px] font-semibold text-white transition hover:bg-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-white/30">
                <x-lucide name="user-plus" :class="$iconMobileLight" />
                Create account
            </a>
        </div>
    @else
        <a href="{{ route('login') }}" class="{{ $navLinkClass }} {{ request()->routeIs('login') ? $navActiveClass : $navInactiveClass }}">
            <x-lucide name="log-in" :class="$iconDesktop" />
            Login
        </a>
        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 whitespace-nowrap rounded-xl bg-sky-200 px-4 py-2 text-sm font-medium text-sky-600 transition hover:bg-sky-300">
            <x-lucide name="user-plus" :class="$iconDesktop" />
            Register
        </a>
    @endif
@endauth

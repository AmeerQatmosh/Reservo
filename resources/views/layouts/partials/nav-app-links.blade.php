{{-- Expects: $navLinkClass, $navActiveClass, $navInactiveClass, $variant: 'desktop'|'mobile' --}}
@php
    $isMobile = ($variant ?? 'desktop') === 'mobile';
    $iconMobile = 'h-[18px] w-[18px] shrink-0 text-gray-600 transition-colors group-hover:text-gray-900 group-focus-visible:text-gray-900';
    $iconMobileOn = 'h-[18px] w-[18px] shrink-0 text-white/90 transition-colors group-hover:text-white group-focus-visible:text-white';
    $iconDesktop = 'h-4 w-4 shrink-0 opacity-90';
    $m = 'group flex w-full items-center gap-2.5 rounded-md px-2.5 py-2 text-[15px] font-medium transition outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-gray-900/15';
    $mOn = $m.' bg-gray-900 text-white focus-visible:ring-white/25';
    $mOff = $m.' text-gray-900 hover:bg-gray-100';
@endphp

@if ($isMobile)
    @auth
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? $mOn : $mOff }}">
            <x-lucide name="layout-grid" :class="request()->routeIs('dashboard') ? $iconMobileOn : $iconMobile" />
            Dashboard
        </a>
    @endauth
    <a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('rooms.index', 'rooms.show') ? $mOn : $mOff }}">
        <x-lucide name="door-open" :class="request()->routeIs('rooms.index', 'rooms.show') ? $iconMobileOn : $iconMobile" />
        Rooms
    </a>
    @guest
        @if (config('reservo.demo_enabled'))
            <a href="{{ route('demo.index') }}" class="{{ request()->routeIs('demo.*') ? $mOn : $mOff }}">
                <x-lucide name="calendar-plus" :class="request()->routeIs('demo.*') ? $iconMobileOn : $iconMobile" />
                Free room
            </a>
        @endif
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? $mOn : $mOff }}">
            <x-lucide name="house" :class="request()->routeIs('home') ? $iconMobileOn : $iconMobile" />
            Home
        </a>
    @endguest
    @auth
        <a href="{{ route('reservations.my') }}" class="{{ request()->routeIs('reservations.my', 'reservations.edit') ? $mOn : $mOff }}">
            <x-lucide name="calendar-days" :class="request()->routeIs('reservations.my', 'reservations.edit') ? $iconMobileOn : $iconMobile" />
            My reservations
        </a>
        @if (auth()->user()->isAdmin())
            <p class="flex items-center gap-1.5 px-2.5 pb-0.5 pt-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400">
                <x-lucide name="shield" class="h-3.5 w-3.5 shrink-0" />
                Admin
            </p>
            <a href="{{ route('admin.rooms.index') }}" class="{{ request()->routeIs('admin.rooms.*') ? $mOn : $mOff }}">
                <x-lucide name="building-2" :class="request()->routeIs('admin.rooms.*') ? $iconMobileOn : $iconMobile" />
                Manage rooms
            </a>
            <a href="{{ route('admin.reservations.index') }}" class="{{ request()->routeIs('admin.reservations.*') ? $mOn : $mOff }}">
                <x-lucide name="clipboard-list" :class="request()->routeIs('admin.reservations.*') ? $iconMobileOn : $iconMobile" />
                All reservations
            </a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? $mOn : $mOff }}">
                <x-lucide name="users" :class="request()->routeIs('admin.users.*') ? $iconMobileOn : $iconMobile" />
                Users
            </a>
        @endif
    @endauth
@else
    @php
        $linkClass = $navLinkClass;
    @endphp
    @auth
        <a href="{{ route('dashboard') }}" class="{{ $linkClass }} {{ request()->routeIs('dashboard') ? $navActiveClass : $navInactiveClass }}">
            <x-lucide name="layout-grid" :class="$iconDesktop" />
            Dashboard
        </a>
    @endauth
    @guest
        <a href="{{ route('home') }}" class="{{ $linkClass }} {{ request()->routeIs('home') ? $navActiveClass : $navInactiveClass }}">
            <x-lucide name="house" :class="$iconDesktop" />
            Home
        </a>
    @endguest

    <a href="{{ route('rooms.index') }}" class="{{ $linkClass }} {{ request()->routeIs('rooms.index', 'rooms.show') ? $navActiveClass : $navInactiveClass }}">
        <x-lucide name="door-open" :class="$iconDesktop" />
        Rooms
    </a>

    @guest
        @if (config('reservo.demo_enabled'))
            <a href="{{ route('demo.index') }}" class="{{ $linkClass }} {{ request()->routeIs('demo.*') ? $navActiveClass : $navInactiveClass }}">
                <x-lucide name="calendar-plus" :class="$iconDesktop" />
                Free room
            </a>
        @endif
    @endguest

    @auth
        <a href="{{ route('reservations.my') }}" class="{{ $linkClass }} {{ request()->routeIs('reservations.my', 'reservations.edit') ? $navActiveClass : $navInactiveClass }}">
            <x-lucide name="calendar-days" :class="$iconDesktop" />
            My Reservations
        </a>

        @if (auth()->user()->isAdmin())
            <a href="{{ route('admin.rooms.index') }}" class="{{ $linkClass }} {{ request()->routeIs('admin.rooms.*') ? $navActiveClass : $navInactiveClass }}">
                <x-lucide name="building-2" :class="$iconDesktop" />
                Admin Rooms
            </a>
            <a href="{{ route('admin.reservations.index') }}" class="{{ $linkClass }} {{ request()->routeIs('admin.reservations.*') ? $navActiveClass : $navInactiveClass }}">
                <x-lucide name="clipboard-list" :class="$iconDesktop" />
                Admin Reservations
            </a>
            <a href="{{ route('admin.users.index') }}" class="{{ $linkClass }} {{ request()->routeIs('admin.users.*') ? $navActiveClass : $navInactiveClass }}">
                <x-lucide name="users" :class="$iconDesktop" />
                Admin Users
            </a>
        @endif
    @endauth
@endif

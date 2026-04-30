{{-- Expects: $navLinkClass, $navActiveClass, $navInactiveClass, $variant: 'desktop'|'mobile'|'sidebar' --}}
@php
    $variant = $variant ?? 'desktop';
    $isMobile = $variant === 'mobile';
    $isSidebar = $variant === 'sidebar';
    $useStackedNav = $isMobile || $isSidebar;
    $iconMobile = 'h-[18px] w-[18px] shrink-0 text-gray-600 transition-colors group-hover:text-gray-900 group-focus-visible:text-gray-900';
    $iconMobileOn = 'h-[18px] w-[18px] shrink-0 text-white/90 transition-colors group-hover:text-white group-focus-visible:text-white';
    $iconMobileGuest = 'h-[18px] w-[18px] shrink-0 text-amber-700 transition-colors group-hover:text-amber-900 group-focus-visible:text-amber-900';
    $iconMobileGuestOn = 'h-[18px] w-[18px] shrink-0 text-white';
    $iconDesktop = 'h-4 w-4 shrink-0 opacity-90';
    $iconDesktopGuest = 'h-4 w-4 shrink-0 text-amber-400 hover:text-amber-500';
    $iconDesktopGuestActive = 'h-4 w-4 shrink-0 text-white';
    $m = 'group flex w-full items-center gap-2.5 rounded-md px-2.5 py-2 text-[15px] font-medium transition outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-gray-900/15';
    $mOn = $m.' bg-gray-900 text-white focus-visible:ring-white/25';
    $mOff = $m.' text-gray-900 hover:bg-gray-100';
    $mGuest = $m.' border border-amber-400/60 bg-gradient-to-br from-amber-50 to-orange-50 text-amber-950 shadow-sm ring-1 ring-amber-500/15 hover:border-amber-500 hover:from-amber-100 hover:to-amber-50 hover:ring-amber-500/25';
    $mGuestOn = $m.' border border-amber-600 bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-md ring-2 ring-amber-500/35 focus-visible:ring-white/30';
    $guestModeDesktop = $navLinkClass.' text-amber-400 hover:text-amber-500';
    $guestModeDesktopActive = $navLinkClass.'border border-yellow-500 bg-yellow-400 rounded-xl text-white';
@endphp

@if ($useStackedNav)
    @auth
        @php
            $navHomeLabel = auth()->user()->isAdmin() ? __('Dashboard') : __('Home');
        @endphp
        <a
            href="{{ route('dashboard') }}"
            title="{{ $navHomeLabel }}"
            @if ($isSidebar) data-sidebar-tooltip="{{ $navHomeLabel }}" @endif
            class="{{ request()->routeIs('dashboard') ? $mOn : $mOff }}"
        >
            <x-lucide
                :name="auth()->user()->isAdmin() ? 'layout-grid' : 'house'"
                :class="request()->routeIs('dashboard') ? $iconMobileOn : $iconMobile"
            />
            @if ($isSidebar)
                <span class="admin-sidebar-label min-w-0 truncate">{{ $navHomeLabel }}</span>
            @else
                {{ $navHomeLabel }}
            @endif
        </a>
    @endauth
    @guest
        <a
            href="{{ route('home') }}"
            title="{{ __('Home') }}"
            @if ($isSidebar) data-sidebar-tooltip="{{ __('Home') }}" @endif
            class="{{ request()->routeIs('home') ? $mOn : $mOff }}"
        >
            <x-lucide name="house" :class="request()->routeIs('home') ? $iconMobileOn : $iconMobile" />
            @if ($isSidebar)
                <span class="admin-sidebar-label min-w-0 truncate">{{ __('Home') }}</span>
            @else
                Home
            @endif
        </a>
    @endguest
    <a
        href="{{ route('rooms.index') }}"
        title="{{ __('Rooms') }}"
        @if ($isSidebar) data-sidebar-tooltip="{{ __('Rooms') }}" @endif
        class="{{ request()->routeIs('rooms.index', 'rooms.show') ? $mOn : $mOff }}"
    >
        <x-lucide name="door-open" :class="request()->routeIs('rooms.index', 'rooms.show') ? $iconMobileOn : $iconMobile" />
        @if ($isSidebar)
            <span class="admin-sidebar-label min-w-0 truncate">{{ __('Rooms') }}</span>
        @else
            Rooms
        @endif
    </a>
    @guest
        @if (config('reservo.demo_enabled'))
            <a
                href="{{ route('demo.index') }}"
                title="{{ __('Guest Mode') }}"
                @if ($isSidebar) data-sidebar-tooltip="{{ __('Guest Mode') }}" @endif
                class="{{ request()->routeIs('demo.*') ? $mGuestOn : $mGuest }}"
            >
                <x-lucide name="calendar-plus" :class="request()->routeIs('demo.*') ? $iconMobileGuestOn : $iconMobileGuest" />
                @if ($isSidebar)
                    <span class="admin-sidebar-label min-w-0 truncate">{{ __('Guest Mode') }}</span>
                @else
                    Guest Mode
                @endif
            </a>
        @endif
    @endguest
    @auth
        <a
            href="{{ route('reservations.my') }}"
            title="{{ __('My reservations') }}"
            @if ($isSidebar) data-sidebar-tooltip="{{ __('My reservations') }}" @endif
            class="{{ request()->routeIs('reservations.my', 'reservations.edit') ? $mOn : $mOff }}"
        >
            <x-lucide name="calendar-days" :class="request()->routeIs('reservations.my', 'reservations.edit') ? $iconMobileOn : $iconMobile" />
            @if ($isSidebar)
                <span class="admin-sidebar-label min-w-0 truncate">{{ __('My reservations') }}</span>
            @else
                My reservations
            @endif
        </a>
        @if (auth()->user()->isAdmin())
            <a
                href="{{ route('admin.rooms.index') }}"
                title="{{ __('Manage rooms') }}"
                @if ($isSidebar) data-sidebar-tooltip="{{ __('Manage rooms') }}" @endif
                class="{{ request()->routeIs('admin.rooms.*') ? $mOn : $mOff }}"
            >
                <x-lucide name="building-2" :class="request()->routeIs('admin.rooms.*') ? $iconMobileOn : $iconMobile" />
                @if ($isSidebar)
                    <span class="admin-sidebar-label min-w-0 truncate">{{ __('Manage rooms') }}</span>
                @else
                    Manage rooms
                @endif
            </a>
            <a
                href="{{ route('admin.reservations.index') }}"
                title="{{ __('All reservations') }}"
                @if ($isSidebar) data-sidebar-tooltip="{{ __('All reservations') }}" @endif
                class="{{ request()->routeIs('admin.reservations.*') ? $mOn : $mOff }}"
            >
                <x-lucide name="clipboard-list" :class="request()->routeIs('admin.reservations.*') ? $iconMobileOn : $iconMobile" />
                @if ($isSidebar)
                    <span class="admin-sidebar-label min-w-0 truncate">{{ __('All reservations') }}</span>
                @else
                    All reservations
                @endif
            </a>
            <a
                href="{{ route('admin.users.index') }}"
                title="{{ __('Users') }}"
                @if ($isSidebar) data-sidebar-tooltip="{{ __('Users') }}" @endif
                class="{{ request()->routeIs('admin.users.*') ? $mOn : $mOff }}"
            >
                <x-lucide name="users" :class="request()->routeIs('admin.users.*') ? $iconMobileOn : $iconMobile" />
                @if ($isSidebar)
                    <span class="admin-sidebar-label min-w-0 truncate">{{ __('Users') }}</span>
                @else
                    Users
                @endif
            </a>
        @endif
    @endauth
@else
    @php
        $linkClass = $navLinkClass;
    @endphp
    @auth
        @php
            $navHomeLabelDesktop = auth()->user()->isAdmin() ? __('Dashboard') : __('Home');
        @endphp
        <a href="{{ route('dashboard') }}" class="{{ $linkClass }} {{ request()->routeIs('dashboard') ? $navActiveClass : $navInactiveClass }}">
            <x-lucide :name="auth()->user()->isAdmin() ? 'layout-grid' : 'house'" :class="$iconDesktop" />
            {{ $navHomeLabelDesktop }}
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
            <a
                href="{{ route('demo.index') }}"
                class="{{ request()->routeIs('demo.*') ? $guestModeDesktopActive : $guestModeDesktop }}"
            >
                <x-lucide
                    name="calendar-plus"
                    :class="request()->routeIs('demo.*') ? $iconDesktopGuestActive : $iconDesktopGuest"
                />
                Guest Mode
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

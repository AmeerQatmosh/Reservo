{{--
    Compact demo strip below main nav: label, core links, admin menu, role, exit.
--}}
@php
    $role = \App\Support\DemoState::role();
    $adminSectionActive = request()->routeIs(
        'demo.admin.rooms',
        'demo.admin.rooms.create',
        'demo.admin.rooms.show',
        'demo.admin.reservations',
        'demo.admin.users',
    );

    $pill = 'inline-flex shrink-0 items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium transition sm:px-3 sm:text-sm';
    $pillOff = $pill.' border border-amber-900/12 bg-white/80 text-amber-950 hover:border-amber-900/25 hover:bg-white';
    $pillOn = $pill.' border border-amber-900 bg-amber-900 text-white shadow-sm';
@endphp

<div class="border-b border-amber-200/60 bg-amber-50/90 backdrop-blur-sm">
    <div class="mx-auto flex max-w-7xl flex-col gap-2 px-3 py-2 sm:px-4 lg:flex-row lg:items-center lg:justify-between lg:gap-3 lg:px-8 lg:py-2">
        <div class="flex min-w-0 items-center">
            <span class="rounded-md bg-amber-900/10 px-2 py-1 text-xs font-bold uppercase tracking-wide text-amber-950 sm:text-sm">
                Demo
            </span>
        </div>

        <div class="flex min-w-0 flex-1 flex-wrap items-center gap-1.5 sm:gap-2 lg:justify-center">
            <a href="{{ route('demo.hub') }}" class="{{ request()->routeIs('demo.hub') ? $pillOn : $pillOff }}">
                <x-lucide name="layout-grid" class="h-3.5 w-3.5 opacity-90" />
                Home
            </a>
            <a href="{{ route('demo.rooms') }}" class="{{ request()->routeIs('demo.rooms', 'demo.room.show') ? $pillOn : $pillOff }}">
                <x-lucide name="door-open" class="h-3.5 w-3.5 opacity-90" />
                Rooms
            </a>
            @if (\App\Support\DemoState::canUser())
                <a href="{{ route('demo.reservations.my') }}" class="{{ request()->routeIs('demo.reservations.my') ? $pillOn : $pillOff }}">
                    <x-lucide name="calendar-days" class="h-3.5 w-3.5 opacity-90" />
                    Bookings
                </a>
            @endif

            @if (\App\Support\DemoState::canAdmin())
                <details class="relative shrink-0">
                    <summary
                        class="{{ $adminSectionActive ? $pillOn : $pillOff }} cursor-pointer list-none [&::-webkit-details-marker]:hidden"
                    >
                        <x-lucide name="shield" class="h-3.5 w-3.5 opacity-90" />
                        Admin
                        <x-lucide name="chevron-down" class="h-3.5 w-3.5 opacity-60" />
                    </summary>
                    <div
                        class="absolute left-0 top-[calc(100%+0.25rem)] z-50 min-w-[11rem] rounded-xl border border-amber-200/80 bg-white py-1 shadow-lg ring-1 ring-black/5 sm:left-auto sm:right-0 lg:left-0 lg:right-auto"
                    >
                        <a
                            href="{{ route('demo.admin.rooms') }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm text-gray-800 hover:bg-amber-50 {{ request()->routeIs('demo.admin.rooms') ? 'bg-amber-50 font-medium' : '' }}"
                        >
                            <x-lucide name="building-2" class="h-4 w-4 text-gray-500" />
                            All rooms
                        </a>
                        <a
                            href="{{ route('demo.admin.rooms.create') }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm text-gray-800 hover:bg-amber-50 {{ request()->routeIs('demo.admin.rooms.create') ? 'bg-amber-50 font-medium' : '' }}"
                        >
                            <x-lucide name="plus" class="h-4 w-4 text-gray-500" />
                            Add room
                        </a>
                        <a
                            href="{{ route('demo.admin.reservations') }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm text-gray-800 hover:bg-amber-50 {{ request()->routeIs('demo.admin.reservations') ? 'bg-amber-50 font-medium' : '' }}"
                        >
                            <x-lucide name="clipboard-list" class="h-4 w-4 text-gray-500" />
                            Reservations
                        </a>
                        <a
                            href="{{ route('demo.admin.users') }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm text-gray-800 hover:bg-amber-50 {{ request()->routeIs('demo.admin.users') ? 'bg-amber-50 font-medium' : '' }}"
                        >
                            <x-lucide name="users" class="h-4 w-4 text-gray-500" />
                            Users
                        </a>
                    </div>
                </details>
            @endif
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2 sm:gap-3">
            <form method="POST" action="{{ route('demo.role') }}" class="flex min-w-0 items-center">
                @csrf
                <label for="demo_sandbox_role" class="sr-only">Preview as role</label>
                <select
                    id="demo_sandbox_role"
                    name="role"
                    class="w-full min-w-0 max-w-full rounded-lg border border-amber-900/15 bg-white py-1.5 pl-2.5 pr-8 text-xs font-medium text-amber-950 shadow-sm focus:border-amber-900/40 focus:outline-none focus:ring-2 focus:ring-amber-900/15 sm:w-auto sm:max-w-[14rem] sm:text-sm"
                    onchange="this.form.submit()"
                >
                    <option value="user" @selected($role === 'user')>As user</option>
                    <option value="admin" @selected($role === 'admin')>As admin</option>
                    <option value="super_admin" @selected($role === 'super_admin')>As super admin</option>
                </select>
            </form>
            <a
                href="{{ route('demo.exit') }}"
                class="shrink-0 rounded-lg border border-amber-900/15 bg-white px-3 py-1.5 text-center text-xs font-semibold text-amber-900 shadow-sm transition hover:border-amber-900/30 hover:bg-amber-50 sm:text-sm"
            >
                Exit Sandbox
            </a>
        </div>
    </div>
</div>

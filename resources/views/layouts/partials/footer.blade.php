@php
    $f = 'text-sm text-slate-600 transition hover:text-slate-900 hover:underline';
    $fh = 'text-xs font-semibold uppercase tracking-wider text-slate-400';
    $footerGrid = 'grid min-w-0 flex-1 grid-cols-1 gap-8 sm:grid-cols-2';
    if (auth()->check() && auth()->user()->isAdmin()) {
        $footerGrid .= ' lg:grid-cols-3';
    }
@endphp

<footer class="mt-auto border-t border-slate-200/80 bg-white/90 py-10 text-slate-600" aria-label="Site footer">
    <div class="mx-auto max-w-[min(100%,85rem)] px-2.5 sm:px-3.5 lg:px-6">
        <div class="flex flex-col gap-10 sm:flex-row sm:items-start sm:justify-between">
            <a
                href="{{ auth()->check() ? route('dashboard') : route('home') }}"
                class="inline-flex max-w-xs shrink-0 items-center gap-2.5 rounded-lg transition hover:opacity-90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900"
            >
                <span class="flex h-5 items-center sm:h-6" aria-hidden="true">
                    <img
                        src="{{ asset('images/reservo-logo-colored.svg') }}"
                        alt=""
                        width="277"
                        height="332"
                        class="h-full w-auto max-h-full object-contain object-left"
                        draggable="false"
                    />
                </span>
                <span class="font-brand text-lg font-bold leading-none tracking-[-0.03em] text-[#101828] sm:text-xl">
                    {{ config('app.name') }}
                </span>
            </a>

            <div class="{{ $footerGrid }} sm:max-w-3xl">
                @guest
                    <div class="min-w-0">
                        <p class="{{ $fh }}">Explore</p>
                        <ul class="mt-3 space-y-2.5" role="list">
                            <li><a href="{{ route('home') }}" class="{{ $f }}">Home</a></li>
                            <li><a href="{{ route('rooms.index') }}" class="{{ $f }}">Rooms</a></li>
                            @if (config('reservo.demo_enabled'))
                                <li><a href="{{ route('demo.index') }}" class="text-sm font-medium text-amber-800 transition hover:text-amber-950">Guest Mode</a></li>
                            @endif
                        </ul>
                    </div>
                    <div class="min-w-0">
                        <p class="{{ $fh }}">Account</p>
                        <ul class="mt-3 space-y-2.5" role="list">
                            <li><a href="{{ route('login') }}" class="{{ $f }}">Log in</a></li>
                            <li><a href="{{ route('register') }}" class="{{ $f }} font-medium text-slate-800">Register</a></li>
                        </ul>
                    </div>
                @else
                    <div class="min-w-0">
                        <p class="{{ $fh }}">App</p>
                        <ul class="mt-3 space-y-2.5" role="list">
                            <li>
                                <a href="{{ route('dashboard') }}" class="{{ $f }}">
                                    {{ auth()->user()->isAdmin() ? __('Dashboard') : __('Home') }}
                                </a>
                            </li>
                            <li><a href="{{ route('rooms.index') }}" class="{{ $f }}">Rooms</a></li>
                            <li><a href="{{ route('reservations.my') }}" class="{{ $f }}">My reservations</a></li>
                            <li><a href="{{ route('settings.index') }}" class="{{ $f }}">Settings</a></li>
                        </ul>
                    </div>
                    @if (auth()->user()->isAdmin())
                        <div class="min-w-0">
                            <p class="{{ $fh }}">Administration</p>
                            <ul class="mt-3 space-y-2.5" role="list">
                                <li><a href="{{ route('admin.rooms.index') }}" class="{{ $f }}">Manage rooms</a></li>
                                <li><a href="{{ route('admin.reservations.index') }}" class="{{ $f }}">All reservations</a></li>
                                <li><a href="{{ route('admin.users.index') }}" class="{{ $f }}">Users</a></li>
                            </ul>
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="{{ $fh }}">Session</p>
                        <ul class="mt-3 space-y-2.5" role="list">
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="{{ $f }} cursor-pointer bg-transparent p-0 text-left underline-offset-2 hover:text-yellow-500" data-no-loading>
                                        Log out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endguest
            </div>
        </div>

        <div class="mt-10 border-t border-slate-200/70 pt-6 text-center text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</footer>

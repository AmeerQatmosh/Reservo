@php
    $navBase = 'inline-flex w-full min-w-0 items-center gap-2 whitespace-nowrap rounded-full px-3 py-2 text-sm font-medium no-underline outline-none transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2';
    $navOn = 'bg-gray-900 text-white shadow-sm hover:bg-gray-800 focus-visible:outline-white/70';
    $navOff = 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus-visible:outline-gray-900';

    $links = [
        [
            'label' => 'Overview',
            'route' => 'settings.index',
            'icon' => 'layout-grid',
            'active' => request()->routeIs('settings.index'),
        ],
        [
            'label' => 'Profile',
            'route' => 'profile.edit',
            'icon' => 'user',
            'active' => request()->routeIs('profile.edit'),
        ],
        [
            'label' => 'Security',
            'route' => 'security.edit',
            'icon' => 'lock',
            'active' => request()->routeIs('security.edit'),
        ],
    ];
@endphp
<nav class="shrink-0 lg:min-w-[12rem] lg:w-52" aria-label="Account settings">
    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-gray-500">Account</p>
    <ul class="mt-3 flex flex-col gap-1">
        @foreach ($links as $link)
            @php($active = $link['active'])
            <li class="min-w-0">
                <a
                    href="{{ route($link['route']) }}"
                    @if ($active) aria-current="page" @endif
                    class="{{ $navBase }} {{ $active ? $navOn : $navOff }}"
                ><span class="flex min-w-0 flex-1 items-center gap-2"><x-lucide
                        :name="$link['icon']"
                        class="h-4 w-4 shrink-0 {{ $active ? 'text-white opacity-90' : 'text-gray-500' }}"
                    /><span class="min-w-0 truncate">{{ $link['label'] }}</span></span></a>
            </li>
        @endforeach
    </ul>
</nav>

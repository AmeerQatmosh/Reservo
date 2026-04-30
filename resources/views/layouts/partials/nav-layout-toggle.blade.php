{{-- Admin-only: one-click switch between horizontal (top) and vertical (left) nav. Optional $variant: header | mobile-drawer | sidebar --}}
@auth
    @if (auth()->user()->isAdmin())
        @php
            $navLayoutCurrent = auth()->user()->nav_layout ?? 'horizontal';
            $nextLayout = $navLayoutCurrent === 'horizontal' ? 'vertical' : 'horizontal';
            $variant = $variant ?? 'header';

            $labelToSidebar = __('Switch to left sidebar navigation');
            $labelToTop = __('Switch to top navigation');

            if ($variant === 'header') {
                $btnClass = 'flex h-8 w-8 shrink-0 cursor-pointer items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 shadow-sm transition outline-none hover:border-gray-300 hover:bg-gray-50 hover:text-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900/25 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
                $iconClass = 'h-4 w-4';
            } elseif ($variant === 'mobile-drawer') {
                $btnClass = 'flex h-10 w-10 shrink-0 cursor-pointer items-center justify-center rounded-xl border border-slate-200/90 bg-white/90 text-slate-700 shadow-sm ring-1 ring-slate-900/[0.04] backdrop-blur-sm transition [touch-action:manipulation] outline-none hover:border-slate-300/90 hover:bg-white hover:text-slate-900 focus-visible:ring-2 focus-visible:ring-slate-400/35 focus-visible:ring-offset-2 focus-visible:ring-offset-[#f8fafc] active:scale-[0.97]';
                $iconClass = 'h-5 w-5';
            } else {
                $btnClass = 'flex h-8 w-8 shrink-0 cursor-pointer items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 shadow-sm transition outline-none hover:border-gray-300 hover:bg-gray-50 hover:text-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900/25 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
                $iconClass = 'h-4 w-4';
            }
        @endphp
        <form method="POST" action="{{ route('preferences.nav-layout') }}" class="inline-flex shrink-0">
            @csrf
            @method('PUT')
            <input type="hidden" name="layout" value="{{ $nextLayout }}">
            <button
                type="submit"
                class="{{ $btnClass }}"
                title="{{ $nextLayout === 'vertical' ? $labelToSidebar : $labelToTop }}"
                aria-label="{{ $nextLayout === 'vertical' ? $labelToSidebar : $labelToTop }}"
                @if ($variant === 'sidebar') data-sidebar-tooltip="{{ $nextLayout === 'vertical' ? $labelToSidebar : $labelToTop }}" @endif
            >
                @if ($nextLayout === 'vertical')
                    <x-lucide name="panel-left" :class="$iconClass" />
                @else
                    <x-lucide name="panel-top" :class="$iconClass" />
                @endif
            </button>
        </form>
    @endif
@endauth

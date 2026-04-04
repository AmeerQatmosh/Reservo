{{-- Shown only when html.reservo-skeleton-enter (set via inline head script after internal navigation) --}}
<div
    id="reservo-page-skeleton"
    class="reservo-page-skeleton-root fixed inset-0 z-[85] flex flex-col bg-[#f8fafc] [background-image:radial-gradient(circle_at_top,rgba(15,23,42,0.04),transparent_32%),linear-gradient(to_bottom,#f8fafc,#eef2ff_42%,#f8fafc)]"
    aria-hidden="true"
>
    <div class="border-b border-white/60 bg-white/90 shadow-[0_1px_0_rgba(15,23,42,0.06)] backdrop-blur-xl supports-[backdrop-filter]:bg-white/85">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-3 py-3.5 sm:px-4 lg:px-8">
            <div class="flex items-center gap-3">
                <div class="h-8 w-28 animate-pulse rounded-lg bg-slate-200/90 sm:w-36"></div>
            </div>
            <div class="hidden flex-1 justify-center gap-2 lg:flex">
                <div class="h-9 w-16 animate-pulse rounded-full bg-slate-200/80"></div>
                <div class="h-9 w-20 animate-pulse rounded-full bg-slate-200/80"></div>
                <div class="h-9 w-24 animate-pulse rounded-full bg-slate-200/80"></div>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-9 w-20 animate-pulse rounded-full bg-slate-200/80"></div>
                <div class="h-9 w-24 animate-pulse rounded-full bg-slate-300/80"></div>
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-7xl flex-1 space-y-6 px-3 py-8 sm:px-4 lg:px-8">
        <div class="space-y-3">
            <div class="h-4 w-40 animate-pulse rounded-lg bg-slate-200/80"></div>
            <div class="h-9 w-full max-w-xl animate-pulse rounded-xl bg-slate-200/85"></div>
            <div class="h-4 max-w-2xl animate-pulse rounded-lg bg-slate-200/70"></div>
        </div>

        <div class="h-48 w-full animate-pulse rounded-3xl bg-slate-200/75 sm:h-56"></div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="h-36 animate-pulse rounded-3xl bg-slate-200/70"></div>
            <div class="h-36 animate-pulse rounded-3xl bg-slate-200/70 lg:block"></div>
            <div class="hidden h-36 animate-pulse rounded-3xl bg-slate-200/70 lg:block"></div>
        </div>

        <div class="space-y-3 pt-2">
            <div class="h-4 w-48 animate-pulse rounded-lg bg-slate-200/75"></div>
            <div class="h-32 w-full animate-pulse rounded-2xl bg-slate-200/65"></div>
        </div>
    </div>
</div>

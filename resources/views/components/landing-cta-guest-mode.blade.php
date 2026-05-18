@props([
    'enabled' => false,
])

@if ($enabled)
    <section
        class="relative overflow-hidden rounded-3xl border border-amber-200/35 bg-gradient-to-br from-amber-950/90 via-slate-900 to-slate-950 px-6 py-9 text-center text-amber-50 shadow-[0_24px_48px_-14px_rgba(120,53,15,0.45)] sm:px-10 sm:py-10"
        data-reservo-reveal
        aria-labelledby="reservo-landing-guest-mode-heading"
    >
        <div class="pointer-events-none absolute -right-14 -top-16 h-56 w-56 rounded-full bg-amber-400/[0.12]"></div>
        <div class="pointer-events-none absolute -bottom-10 -left-8 h-44 w-44 rounded-full bg-orange-500/[0.08]"></div>
        <div class="relative">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-amber-200/80">Guest sandbox</p>
            <h2 id="reservo-landing-guest-mode-heading" class="mt-2 text-xl font-semibold tracking-tight text-white sm:text-2xl">
                Try Guest Mode
            </h2>
            <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-amber-100/85 sm:text-[0.9375rem]">
                Click through rooms and reservations with sample data — no email or password required. Perfect for a quick look before you sign up.
            </p>
            <div class="mt-7">
                <a
                    href="{{ route('demo.index') }}"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-amber-200/90 bg-amber-400/25 px-7 py-3.5 text-sm font-semibold text-amber-50 shadow-lg shadow-amber-950/30 transition duration-200 hover:border-amber-100 hover:bg-amber-400/35 hover:shadow-xl active:scale-[0.98] sm:w-auto"
                >
                    <x-lucide name="door-open" class="h-4 w-4 shrink-0" />
                    Open Guest Mode
                </a>
            </div>
        </div>
    </section>
@endif

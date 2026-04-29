<section
    class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 px-6 py-9 text-center text-white shadow-[0_28px_56px_-16px_rgba(15,23,42,0.65)] sm:px-10 sm:py-10"
    data-reservo-reveal
    aria-labelledby="reservo-landing-join-heading"
>
    <div class="pointer-events-none absolute -right-20 -top-20 h-56 w-56 rounded-full bg-indigo-500/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-16 -left-16 h-48 w-48 rounded-full bg-cyan-400/10 blur-3xl"></div>
    <div class="relative">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-indigo-200/75">Your account</p>
        <h2 id="reservo-landing-join-heading" class="mt-2 text-xl font-semibold tracking-tight sm:text-2xl">
            Join Reservo
        </h2>
        <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-indigo-100/85 sm:text-[0.9375rem]">
            Create an account to book real rooms and keep your reservations synced. Already have one? Sign in anytime.
        </p>
        <div class="mt-7 flex flex-col items-stretch gap-3 sm:flex-row sm:flex-wrap sm:justify-center sm:gap-4">
            <a
                href="{{ route('register') }}"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-7 py-3.5 text-sm font-semibold text-slate-900 shadow-lg transition duration-200 hover:bg-indigo-50 hover:shadow-xl active:scale-[0.98]"
            >
                <x-lucide name="user-plus" class="h-4 w-4 shrink-0" />
                Create account
            </a>
            <a
                href="{{ route('login') }}"
                class="inline-flex items-center justify-center rounded-2xl border border-white/25 bg-white/5 px-7 py-3.5 text-sm font-semibold text-white transition duration-200 hover:border-white/40 hover:bg-white/10 active:scale-[0.98]"
            >
                Sign in
            </a>
        </div>
    </div>
</section>

@extends('layouts.app')

@section('title', 'Try Reservo')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Guest sandbox</div>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900 sm:text-4xl">Try the full booking experience</h1>
        <p class="mt-4 text-base leading-relaxed text-gray-600">
            Walk through the same screens as a signed-in user: room catalog, availability, <span class="font-medium text-gray-800">My reservations</span> with time slots and overlap rules—no account required. Everything stays in this browser session until you leave.
        </p>

        <div class="mt-10 grid gap-4 sm:grid-cols-3">
            <form method="POST" action="{{ route('demo.start') }}" class="flex flex-col rounded-3xl border border-white/70 bg-white/90 p-6 shadow-sm transition hover:border-gray-200 hover:shadow-md">
                @csrf
                <input type="hidden" name="role" value="user">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gray-900 text-white">
                    <x-lucide name="door-open" class="h-5 w-5 opacity-90" />
                </div>
                <h2 class="mt-4 text-base font-semibold text-gray-900">User</h2>
                <p class="mt-2 flex-1 text-sm text-gray-600">Rooms, room details, and My reservations—create and cancel sandbox bookings.</p>
                <button type="submit" class="mt-6 w-full rounded-2xl bg-gray-900 px-4 py-3 text-sm font-medium text-white transition hover:bg-gray-800">Start as user</button>
            </form>
            <form method="POST" action="{{ route('demo.start') }}" class="flex flex-col rounded-3xl border border-white/70 bg-white/90 p-6 shadow-sm transition hover:border-gray-200 hover:shadow-md">
                @csrf
                <input type="hidden" name="role" value="admin">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gray-900 text-white">
                    <x-lucide name="building-2" class="h-5 w-5 opacity-90" />
                </div>
                <h2 class="mt-4 text-base font-semibold text-gray-900">Admin</h2>
                <p class="mt-2 flex-1 text-sm text-gray-600">Everything users get, plus admin tables for rooms and all reservations.</p>
                <button type="submit" class="mt-6 w-full rounded-2xl bg-gray-900 px-4 py-3 text-sm font-medium text-white transition hover:bg-gray-800">Start as admin</button>
            </form>
            <form method="POST" action="{{ route('demo.start') }}" class="flex flex-col rounded-3xl border border-white/70 bg-white/90 p-6 shadow-sm transition hover:border-gray-200 hover:shadow-md">
                @csrf
                <input type="hidden" name="role" value="super_admin">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gray-900 text-white">
                    <x-lucide name="shield" class="h-5 w-5 opacity-90" />
                </div>
                <h2 class="mt-4 text-base font-semibold text-gray-900">Super admin</h2>
                <p class="mt-2 flex-1 text-sm text-gray-600">Same as admin; switch roles anytime from the sandbox dashboard.</p>
                <button type="submit" class="mt-6 w-full rounded-2xl bg-gray-900 px-4 py-3 text-sm font-medium text-white transition hover:bg-gray-800">Start as super admin</button>
            </form>
        </div>
    </div>
@endsection

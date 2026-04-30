@extends('settings.layout')

@section('title', 'Account')

@section('settings_header')
    <h1 class="text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">Your account</h1>
@endsection

@section('settings_body_class', 'mt-8 grid gap-4 sm:grid-cols-2')

@section('settings_body')
    <a
        href="{{ route('profile.edit') }}"
        class="group rounded-2xl border border-white/70 bg-white/90 p-6 shadow-sm transition hover:border-gray-200 hover:shadow-md sm:p-7"
    >
        <div class="flex items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-700">
                <x-lucide name="user" class="h-5 w-5" />
            </span>
            <div class="min-w-0">
                <h2 class="text-base font-semibold text-gray-900">Profile</h2>
                <p class="mt-2 text-sm leading-relaxed text-gray-600">
                    Name, email, and deleting your account.
                </p>
                <span class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-gray-900 group-hover:gap-2">
                    Open profile
                    <x-lucide name="chevron-right" class="h-4 w-4" />
                </span>
            </div>
        </div>
    </a>

    <a
        href="{{ route('security.edit') }}"
        class="group rounded-2xl border border-white/70 bg-white/90 p-6 shadow-sm transition hover:border-gray-200 hover:shadow-md sm:p-7"
    >
        <div class="flex items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-700">
                <x-lucide name="lock" class="h-5 w-5" />
            </span>
            <div class="min-w-0">
                <h2 class="text-base font-semibold text-gray-900">Security</h2>
                <p class="mt-2 text-sm leading-relaxed text-gray-600">
                    Password and two-factor authentication when enabled.
                </p>
                <span class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-gray-900 group-hover:gap-2">
                    Open security
                    <x-lucide name="chevron-right" class="h-4 w-4" />
                </span>
            </div>
        </div>
    </a>

    <div class="rounded-2xl border border-gray-200 bg-gray-50/80 px-5 py-4 text-sm text-gray-600 sm:col-span-2">
        Signed in as <span class="font-medium text-gray-900">{{ $user->email }}</span>
    </div>
@endsection

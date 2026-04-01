@extends('settings.layout')

@section('title', 'Profile')

@section('settings_header')
    <div class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Account</div>
    <h1 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">Profile</h1>
    <p class="mt-2 text-sm text-gray-600">
        Update how you appear in Reservo and manage your sign-in email.
    </p>
@endsection

@section('settings_body')
    <section class="rounded-2xl border border-white/70 bg-white/90 p-6 shadow-sm sm:p-8">
        <h2 class="text-base font-semibold text-gray-900">Profile information</h2>
        <p class="mt-1 text-sm text-gray-600">Your name and email are used across reservations and notifications.</p>

        <form method="post" action="{{ route('profile.update') }}" class="mt-6">
            @csrf
            @method('patch')

            <div class="max-w-md space-y-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-900">Name</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $user->name) }}"
                        required
                        autocomplete="name"
                        class="app-field"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-900">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        autocomplete="username"
                        class="app-field"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button
                        type="submit"
                        class="rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900"
                        data-test="update-profile-button"
                    >
                        Save changes
                    </button>
                </div>
            </div>
        </form>

        @if ($mustVerifyEmail && is_null($user->email_verified_at))
            <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50/90 px-4 py-3 text-sm text-amber-900">
                <p>Your email address is unverified.</p>
                <form method="post" action="{{ route('verification.send') }}" class="mt-2 inline">
                    @csrf
                    <button type="submit" class="font-medium text-amber-950 underline decoration-amber-400 underline-offset-2 hover:decoration-amber-950">
                        Resend verification email
                    </button>
                </form>
                @if ($status === 'verification-link-sent')
                    <p class="mt-2 font-medium text-emerald-800">A new verification link has been sent.</p>
                @endif
            </div>
        @endif
    </section>

    <section class="rounded-2xl border border-red-200/80 bg-red-50/50 p-6 shadow-sm sm:p-8">
        <h2 class="text-base font-semibold text-red-900">Delete account</h2>
        <p class="mt-1 text-sm text-red-800/90">
            Permanently remove your account and personal data. Your reservations may become inaccessible. This cannot be undone.
        </p>

        <form
            method="post"
            action="{{ route('profile.destroy') }}"
            class="mt-6 space-y-4"
            data-confirm-message="Permanently delete your account and all associated data? This cannot be undone."
            data-confirm-button-label="Delete account"
        >
            @csrf
            @method('delete')

            <div class="max-w-md space-y-4">
                <div>
                    <label for="delete_password" class="block text-sm font-medium text-red-900">Confirm with your password</label>
                    <input
                        id="delete_password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="app-field border-red-200 focus:border-red-800 focus:ring-red-900/10"
                        placeholder="Current password"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="rounded-xl border border-red-300 bg-white px-4 py-2.5 text-sm font-medium text-red-700 shadow-sm transition hover:bg-red-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-800"
                    data-test="delete-user-button"
                >
                    Delete account
                </button>
            </div>
        </form>
    </section>
@endsection

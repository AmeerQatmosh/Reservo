@extends('settings.layout')

@section('title', 'Profile')

@section('settings_header')
    <h1 class="text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">Profile</h1>
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

    @unless ($user->isAdmin())
        @php
            $deleteFormHasErrors =
                $errors->has('identity')
                || $errors->has('password')
                || $errors->has('delete_confirmation');
        @endphp

        <section class="rounded-2xl border border-red-200/80 bg-red-50/50 p-6 shadow-sm sm:p-8">
        <h2 class="text-base font-semibold text-red-900">Delete account</h2>
        <p class="mt-1 text-sm text-red-800/90">
            Permanently remove your account and personal data. Your reservations may become inaccessible. This cannot be undone.
        </p>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <button
                type="button"
                id="profile-open-delete-account-dialog"
                class="rounded-xl border border-red-300 bg-white px-4 py-2.5 text-sm font-medium text-red-700 shadow-sm transition hover:bg-red-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-800"
                data-test="delete-user-button"
            >
                Delete account
            </button>
        </div>
    </section>

    <dialog
        id="profile-delete-account-dialog"
        class="fixed left-1/2 top-1/2 z-[95] m-4 w-[min(100%,31rem)] max-w-[calc(100vw-2rem)] -translate-x-1/2 -translate-y-1/2 rounded-2xl border border-red-100 bg-white p-0 text-gray-900 shadow-2xl [&::backdrop]:bg-black/[0.42]"
        aria-labelledby="profile-delete-account-title"
        data-show-modal-on-load="{{ $deleteFormHasErrors ? '1' : '0' }}"
    >
        <form
            id="profile-delete-account-form"
            method="post"
            action="{{ route('profile.destroy') }}"
            autocomplete="off"
            class="relative"
        >
            @csrf
            @method('delete')

            {{-- Decoy inputs: reduce browser password manager pairing with profile email/name fields --}}
            <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
                <input type="text" autocomplete="off" tabindex="-1">
                <input type="password" autocomplete="new-password" tabindex="-1">
            </div>

            <div class="border-b border-red-100 bg-red-50/80 px-5 py-4 sm:px-6 sm:py-5">
                <h3 id="profile-delete-account-title" class="text-lg font-semibold text-red-950">
                    Delete your account
                </h3>
                <p class="mt-2 text-sm leading-relaxed text-red-900/90">
                    This will permanently delete your account and associated data. You will be signed out everywhere. This action
                    <span class="font-semibold">cannot be undone</span>.
                </p>
            </div>

            <div class="max-h-[min(70dvh,calc(100dvh-6rem))] space-y-4 overflow-y-auto overscroll-contain px-5 py-5 sm:px-6 sm:py-6">
                <div>
                    <label for="delete_identity" class="block text-sm font-medium text-gray-900">
                        Your email or display name
                    </label>
                    <p class="mt-0.5 text-xs text-gray-500">
                        Must match your email or display name (email is not case-sensitive).
                    </p>
                    <input
                        id="delete_identity"
                        name="identity"
                        type="text"
                        required
                        value="{{ old('identity') }}"
                        maxlength="255"
                        autocapitalize="off"
                        autocorrect="off"
                        autocomplete="off"
                        class="mt-2 app-field border-red-200/80 focus:border-red-800 focus:ring-red-900/12"
                        data-lpignore="true"
                    >
                    @error('identity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="delete_confirmation_phrase" class="block text-sm font-medium text-gray-900">
                        Type confirmation phrase
                    </label>
                    <p class="mt-0.5 text-xs text-gray-500">
                        Enter exactly: <kbd class="rounded border border-gray-200 bg-gray-50 px-1 py-px font-mono text-xs text-gray-800">delete my account</kbd>
                    </p>
                    <input
                        id="delete_confirmation_phrase"
                        name="delete_confirmation"
                        type="text"
                        required
                        value="{{ old('delete_confirmation') }}"
                        maxlength="64"
                        spellcheck="false"
                        autocomplete="off"
                        class="mt-2 app-field border-red-200/80 focus:border-red-800 focus:ring-red-900/12"
                        data-test="delete-confirmation-input"
                        data-lpignore="true"
                    >
                    @error('delete_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="delete_password" class="block text-sm font-medium text-gray-900">Password</label>
                    <p class="mt-0.5 text-xs text-gray-500">
                        Confirm with your account password — not autofilled like a normal log-in field.
                    </p>
                    <input
                        id="delete_password"
                        name="password"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="mt-2 app-field border-red-200/80 focus:border-red-800 focus:ring-red-900/12"
                        data-test="delete-password-input"
                        data-lpignore="true"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-gray-100 bg-gray-50/80 px-5 py-4 sm:flex-row sm:justify-end sm:px-6 sm:py-4">
                <button
                    type="button"
                    id="profile-delete-account-cancel"
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm transition hover:bg-gray-50 sm:w-auto"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="w-full rounded-xl border border-red-300 bg-red-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-red-700 sm:w-auto"
                    data-test="delete-user-confirm-button"
                >
                    Permanently delete
                </button>
            </div>
        </form>
    </dialog>

    <script>
        (function () {
            const dialog = document.getElementById('profile-delete-account-dialog');
            const openBtn = document.getElementById('profile-open-delete-account-dialog');
            const cancelBtn = document.getElementById('profile-delete-account-cancel');

            if (!dialog || !openBtn) return;

            const openModal = function () {
                if (typeof dialog.showModal === 'function') {
                    dialog.showModal();
                }
            };

            const closeModal = function () {
                if (typeof dialog.close === 'function') {
                    dialog.close();
                }
            };

            openBtn.addEventListener('click', openModal);

            if (cancelBtn) {
                cancelBtn.addEventListener('click', closeModal);
            }

            if (dialog.dataset.showModalOnLoad === '1') {
                openModal();
            }
        })();
    </script>
    @else
        <section class="rounded-2xl border border-slate-200 bg-slate-50/90 p-6 shadow-sm sm:p-8">
            <h2 class="text-base font-semibold text-slate-900">Delete account</h2>
            <p class="mt-1 text-sm text-slate-700">
                Administrator and super administrator accounts cannot be deleted from profile settings. That avoids accidental loss of operational access or leaving the app without admins.
                If someone should lose access or be removed entirely, handle that separately (for example by another super administrator or controlled account changes).
            </p>
        </section>
    @endunless
@endsection

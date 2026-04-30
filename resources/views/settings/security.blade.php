@extends('settings.layout')

@section('title', 'Security')

@section('settings_header')
    <h1 class="text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">Security</h1>
@endsection

@section('settings_body')
    @include('settings.partials.flash-fortify')

    <section class="rounded-2xl border border-white/70 bg-white/90 p-6 shadow-sm sm:p-8">
        <h2 class="text-base font-semibold text-gray-900">Password</h2>
        <p class="mt-1 text-sm text-gray-600">Use a long, unique password you do not reuse elsewhere.</p>

        <form method="post" action="{{ route('user-password.update') }}" class="mt-6">
            @csrf
            @method('put')

            <div class="max-w-md space-y-5">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-900">Current password</label>
                    <input
                        id="current_password"
                        name="current_password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="app-field"
                    >
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-900">New password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="app-field"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-900">Confirm new password</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="app-field"
                    >
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900"
                    data-test="update-password-button"
                >
                    Update password
                </button>
            </div>
        </form>
    </section>

    @if ($canManageTwoFactor)
        <section class="rounded-2xl border border-white/70 bg-white/90 p-6 shadow-sm sm:p-8">
            <h2 class="text-base font-semibold text-gray-900">Two-factor authentication</h2>
            <p class="mt-1 text-sm text-gray-600">
                Add a code from an authenticator app when you sign in. Recommended for admins.
            </p>

            @if ($pendingTwoFactor && $qrSvg)
                <div class="mt-6 space-y-4">
                    <p class="text-sm text-gray-700">
                        Scan this QR code with your authenticator app, then enter the 6-digit code to confirm.
                    </p>
                    <div class="inline-flex rounded-xl border border-gray-200 bg-white p-3 shadow-inner [&_svg]:max-h-44 [&_svg]:w-auto">
                        {!! $qrSvg !!}
                    </div>
                    @if ($twoFactorSecretPlain)
                        <p class="text-xs text-gray-500">
                            Or enter this key manually:
                            <code class="mt-1 block rounded-lg bg-gray-100 px-2 py-1.5 font-mono text-sm text-gray-800">{{ $twoFactorSecretPlain }}</code>
                        </p>
                    @endif

                    <form method="post" action="{{ route('two-factor.confirm') }}" class="max-w-sm space-y-3">
                        @csrf
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-900">Authentication code</label>
                            <input
                                id="code"
                                name="code"
                                type="text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                maxlength="10"
                                required
                                autocomplete="one-time-code"
                                class="app-field"
                                placeholder="000000"
                            >
                            @error('code', 'confirmTwoFactorAuthentication')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800">
                            Confirm and enable
                        </button>
                    </form>
                </div>
            @elseif ($twoFactorEnabled)
                <div class="mt-6 space-y-6">
                    <p class="text-sm text-gray-700">Two-factor authentication is <span class="font-semibold text-emerald-800">on</span> for your account.</p>

                    <form method="post" action="{{ route('two-factor.disable') }}" data-confirm-message="Disable two-factor authentication? Your account will only use your password to sign in.">
                        @csrf
                        @method('delete')
                        <button
                            type="submit"
                            class="rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-medium text-red-700 shadow-sm hover:bg-red-50"
                        >
                            Disable 2FA
                        </button>
                    </form>

                    @if (count($recoveryCodes) > 0)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Recovery codes</h3>
                            <p class="mt-1 text-sm text-gray-600">Store these codes somewhere safe. Each code works once if you lose your device.</p>
                            <ul class="mt-3 grid gap-1 font-mono text-sm text-gray-800 sm:grid-cols-2">
                                @foreach ($recoveryCodes as $code)
                                    <li class="rounded-lg bg-gray-50 px-3 py-1.5">{{ $code }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="post" action="{{ route('two-factor.regenerate-recovery-codes') }}" data-confirm-message="Generate new recovery codes? Your old codes will stop working.">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-gray-900 underline decoration-gray-300 underline-offset-2 hover:decoration-gray-900">
                            Regenerate recovery codes
                        </button>
                    </form>
                </div>
            @else
                <div class="mt-6">
                    <p class="text-sm text-gray-700">
                        When enabled, you will enter a short code from an authenticator app after your password.
                    </p>
                    <form method="post" action="{{ route('two-factor.enable') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800">
                            Enable two-factor authentication
                        </button>
                    </form>
                </div>
            @endif
        </section>
    @endif
@endsection

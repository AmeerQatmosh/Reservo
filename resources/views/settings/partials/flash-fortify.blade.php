@if (session('status'))
    @php $status = session('status'); @endphp
    @if ($status === \Laravel\Fortify\Fortify::TWO_FACTOR_AUTHENTICATION_ENABLED)
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50/90 px-4 py-3 text-sm text-emerald-900 shadow-sm">
            Two-factor authentication setup started. Scan the QR code below and confirm with your app.
        </div>
    @elseif ($status === \Laravel\Fortify\Fortify::TWO_FACTOR_AUTHENTICATION_CONFIRMED)
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50/90 px-4 py-3 text-sm text-emerald-900 shadow-sm">
            Two-factor authentication is now enabled.
        </div>
    @elseif ($status === \Laravel\Fortify\Fortify::TWO_FACTOR_AUTHENTICATION_DISABLED)
        <div class="mb-6 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 shadow-sm">
            Two-factor authentication has been disabled.
        </div>
    @elseif ($status === \Laravel\Fortify\Fortify::RECOVERY_CODES_GENERATED)
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50/90 px-4 py-3 text-sm text-emerald-900 shadow-sm">
            New recovery codes have been generated. Save them in a safe place.
        </div>
    @endif
@endif

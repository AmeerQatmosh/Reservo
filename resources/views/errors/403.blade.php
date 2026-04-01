@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
    <div class="mx-auto max-w-3xl py-10">
        <div class="rounded-3xl border border-white/70 bg-white/90 p-8 shadow-sm">
            <div class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Access denied</div>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">You do not have permission to view this page.</h1>
            <p class="mt-3 text-sm leading-6 text-gray-600">
                This area is restricted based on your current account role. If you expected to have access, sign in with the correct account or contact the project owner.
            </p>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-2xl bg-gray-900 px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800">
                        Back to dashboard
                    </a>
                @endauth

                <a href="{{ route('rooms.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-sm transition hover:border-gray-300 hover:bg-gray-50 hover:text-gray-900">
                    Browse rooms
                </a>
            </div>
        </div>
    </div>
@endsection

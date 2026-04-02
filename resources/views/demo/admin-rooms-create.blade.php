@extends('layouts.app')

@section('title', 'Create Room')

@section('content')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Admin</div>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">Create room</h1>
            <p class="mt-2 text-sm text-gray-600">
                Same fields as production. Data exists only in this browser session—not in the real database.
            </p>
        </div>
        <a href="{{ route('demo.admin.rooms') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Back</a>
    </div>

    <div class="mt-8 rounded-3xl border border-white/70 bg-white/90 p-7 shadow-sm">
        <form method="POST" action="{{ route('demo.admin.rooms.store') }}">
            @csrf
            @include('admin.rooms._form', ['submitLabel' => 'Create', 'room' => null])
        </form>
    </div>
@endsection

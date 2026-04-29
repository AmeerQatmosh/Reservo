@extends('layouts.app')

@section('title', 'Create Room')

@section('content')
    <x-page-breadcrumbs
        class="mb-4"
        :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Admin · Rooms', 'url' => route('admin.rooms.index')],
            ['label' => 'Create room'],
        ]"
    />
    <div>
        <div class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Admin</div>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">Create room</h1>
        <p class="mt-2 text-sm text-gray-600">Add a new room with a clear name, capacity, and description.</p>
    </div>

    <div class="mt-8 rounded-3xl border border-white/70 bg-white/90 p-7 shadow-sm">
        <form method="POST" action="{{ route('admin.rooms.store') }}">
            @csrf
            @include('admin.rooms._form', ['submitLabel' => 'Create', 'room' => null])
        </form>
    </div>
@endsection


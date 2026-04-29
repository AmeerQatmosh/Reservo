@extends('layouts.app')

@section('title', 'Edit Room')

@section('content')
    <x-page-breadcrumbs
        class="mb-4"
        :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Admin · Rooms', 'url' => route('admin.rooms.index')],
            ['label' => 'Edit room'],
        ]"
    />
    <div>
        <div class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Admin</div>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">Edit room</h1>
        <p class="mt-2 text-sm text-gray-600">Update the room details below and save your changes.</p>
    </div>

    <div class="mt-8 rounded-3xl border border-white/70 bg-white/90 p-7 shadow-sm">
        <form method="POST" action="{{ route('admin.rooms.update', $room->id) }}">
            @csrf
            @method('PUT')
            @include('admin.rooms._form', ['submitLabel' => 'Save', 'room' => $room])
        </form>
    </div>
@endsection


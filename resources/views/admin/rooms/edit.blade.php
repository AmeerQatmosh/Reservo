@extends('layouts.app')

@section('title', 'Edit Room')

@section('content')
    <x-page-breadcrumbs
        class="mb-4"
        :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Admin'],
            ['label' => 'Rooms', 'url' => route('admin.rooms.index')],
            ['label' => 'Edit room'],
        ]"
    />
    <div>
        <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Edit room</h1>
    </div>

    <div class="mt-8 grid gap-8 lg:grid-cols-12 lg:items-start lg:gap-10">
        <div class="min-w-0 lg:col-span-7 xl:col-span-8">
            <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-sm sm:p-7">
                <form
                    method="POST"
                    action="{{ route('admin.rooms.update', $room->id) }}"
                    id="admin-room-form"
                    data-admin-room-form
                >
                    @csrf
                    @method('PUT')
                    @include('admin.rooms._form', [
                        'submitLabel' => __('Save'),
                        'room' => $room,
                        'cancelUrl' => route('admin.rooms.show', $room),
                    ])
                </form>
            </div>
        </div>
        <aside class="min-w-0 lg:col-span-5 xl:col-span-4 lg:sticky lg:top-24 lg:self-start">
            @include('admin.rooms.partials.form-preview')
        </aside>
    </div>
@endsection


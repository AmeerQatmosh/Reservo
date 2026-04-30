@extends('layouts.app')

@section('title', 'Create Room')

@section('content')
    <x-page-breadcrumbs
        class="mb-4"
        :items="[
            ['label' => 'Sandbox', 'url' => route('demo.hub')],
            ['label' => 'Admin rooms', 'url' => route('demo.admin.rooms')],
            ['label' => 'Create room'],
        ]"
    />
    <div>
        <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Create room</h1>
    </div>

    <div class="mt-8 grid gap-8 lg:grid-cols-12 lg:items-start lg:gap-10">
        <div class="min-w-0 lg:col-span-7 xl:col-span-8">
            <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-sm sm:p-7">
                <form
                    method="POST"
                    action="{{ route('demo.admin.rooms.store') }}"
                    id="admin-room-form"
                    data-admin-room-form
                >
                    @csrf
                    @include('admin.rooms._form', [
                        'submitLabel' => __('Create'),
                        'room' => null,
                        'cancelUrl' => route('demo.admin.rooms'),
                    ])
                </form>
            </div>
        </div>
        <aside class="min-w-0 lg:col-span-5 xl:col-span-4 lg:sticky lg:top-24 lg:self-start">
            @include('admin.rooms.partials.form-preview')
        </aside>
    </div>
@endsection

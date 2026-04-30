@extends('layouts.app')

@section('title', 'Admin Rooms: '.$room->name)

@section('content')
    <x-page-breadcrumbs
        class="mb-4"
        :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Admin'],
            ['label' => 'Rooms', 'url' => route('admin.rooms.index')],
            ['label' => $room->name],
        ]"
    />
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">{{ $room->name }}</h1>
                @if ($room->trashed())
                    <span class="rounded-full bg-gray-200 px-3 py-1 text-xs font-medium text-gray-800">Deleted (archived)</span>
                @else
                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">Active</span>
                @endif
            </div>
        </div>
        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:flex-wrap">
            @if (! $room->trashed())
                <a href="{{ route('rooms.show', $room->id) }}" class="inline-flex w-full items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm hover:bg-gray-50 sm:w-auto">
                    Public room page
                </a>
            @endif
            <a href="{{ route('admin.rooms.edit', $room->id) }}" class="inline-flex w-full items-center justify-center rounded-xl bg-teal-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-teal-700 sm:w-auto">
                Edit room
            </a>
            @if ($room->trashed())
                <form method="POST" action="{{ route('admin.rooms.restore', $room->id) }}" class="w-full sm:w-auto" data-confirm-message="Restore this room?" data-confirm-variant="success" data-confirm-button-label="Restore">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full rounded-xl border border-green-200 bg-green-50 px-4 py-2.5 text-sm font-medium text-green-900 hover:bg-green-100 sm:w-auto">
                        Restore
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.rooms.destroy', $room->id) }}" class="w-full sm:w-auto" data-confirm-message="Delete this room?" data-confirm-variant="danger" data-confirm-button-label="Delete">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-medium text-red-900 hover:bg-red-100 sm:w-auto">
                        Delete
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="mt-8 overflow-hidden rounded-3xl border border-white/70 bg-white shadow-sm">
        <div class="aspect-[21/9] max-h-[22rem] w-full overflow-hidden bg-gray-100 sm:aspect-[2.4/1]">
            @if ($room->image_url)
                <img src="{{ $room->image_url }}" alt="{{ $room->name }}" class="h-full w-full object-cover" loading="eager">
            @else
                <div class="flex h-full min-h-[12rem] w-full items-center justify-center text-sm text-gray-500">
                    No photo URL set
                </div>
            @endif
        </div>
    </div>

    <div class="mt-8 rounded-3xl border border-white/70 bg-white/90 p-7 shadow-sm">
        <div class="text-xs font-semibold uppercase tracking-[0.15em] text-gray-500">Database</div>
        <dl class="mt-3 grid gap-3 text-sm sm:grid-cols-2">
            <div>
                <dt class="text-gray-500">Room ID</dt>
                <dd class="font-mono text-gray-900">{{ $room->id }}</dd>
            </div>
            @if ($room->image_url)
                <div class="sm:col-span-2">
                    <dt class="text-gray-500">Photo URL</dt>
                    <dd class="break-all font-mono text-xs text-gray-800">{{ $room->image_url }}</dd>
                </div>
            @endif
        </dl>
    </div>

    <div class="mt-6 rounded-3xl border border-white/70 bg-white/90 p-7 shadow-sm">
        @include('rooms.partials.detail-snippet', [
            'room' => $room,
            'hideImage' => true,
            'showViewLink' => false,
            'showTitle' => false,
        ])
    </div>
@endsection

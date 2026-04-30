@extends('layouts.app')

@section('title', 'Rooms')

@section('content')
    <x-page-breadcrumbs
        class="mb-4"
        :items="[
            auth()->check()
                ? [
                    'label' => auth()->user()->isAdmin() ? __('Dashboard') : __('Home'),
                    'url' => route('dashboard'),
                ]
                : ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Rooms'],
        ]"
    />

    @include('rooms.partials.browse-surface')
@endsection

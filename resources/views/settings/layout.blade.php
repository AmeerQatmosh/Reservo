@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full min-w-0 max-w-6xl flex-col gap-8 lg:flex-row lg:gap-12">
        @include('settings.partials.sidebar')
        <div class="min-w-0 flex-1">
            @yield('settings_header')
            <div class="@yield('settings_body_class', 'mt-8 space-y-10')">
                @yield('settings_body')
            </div>
        </div>
    </div>
@endsection

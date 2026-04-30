@extends('layouts.app')

@section('title', 'Admin Users')

@section('content')
    @php
        $roleVal = (string) ($filters['role'] ?? 'all');
    @endphp

    <x-page-breadcrumbs
        class="mb-6"
        :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Admin'],
            ['label' => 'Users'],
        ]"
    />

    <div class="flex flex-col gap-8 lg:flex-row lg:items-start">
        <div
            class="w-full min-w-0 lg:w-[24rem] lg:flex-none lg:sticky lg:top-[calc(5rem+env(safe-area-inset-top,0px))] lg:z-10 lg:self-start"
        >
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm leading-relaxed text-blue-900">
                Public registration always creates a normal <span class="font-medium">User</span> account.
                @if ($canManageRoles)
                    Only a <span class="font-medium">Super Admin</span> can grant admin access from this screen.
                @else
                    You can view users here, but only a <span class="font-medium">Super Admin</span> can change roles.
                @endif
            </div>

            <div class="mt-5 rounded-3xl border border-white/70 bg-white/90 p-6 shadow-sm">
                <div class="mb-5">
                    <h2 class="text-base font-semibold text-gray-900">{{ __('Search & filters') }}</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Narrow the list, then apply. Reset clears both fields.') }}</p>
                </div>

                <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-5">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-900">{{ __('Search') }}</label>
                        <input
                            id="search"
                            name="search"
                            type="text"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="{{ __('Name or email') }}"
                            class="app-field mt-1.5 w-full"
                        >
                    </div>

                    <x-reservo-form-select
                        name="role"
                        hidden-id="role"
                        trigger-id="admin_users_role_trigger"
                        listbox-id="admin_users_role_listbox"
                        label="Role"
                        placeholder="Role"
                        :value="$roleVal"
                    >
                        <button
                            type="button"
                            role="option"
                            data-value="all"
                            @class([
                                'reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2',
                                'bg-gray-100 font-medium text-gray-900' => $roleVal === 'all',
                                'text-gray-900' => $roleVal !== 'all',
                            ])
                            aria-selected="{{ $roleVal === 'all' ? 'true' : 'false' }}"
                        >{{ __('All roles') }}</button>
                        <button
                            type="button"
                            role="option"
                            data-value="super_admin"
                            @class([
                                'reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2',
                                'bg-gray-100 font-medium text-gray-900' => $roleVal === 'super_admin',
                                'text-gray-900' => $roleVal !== 'super_admin',
                            ])
                            aria-selected="{{ $roleVal === 'super_admin' ? 'true' : 'false' }}"
                        >{{ __('Super Admins') }}</button>
                        <button
                            type="button"
                            role="option"
                            data-value="admin"
                            @class([
                                'reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2',
                                'bg-gray-100 font-medium text-gray-900' => $roleVal === 'admin',
                                'text-gray-900' => $roleVal !== 'admin',
                            ])
                            aria-selected="{{ $roleVal === 'admin' ? 'true' : 'false' }}"
                        >{{ __('Admins') }}</button>
                        <button
                            type="button"
                            role="option"
                            data-value="user"
                            @class([
                                'reservo-form-select__opt w-full rounded-lg px-3 py-2.5 text-left text-sm hover:bg-gray-100 focus:bg-gray-100 focus:outline-none sm:py-2',
                                'bg-gray-100 font-medium text-gray-900' => $roleVal === 'user',
                                'text-gray-900' => $roleVal !== 'user',
                            ])
                            aria-selected="{{ $roleVal === 'user' ? 'true' : 'false' }}"
                        >{{ __('Users') }}</button>
                    </x-reservo-form-select>

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-stretch">
                        <button
                            type="submit"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-2xl bg-teal-600 px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-teal-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/35"
                        >
                            <x-lucide name="filter" class="h-4 w-4 shrink-0" aria-hidden="true" />
                            {{ __('Filter') }}
                        </button>
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-sm transition hover:border-gray-400 hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/15"
                        >
                            <x-lucide name="rotate-ccw" class="h-4 w-4 shrink-0" aria-hidden="true" />
                            {{ __('Reset') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl">{{ __('Users') }}</h1>
                </div>
            </div>

            @if ($users->count() === 0)
                <div class="mt-6 rounded-3xl border border-white/70 bg-white/90 p-6 text-sm text-gray-700 shadow-sm">
                    {{ __('No users match the current filters.') }}
                </div>
            @else
                <div class="mt-6 overflow-hidden rounded-lg border border-white/70 bg-white/90 shadow-sm">
                    <div class="app-table-scroll">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                                <tr>
                                    <th class="px-4 py-3">{{ __('Name') }}</th>
                                    <th class="px-4 py-3">{{ __('Email') }}</th>
                                    <th class="px-4 py-3">{{ __('Current role') }}</th>
                                    <th class="px-4 py-3">{{ $canManageRoles ? __('Change role') : __('Role access') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                                        <td class="px-4 py-3">{{ $user->email }}</td>
                                        <td class="px-4 py-3">
                                            @if ($user->role === 'super_admin')
                                                <span class="rounded-full bg-purple-100 px-2 py-0.5 text-xs text-purple-700">{{ __('Super Admin') }}</span>
                                            @elseif ($user->role === 'admin')
                                                <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700">{{ __('Admin') }}</span>
                                            @else
                                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ __('User') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if (! $canManageRoles)
                                                <span class="text-sm text-gray-500">{{ __('View only') }}</span>
                                            @elseif ($user->role === 'super_admin')
                                                <span class="text-sm text-gray-500">{{ __('Managed outside this screen') }}</span>
                                            @else
                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.users.update-role', $user->id) }}"
                                                    class="flex flex-col gap-2 sm:flex-row sm:items-center"
                                                    data-confirm-message="{{ __("Are you sure you want to update this user's role?") }}"
                                                    data-confirm-variant="warning"
                                                    data-confirm-button-label="{{ __('Update role') }}"
                                                >
                                                    @csrf
                                                    @method('PUT')

                                                    <select name="role" class="app-field min-w-[130px]">
                                                        <option value="user" @selected($user->role === 'user')>{{ __('User') }}</option>
                                                        <option value="admin" @selected($user->role === 'admin')>{{ __('Admin') }}</option>
                                                    </select>

                                                    <button type="submit" class="app-table-action">
                                                        {{ __('Save') }}
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

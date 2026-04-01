@extends('layouts.app')

@section('title', 'Admin Users')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold sm:text-2xl">Admin · Users</h1>
    </div>

    <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm leading-relaxed text-blue-900">
        Public registration always creates a normal <span class="font-medium">User</span> account.
        @if ($canManageRoles)
            Only a <span class="font-medium">Super Admin</span> can grant admin access from this screen.
        @else
            You can view users here, but only a <span class="font-medium">Super Admin</span> can change roles.
        @endif
    </div>

    <div class="mt-6 rounded-2xl border border-white/70 bg-white/90 p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-[minmax(0,1.5fr)_minmax(220px,0.8fr)_auto] xl:items-end">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-900">Search</label>
                <input
                    id="search"
                    name="search"
                    type="text"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Name or email"
                    class="app-field"
                >
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-900">Role</label>
                <select id="role" name="role" class="app-field">
                    <option value="all" @selected(($filters['role'] ?? 'all') === 'all')>All roles</option>
                    <option value="super_admin" @selected(($filters['role'] ?? '') === 'super_admin')>Super Admins</option>
                    <option value="admin" @selected(($filters['role'] ?? '') === 'admin')>Admins</option>
                    <option value="user" @selected(($filters['role'] ?? '') === 'user')>Users</option>
                </select>
            </div>

            <div class="flex flex-row flex-wrap items-end gap-2 xl:flex-nowrap xl:justify-end">
                <button
                    type="submit"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/35"
                >
                    <x-lucide name="filter" class="h-4 w-4 shrink-0" aria-hidden="true" />
                    Filter
                </button>
                <a
                    href="{{ route('admin.users.index') }}"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:border-gray-400 hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/15"
                >
                    <x-lucide name="rotate-ccw" class="h-4 w-4 shrink-0" aria-hidden="true" />
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if ($users->count() === 0)
        <div class="mt-6 rounded-lg border bg-white p-6 text-sm text-gray-700">
            No users match the current filters.
        </div>
    @else
        <div class="mt-6 overflow-hidden rounded-lg border bg-white">
            <div class="app-table-scroll">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Current role</th>
                        <th class="px-4 py-3">{{ $canManageRoles ? 'Change role' : 'Role access' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($users as $user)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                @if ($user->role === 'super_admin')
                                    <span class="rounded-full bg-purple-100 px-2 py-0.5 text-xs text-purple-700">Super Admin</span>
                                @elseif ($user->role === 'admin')
                                    <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700">Admin</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">User</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if (! $canManageRoles)
                                    <span class="text-sm text-gray-500">View only</span>
                                @elseif ($user->role === 'super_admin')
                                    <span class="text-sm text-gray-500">Managed outside this screen</span>
                                @else
                                    <form
                                        method="POST"
                                        action="{{ route('admin.users.update-role', $user->id) }}"
                                        class="flex flex-col gap-2 sm:flex-row sm:items-center"
                                        data-confirm-message="Are you sure you want to update this user's role?"
                                        data-confirm-variant="warning"
                                        data-confirm-button-label="Update role"
                                    >
                                        @csrf
                                        @method('PUT')

                                        <select name="role" class="app-field min-w-[130px]">
                                            <option value="user" @selected($user->role === 'user')>User</option>
                                            <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                        </select>

                                        <button type="submit" class="app-table-action">
                                            Save
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
@endsection


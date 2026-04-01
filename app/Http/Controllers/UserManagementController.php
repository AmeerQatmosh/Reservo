<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $role = $request->query('role', 'all');

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($userQuery) use ($search) {
                    $userQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(in_array($role, ['user', 'admin', 'super_admin'], true), fn ($query) => $query->where('role', $role))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'canManageRoles' => $request->user()?->isSuperAdmin() ?? false,
            'filters' => [
                'search' => $search,
                'role' => $role,
            ],
        ]);
    }

    public function updateRole(Request $request, int $id): RedirectResponse
    {
        $user = User::query()->findOrFail($id);

        $validated = $request->validate([
            'role' => ['required', 'in:user,admin'],
        ]);

        if ($user->isSuperAdmin()) {
            return back()->withErrors([
                'role' => 'Super admin accounts cannot be changed from this screen.',
            ]);
        }

        if ($user->id === $request->user()->id && $validated['role'] !== 'admin') {
            return back()->withErrors([
                'role' => 'You cannot remove your own admin access.',
            ]);
        }

        $user->update([
            'role' => $validated['role'],
        ]);

        return back()->with('success', 'User role updated.');
    }
}


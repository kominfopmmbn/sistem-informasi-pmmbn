<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $q = isset($filters['q']) ? trim($filters['q']) : '';

        $users = User::query()->with('roles');

        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $users->where(function ($query) use ($like): void {
                $query->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        }

        $users = $users->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $filterState = ['q' => $q];
        $hasActiveFilters = $q !== '';

        return view('admin.users.index', compact('users', 'filterState', 'hasActiveFilters'));
    }

    public function create(): View
    {
        $roles = Role::query()->where('guard_name', 'web')->orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create($request->safe()->only(['name', 'email', 'password']));
        $this->syncSingleRole($user, $request->validated('role_id'));

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $user->load('roles');
        $roles = Role::query()->where('guard_name', 'web')->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        $user->update($data);
        $this->syncSingleRole($user, $request->validated('role_id'));

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->getKey() === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    /** Satu user hanya satu peran; null menghapus semua peran. */
    private function syncSingleRole(User $user, ?int $roleId): void
    {
        $user->syncRoles($roleId !== null ? [$roleId] : []);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->withCount('permissions')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissionsByGroup = $this->webPermissionsByGroup();

        return view('admin.roles.create', compact('permissionsByGroup'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = Role::create([
            'name' => $request->validated('name'),
            'guard_name' => 'web',
        ]);
        $this->syncPermissionIds(
            $role,
            $request->validated('permission_ids', []),
        );

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Peran berhasil ditambahkan.');
    }

    public function edit(Role $role): View
    {
        $this->assertWebRole($role);
        $role->load('permissions');
        $permissionsByGroup = $this->webPermissionsByGroup();

        return view('admin.roles.edit', compact('role', 'permissionsByGroup'));
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->assertWebRole($role);
        if ($role->name === 'Administrator') {
            $role->syncPermissions(
                $this->permissionNamesFromIds($request->validated('permission_ids', [])),
            );

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Peran berhasil diperbarui.');
        }

        $role->update(['name' => $request->validated('name')]);
        $this->syncPermissionIds(
            $role,
            $request->validated('permission_ids', []),
        );

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Peran berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->assertWebRole($role);
        if ($role->name === 'Administrator') {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'Peran Administrator tidak dapat dihapus.');
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Peran berhasil dihapus.');
    }

    /** Hanya peran web guard; role guard lain (jika ada) tidak dikelola di sini. */
    private function assertWebRole(Role $role): void
    {
        abort_if($role->guard_name !== 'web', 404);
    }

    private function webPermissionsByGroup(): Collection
    {
        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return $permissions->groupBy(function (Permission $p) {
            $parts = explode('.', $p->name, 2);

            return $parts[0] ?? 'other';
        });
    }

    private function syncPermissionIds(Role $role, array $ids): void
    {
        $role->syncPermissions(
            $this->permissionNamesFromIds($ids),
        );
    }

    private function permissionNamesFromIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        return Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('id', $ids)
            ->pluck('name')
            ->all();
    }
}

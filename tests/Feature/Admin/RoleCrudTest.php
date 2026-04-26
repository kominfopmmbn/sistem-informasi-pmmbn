<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    private function actingAsUser(): User
    {
        $this->administratorRole();

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('Administrator');
        $this->actingAs($user);

        return $user;
    }

    private function administratorRole(): Role
    {
        return Role::firstOrCreate(
            ['name' => 'Administrator', 'guard_name' => 'web'],
        );
    }

    public function test_guest_is_redirected_from_roles_index_to_admin_login(): void
    {
        $this->get(route('admin.roles.index'))
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_guest_cannot_post_to_store_roles(): void
    {
        $this->post(route('admin.roles.store'), [])
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_index_forbidden_without_roles_view_permission(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('admin.roles.index'))->assertForbidden();
    }

    public function test_index_shows_roles(): void
    {
        $this->actingAsUser();

        $this->get(route('admin.roles.index'))->assertOk()
            ->assertViewHas('roles', fn ($paginator) => $paginator->total() >= 1);
    }

    public function test_create_renders(): void
    {
        $this->actingAsUser();

        $this->get(route('admin.roles.create'))->assertOk();
    }

    public function test_store_persists_role_and_syncs_permissions(): void
    {
        $this->actingAsUser();
        $articlesView = Permission::query()
            ->where('name', 'articles.view')
            ->where('guard_name', 'web')
            ->firstOrFail();
        $usersView = Permission::query()
            ->where('name', 'users.view')
            ->where('guard_name', 'web')
            ->firstOrFail();

        $this->post(route('admin.roles.store'), [
            'name' => 'Konten Editor',
            'permission_ids' => [(string) $articlesView->id, (string) $usersView->id],
        ])->assertRedirect(route('admin.roles.index'))
            ->assertSessionHas('success', 'Peran berhasil ditambahkan.');

        $role = Role::query()
            ->where('name', 'Konten Editor')
            ->where('guard_name', 'web')
            ->firstOrFail();

        $this->assertTrue($role->hasPermissionTo('articles.view'));
        $this->assertTrue($role->hasPermissionTo('users.view'));
        $this->assertFalse($role->hasPermissionTo('articles.create'));
    }

    public function test_store_validates_invalid_payload(): void
    {
        $this->actingAsUser();

        $this->post(route('admin.roles.store'), [
            'name' => '',
        ])->assertSessionHasErrors(['name']);
    }

    public function test_edit_renders(): void
    {
        $this->actingAsUser();
        $target = Role::create(['name' => 'Editor Tes', 'guard_name' => 'web']);

        $this->get(route('admin.roles.edit', $target))->assertOk();
    }

    public function test_update_changes_name_and_permissions(): void
    {
        $this->actingAsUser();
        $target = Role::create(['name' => 'Hampir Hapus', 'guard_name' => 'web']);
        $articlesView = Permission::query()
            ->where('name', 'articles.view')
            ->where('guard_name', 'web')
            ->firstOrFail();
        $target->syncPermissions([$articlesView->name]);

        $articlesCreate = Permission::query()
            ->where('name', 'articles.create')
            ->where('guard_name', 'web')
            ->firstOrFail();

        $this->put(route('admin.roles.update', $target), [
            'name' => 'Nama Baru',
            'permission_ids' => [(string) $articlesView->id, (string) $articlesCreate->id],
        ])->assertRedirect(route('admin.roles.index'))
            ->assertSessionHas('success', 'Peran berhasil diperbarui.');

        $target->refresh();
        $this->assertSame('Nama Baru', $target->name);
        $this->assertTrue($target->hasPermissionTo('articles.create'));
    }

    public function test_cannot_update_administrator_name(): void
    {
        $this->actingAsUser();
        $admin = $this->administratorRole();

        $this->from(route('admin.roles.edit', $admin))
            ->put(route('admin.roles.update', $admin), [
                'name' => 'Bukan Admin',
            ])->assertSessionHasErrors('name');
    }

    public function test_update_administrator_still_allows_syncing_permissions(): void
    {
        $this->actingAsUser();
        $admin = $this->administratorRole();
        $ids = $admin->getPermissionNames()->isEmpty() ? [] : $admin->permissions->pluck('id')->all();

        $this->put(route('admin.roles.update', $admin), [
            'name' => 'Administrator',
            'permission_ids' => array_map('strval', $ids),
        ])->assertRedirect(route('admin.roles.index'));

        $this->assertSame('Administrator', $admin->fresh()->name);
    }

    public function test_destroy_deletes_non_admin_role(): void
    {
        $this->actingAsUser();
        $victim = Role::create(['name' => 'Sementara Dihapus', 'guard_name' => 'web']);

        $this->delete(route('admin.roles.destroy', $victim))
            ->assertRedirect(route('admin.roles.index'))
            ->assertSessionHas('success', 'Peran berhasil dihapus.');

        $this->assertDatabaseMissing('roles', ['id' => $victim->id]);
    }

    public function test_destroy_rejects_administrator(): void
    {
        $this->actingAsUser();
        $admin = $this->administratorRole();

        $this->delete(route('admin.roles.destroy', $admin))
            ->assertRedirect(route('admin.roles.index'))
            ->assertSessionHas('error', 'Peran Administrator tidak dapat dihapus.');

        $this->assertDatabaseHas('roles', [
            'id' => $admin->id,
            'name' => 'Administrator',
        ]);
    }
}

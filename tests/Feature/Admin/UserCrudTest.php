<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserCrudTest extends TestCase
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

    public function test_guest_is_redirected_from_users_index_to_admin_login(): void
    {
        $this->get(route('admin.users.index'))
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_guest_cannot_post_to_store_users(): void
    {
        $this->post(route('admin.users.store'), [])
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_index_forbidden_without_users_view_permission(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_index_shows_users_and_respects_query_filter_q(): void
    {
        $this->actingAsUser();
        User::factory()->create([
            'name' => 'Alpha Unique Name',
            'email' => 'alpha-unique@example.com',
        ]);
        User::factory()->create([
            'name' => 'Other Person',
            'email' => 'other@example.com',
        ]);

        $this->get(route('admin.users.index'))->assertOk()
            ->assertViewHas('users', fn ($paginator) => $paginator->total() === 3);

        $r = $this->get(route('admin.users.index', ['q' => 'alpha-unique']))->assertOk();
        $this->assertSame(1, $r->viewData('users')->total());
        $this->assertStringContainsString('alpha-unique@example.com', $r->getContent());
    }

    public function test_create_renders(): void
    {
        $this->actingAsUser();

        $this->get(route('admin.users.create'))->assertOk();
    }

    public function test_store_persists_user_assigns_role_and_flashes_success(): void
    {
        $this->actingAsUser();
        $role = $this->administratorRole();

        $this->post(route('admin.users.store'), [
            'name' => 'Pengguna Baru',
            'email' => 'baru@example.com',
            'password' => 'password-baru-8',
            'password_confirmation' => 'password-baru-8',
            'role_id' => (string) $role->id,
        ])->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success', 'Pengguna berhasil ditambahkan.');

        $this->assertDatabaseHas('users', [
            'name' => 'Pengguna Baru',
            'email' => 'baru@example.com',
        ]);

        $user = User::query()->where('email', 'baru@example.com')->firstOrFail();
        $this->assertTrue($user->fresh()->hasRole('Administrator'));
    }

    public function test_store_validates_invalid_payload(): void
    {
        $this->actingAsUser();

        $this->post(route('admin.users.store'), [
            'name' => 'X',
            'email' => 'not-an-email',
        ])->assertSessionHasErrors(['email', 'password']);

        $this->post(route('admin.users.store'), [
            'name' => 'Y',
            'email' => 'valid@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors(['password']);
    }

    public function test_edit_renders(): void
    {
        $this->actingAsUser();
        $target = User::factory()->create();

        $this->get(route('admin.users.edit', $target))->assertOk();
    }

    public function test_update_changes_attributes_and_role_and_flashes_success(): void
    {
        $this->actingAsUser();
        $role = $this->administratorRole();
        $target = User::factory()->create([
            'name' => 'Lama',
            'email' => 'lama@example.com',
        ]);

        $this->put(route('admin.users.update', $target), [
            'name' => 'Baru',
            'email' => 'baru-email@example.com',
            'role_id' => (string) $role->id,
        ])->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success', 'Pengguna berhasil diperbarui.');

        $target->refresh();
        $this->assertSame('Baru', $target->name);
        $this->assertSame('baru-email@example.com', $target->email);
        $this->assertTrue($target->hasRole('Administrator'));
    }

    public function test_update_leaves_password_unchanged_when_blank(): void
    {
        $this->actingAsUser();
        $role = $this->administratorRole();
        $target = User::factory()->create([
            'password' => Hash::make('unchanged-secret'),
        ]);

        $this->put(route('admin.users.update', $target), [
            'name' => 'Nama Diubah',
            'email' => $target->email,
            'password' => '',
            'password_confirmation' => '',
            'role_id' => (string) $role->id,
        ])->assertRedirect(route('admin.users.index'));

        $this->assertTrue(Hash::check('unchanged-secret', $target->fresh()->password));
    }

    public function test_destroy_deletes_other_user(): void
    {
        $this->actingAsUser();
        $victim = User::factory()->create();

        $this->delete(route('admin.users.destroy', $victim))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success', 'Pengguna berhasil dihapus.');

        $this->assertDatabaseMissing('users', ['id' => $victim->id]);
    }

    public function test_destroy_prevents_deleting_authenticated_user(): void
    {
        $actor = $this->actingAsUser();

        $this->delete(route('admin.users.destroy', $actor))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');

        $this->assertDatabaseHas('users', ['id' => $actor->id]);
    }
}

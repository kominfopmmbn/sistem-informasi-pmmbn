<?php

namespace Tests\Feature\Admin;

use App\Models\RegionalLeader;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegionalLeaderCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    private function actingAsAdministrator(): User
    {
        Role::firstOrCreate(
            ['name' => 'Administrator', 'guard_name' => 'web'],
        );

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('Administrator');
        $this->actingAs($user);

        return $user;
    }

    public function test_guest_is_redirected_from_index_to_admin_login(): void
    {
        $this->get(route('admin.regional-leaders.index'))
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_guest_cannot_post_to_store(): void
    {
        $this->post(route('admin.regional-leaders.store'), [])
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_index_forbidden_without_view_permission(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('admin.regional-leaders.index'))->assertForbidden();
    }

    public function test_index_shows_regional_leaders(): void
    {
        $this->actingAsAdministrator();

        RegionalLeader::query()->create(['code' => 'RL001', 'name' => 'Pimpinan Tes']);

        $this->get(route('admin.regional-leaders.index'))->assertOk()
            ->assertSee('Pimpinan Tes', false);
    }

    public function test_index_filters_by_q(): void
    {
        $this->actingAsAdministrator();

        RegionalLeader::query()->create(['code' => 'RLXYZ', 'name' => 'Pimpinan FilterXYZ']);

        $this->get(route('admin.regional-leaders.index', ['q' => 'FilterXYZ']))
            ->assertOk()
            ->assertSee('Pimpinan FilterXYZ', false);

        $this->get(route('admin.regional-leaders.index', ['q' => 'RLXYZ']))
            ->assertOk()
            ->assertSee('Pimpinan FilterXYZ', false);

        $this->get(route('admin.regional-leaders.index', ['q' => 'tidak-ada-xyz']))
            ->assertOk()
            ->assertDontSee('Pimpinan FilterXYZ', false);
    }

    public function test_create_renders(): void
    {
        $this->actingAsAdministrator();

        $this->get(route('admin.regional-leaders.create'))->assertOk();
    }

    public function test_store_persists_regional_leader(): void
    {
        $this->actingAsAdministrator();

        $this->post(route('admin.regional-leaders.store'), [
            'code' => 'RL010',
            'name' => 'Pimpinan Contoh',
        ])->assertRedirect(route('admin.regional-leaders.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('regional_leaders', [
            'code' => 'RL010',
            'name' => 'Pimpinan Contoh',
        ]);
    }

    public function test_store_validates_invalid_payload(): void
    {
        $this->actingAsAdministrator();

        $this->post(route('admin.regional-leaders.store'), [
            'code' => '',
            'name' => '',
        ])->assertSessionHasErrors(['code', 'name']);
    }

    public function test_store_rejects_duplicate_code(): void
    {
        $this->actingAsAdministrator();

        RegionalLeader::query()->create(['code' => 'RLDUP', 'name' => 'Sudah Ada']);

        $this->post(route('admin.regional-leaders.store'), [
            'code' => 'RLDUP',
            'name' => 'Lainnya',
        ])->assertSessionHasErrors(['code']);
    }

    public function test_edit_renders(): void
    {
        $this->actingAsAdministrator();
        $regionalLeader = RegionalLeader::query()->create(['code' => 'RL020', 'name' => 'Edit Tes']);

        $this->get(route('admin.regional-leaders.edit', $regionalLeader))->assertOk()
            ->assertSee('Edit Tes', false);
    }

    public function test_update_changes_regional_leader(): void
    {
        $this->actingAsAdministrator();
        $regionalLeader = RegionalLeader::query()->create(['code' => 'RL030', 'name' => 'Lama']);

        $this->put(route('admin.regional-leaders.update', $regionalLeader), [
            'code' => 'RL031',
            'name' => 'Baru',
        ])->assertRedirect(route('admin.regional-leaders.index'))
            ->assertSessionHas('success');

        $regionalLeader->refresh();
        $this->assertSame('RL031', $regionalLeader->code);
        $this->assertSame('Baru', $regionalLeader->name);
    }

    public function test_update_rejects_duplicate_code_from_other_row(): void
    {
        $this->actingAsAdministrator();

        RegionalLeader::query()->create(['code' => 'RLA', 'name' => 'Pertama']);
        $second = RegionalLeader::query()->create(['code' => 'RLB', 'name' => 'Kedua']);

        $this->put(route('admin.regional-leaders.update', $second), [
            'code' => 'RLA',
            'name' => 'Kedua',
        ])->assertSessionHasErrors(['code']);
    }

    public function test_destroy_soft_deletes_regional_leader(): void
    {
        $this->actingAsAdministrator();
        $regionalLeader = RegionalLeader::query()->create(['code' => 'RL040', 'name' => 'Hapus Saya']);

        $this->delete(route('admin.regional-leaders.destroy', $regionalLeader))
            ->assertRedirect(route('admin.regional-leaders.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('regional_leaders', ['id' => $regionalLeader->id]);
    }
}

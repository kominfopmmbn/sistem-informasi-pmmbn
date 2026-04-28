<?php

namespace Tests\Feature\Admin;

use App\Models\OrgRegion;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrgRegionCrudTest extends TestCase
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

    public function test_guest_is_redirected_from_org_regions_index_to_admin_login(): void
    {
        $this->get(route('admin.org-regions.index'))
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_guest_cannot_post_to_store_org_regions(): void
    {
        $this->post(route('admin.org-regions.store'), [])
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_index_forbidden_without_org_regions_view_permission(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('admin.org-regions.index'))->assertForbidden();
    }

    public function test_index_shows_org_regions(): void
    {
        $this->actingAsAdministrator();
        OrgRegion::query()->create([
            'name' => 'Wilayah Tes',
            'code' => 'WT',
        ]);

        $this->get(route('admin.org-regions.index'))->assertOk()
            ->assertSee('Wilayah Tes', false)
            ->assertSee('WT', false);
    }

    public function test_create_renders(): void
    {
        $this->actingAsAdministrator();

        $this->get(route('admin.org-regions.create'))->assertOk();
    }

    public function test_store_persists_org_region(): void
    {
        $this->actingAsAdministrator();

        $this->post(route('admin.org-regions.store'), [
            'name' => 'Jawa Barat',
            'code' => 'JBR',
        ])->assertRedirect(route('admin.org-regions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('org_regions', [
            'name' => 'Jawa Barat',
            'code' => 'JBR',
        ]);
    }

    public function test_store_validates_invalid_payload(): void
    {
        $this->actingAsAdministrator();

        $this->post(route('admin.org-regions.store'), [
            'name' => '',
            'code' => '',
        ])->assertSessionHasErrors(['name', 'code']);
    }

    public function test_store_rejects_duplicate_name_or_code(): void
    {
        $this->actingAsAdministrator();
        OrgRegion::query()->create([
            'name' => 'Sudah Ada',
            'code' => 'ADA',
        ]);

        $this->post(route('admin.org-regions.store'), [
            'name' => 'Sudah Ada',
            'code' => 'BARU',
        ])->assertSessionHasErrors(['name']);

        $this->post(route('admin.org-regions.store'), [
            'name' => 'Baru',
            'code' => 'ADA',
        ])->assertSessionHasErrors(['code']);
    }

    public function test_edit_renders(): void
    {
        $this->actingAsAdministrator();
        $region = OrgRegion::query()->create([
            'name' => 'Edit Tes',
            'code' => 'ET',
        ]);

        $this->get(route('admin.org-regions.edit', $region))->assertOk()
            ->assertSee('Edit Tes', false);
    }

    public function test_update_changes_org_region(): void
    {
        $this->actingAsAdministrator();
        $region = OrgRegion::query()->create([
            'name' => 'Lama',
            'code' => 'LAM',
        ]);

        $this->put(route('admin.org-regions.update', $region), [
            'name' => 'Baru',
            'code' => 'BRU',
        ])->assertRedirect(route('admin.org-regions.index'))
            ->assertSessionHas('success');

        $region->refresh();
        $this->assertSame('Baru', $region->name);
        $this->assertSame('BRU', $region->code);
    }

    public function test_update_rejects_duplicate_from_other_row(): void
    {
        $this->actingAsAdministrator();
        OrgRegion::query()->create([
            'name' => 'Pertama',
            'code' => 'P1',
        ]);
        $second = OrgRegion::query()->create([
            'name' => 'Kedua',
            'code' => 'P2',
        ]);

        $this->put(route('admin.org-regions.update', $second), [
            'name' => 'Pertama',
            'code' => 'P2',
        ])->assertSessionHasErrors(['name']);
    }

    public function test_destroy_deletes_org_region(): void
    {
        $this->actingAsAdministrator();
        $region = OrgRegion::query()->create([
            'name' => 'Hapus Saya',
            'code' => 'HS',
        ]);

        $this->delete(route('admin.org-regions.destroy', $region))
            ->assertRedirect(route('admin.org-regions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('org_regions', ['id' => $region->id]);
    }
}

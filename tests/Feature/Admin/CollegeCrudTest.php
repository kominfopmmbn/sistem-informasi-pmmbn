<?php

namespace Tests\Feature\Admin;

use App\Models\College;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CollegeCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
        $this->seed(ProvincesSeeder::class);
        $this->seed(CitiesSeeder::class);
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

    /** @return array{province: Province, city: City} */
    private function sampleProvinceAndCity(): array
    {
        $province = Province::query()->orderBy('id')->firstOrFail();
        $city = City::query()
            ->where('province_code', $province->code)
            ->orderBy('id')
            ->firstOrFail();

        return ['province' => $province, 'city' => $city];
    }

    public function test_guest_is_redirected_from_colleges_index_to_admin_login(): void
    {
        $this->get(route('admin.colleges.index'))
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_guest_cannot_post_to_store_colleges(): void
    {
        $this->post(route('admin.colleges.store'), [])
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_index_forbidden_without_colleges_view_permission(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('admin.colleges.index'))->assertForbidden();
    }

    public function test_index_shows_colleges(): void
    {
        $this->actingAsAdministrator();
        $loc = $this->sampleProvinceAndCity();

        College::query()->create([
            'name' => 'Universitas Tes',
            'province_code' => $loc['province']->code,
            'city_code' => $loc['city']->code,
        ]);

        $this->get(route('admin.colleges.index'))->assertOk()
            ->assertSee('Universitas Tes', false);
    }

    public function test_index_filters_by_q_province_and_city(): void
    {
        $this->actingAsAdministrator();
        $loc = $this->sampleProvinceAndCity();
        $province = $loc['province'];
        $city = $loc['city'];

        College::query()->create([
            'name' => 'Universitas FilterXYZ',
            'province_code' => $province->code,
            'city_code' => $city->code,
        ]);

        $otherProvince = Province::query()->where('id', '!=', $province->getKey())->orderBy('id')->firstOrFail();
        $cityOtherInSameProvince = City::query()
            ->where('province_code', $province->code)
            ->where('id', '!=', $city->getKey())
            ->orderBy('id')
            ->first();

        $this->get(route('admin.colleges.index', ['q' => 'FilterXYZ']))
            ->assertOk()
            ->assertSee('Universitas FilterXYZ', false);

        $this->get(route('admin.colleges.index', ['q' => 'tidak-ada-xyz']))
            ->assertOk()
            ->assertDontSee('Universitas FilterXYZ', false);

        $this->get(route('admin.colleges.index', ['province_code' => $otherProvince->code]))
            ->assertOk()
            ->assertDontSee('Universitas FilterXYZ', false);

        $this->get(route('admin.colleges.index', ['province_code' => $province->code]))
            ->assertOk()
            ->assertSee('Universitas FilterXYZ', false);

        if ($cityOtherInSameProvince !== null) {
            $this->get(route('admin.colleges.index', [
                'province_code' => $province->code,
                'city_code' => $cityOtherInSameProvince->code,
            ]))->assertOk()
                ->assertDontSee('Universitas FilterXYZ', false);
        }

        $this->get(route('admin.colleges.index', [
            'province_code' => $province->code,
            'city_code' => $city->code,
        ]))->assertOk()
            ->assertSee('Universitas FilterXYZ', false);
    }

    public function test_create_renders(): void
    {
        $this->actingAsAdministrator();

        $this->get(route('admin.colleges.create'))->assertOk();
    }

    public function test_store_persists_college(): void
    {
        $this->actingAsAdministrator();
        $loc = $this->sampleProvinceAndCity();

        $this->post(route('admin.colleges.store'), [
            'name' => 'Universitas Contoh',
            'province_code' => $loc['province']->code,
            'city_code' => $loc['city']->code,
        ])->assertRedirect(route('admin.colleges.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('colleges', [
            'name' => 'Universitas Contoh',
            'province_code' => $loc['province']->code,
            'city_code' => $loc['city']->code,
        ]);
    }

    public function test_store_validates_invalid_payload(): void
    {
        $this->actingAsAdministrator();

        $this->post(route('admin.colleges.store'), [
            'name' => '',
            'province_code' => '',
            'city_code' => '',
        ])->assertSessionHasErrors(['name', 'province_code', 'city_code']);
    }

    public function test_store_rejects_duplicate_name(): void
    {
        $this->actingAsAdministrator();
        $loc = $this->sampleProvinceAndCity();

        College::query()->create([
            'name' => 'Sudah Ada',
            'province_code' => $loc['province']->code,
            'city_code' => $loc['city']->code,
        ]);

        $this->post(route('admin.colleges.store'), [
            'name' => 'Sudah Ada',
            'province_code' => $loc['province']->code,
            'city_code' => $loc['city']->code,
        ])->assertSessionHasErrors(['name']);
    }

    public function test_store_rejects_city_not_in_province(): void
    {
        $this->actingAsAdministrator();
        $p1 = Province::query()->orderBy('id')->firstOrFail();
        $p2 = Province::query()->where('id', '!=', $p1->getKey())->orderBy('id')->firstOrFail();
        $cityInP2 = City::query()
            ->where('province_code', $p2->code)
            ->orderBy('id')
            ->firstOrFail();

        $this->post(route('admin.colleges.store'), [
            'name' => 'Salah Provinsi',
            'province_code' => $p1->code,
            'city_code' => $cityInP2->code,
        ])->assertSessionHasErrors(['city_code']);
    }

    public function test_edit_renders(): void
    {
        $this->actingAsAdministrator();
        $loc = $this->sampleProvinceAndCity();
        $college = College::query()->create([
            'name' => 'Edit Tes',
            'province_code' => $loc['province']->code,
            'city_code' => $loc['city']->code,
        ]);

        $this->get(route('admin.colleges.edit', $college))->assertOk()
            ->assertSee('Edit Tes', false);
    }

    public function test_update_changes_college(): void
    {
        $this->actingAsAdministrator();
        $loc = $this->sampleProvinceAndCity();
        $college = College::query()->create([
            'name' => 'Lama',
            'province_code' => $loc['province']->code,
            'city_code' => $loc['city']->code,
        ]);

        $this->put(route('admin.colleges.update', $college), [
            'name' => 'Baru',
            'province_code' => $loc['province']->code,
            'city_code' => $loc['city']->code,
        ])->assertRedirect(route('admin.colleges.index'))
            ->assertSessionHas('success');

        $college->refresh();
        $this->assertSame('Baru', $college->name);
    }

    public function test_update_rejects_duplicate_name_from_other_row(): void
    {
        $this->actingAsAdministrator();
        $loc = $this->sampleProvinceAndCity();

        College::query()->create([
            'name' => 'Pertama',
            'province_code' => $loc['province']->code,
            'city_code' => $loc['city']->code,
        ]);

        $second = College::query()->create([
            'name' => 'Kedua',
            'province_code' => $loc['province']->code,
            'city_code' => $loc['city']->code,
        ]);

        $this->put(route('admin.colleges.update', $second), [
            'name' => 'Pertama',
            'province_code' => $loc['province']->code,
            'city_code' => $loc['city']->code,
        ])->assertSessionHasErrors(['name']);
    }

    public function test_destroy_deletes_college(): void
    {
        $this->actingAsAdministrator();
        $loc = $this->sampleProvinceAndCity();
        $college = College::query()->create([
            'name' => 'Hapus Saya',
            'province_code' => $loc['province']->code,
            'city_code' => $loc['city']->code,
        ]);

        $this->delete(route('admin.colleges.destroy', $college))
            ->assertRedirect(route('admin.colleges.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('colleges', ['id' => $college->id]);
    }
}

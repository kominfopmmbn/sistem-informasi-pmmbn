<?php

namespace Tests\Feature\Admin;

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

class RegionsCrudTest extends TestCase
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

    public function test_guest_is_redirected_from_provinces_index_to_admin_login(): void
    {
        $this->get(route('admin.provinces.index'))
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_provinces_index_forbidden_without_permission(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('admin.provinces.index'))->assertForbidden();
    }

    public function test_provinces_index_ok_for_administrator(): void
    {
        $this->actingAsAdministrator();

        $this->get(route('admin.provinces.index'))
            ->assertOk()
            ->assertViewIs('admin.regions.provinces.index');
    }

    public function test_administrator_can_store_province(): void
    {
        $this->actingAsAdministrator();

        $code = '77';
        Province::query()->where('code', $code)->delete();

        $this->post(route('admin.provinces.store'), [
            'code' => $code,
            'name' => 'Provinsi Tes CRUD',
            'meta' => null,
        ])->assertRedirect(route('admin.provinces.index'));

        $this->assertDatabaseHas('provinces', [
            'code' => $code,
            'name' => 'Provinsi Tes CRUD',
        ]);
    }

    public function test_cities_index_filters_by_province_code(): void
    {
        $this->actingAsAdministrator();

        $province = Province::query()->orderBy('id')->firstOrFail();
        $cityInProvince = City::query()
            ->where('province_code', $province->code)
            ->orderBy('id')
            ->firstOrFail();

        $response = $this->get(route('admin.cities.index', [
            'province_code' => $province->code,
        ]));

        $response->assertOk();
        $response->assertSee($cityInProvince->name, false);
    }

    public function test_lookup_cities_accepts_province_code(): void
    {
        $province = Province::query()->orderBy('id')->firstOrFail();

        $this->get(route('select.cities', [
            'province_code' => $province->code,
            'page' => 1,
        ]))
            ->assertOk()
            ->assertJsonStructure(['results', 'pagination']);
    }

    public function test_lookup_districts_requires_city_code(): void
    {
        $city = City::query()->orderBy('id')->firstOrFail();

        $this->get(route('select.districts', [
            'city_code' => $city->code,
            'page' => 1,
        ]))
            ->assertOk()
            ->assertJsonStructure(['results', 'pagination']);
    }
}

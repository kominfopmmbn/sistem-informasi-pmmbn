<?php

namespace Tests\Feature\Admin;

use App\Models\District;
use App\Models\User;
use App\Models\Village;
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

    public function test_administrator_can_store_province_with_generated_code(): void
    {
        $this->actingAsAdministrator();

        $this->post(route('admin.provinces.store'), [
            'name' => 'Provinsi Tes CRUD',
            'meta' => null,
        ])->assertRedirect(route('admin.provinces.index'));

        $province = Province::query()->where('name', 'Provinsi Tes CRUD')->first();
        $this->assertNotNull($province);
        $this->assertMatchesRegularExpression('/^\d{2}$/', (string) $province->code);
    }

    public function test_store_city_generates_code_from_province(): void
    {
        $this->actingAsAdministrator();

        $province = Province::query()->orderBy('id')->firstOrFail();

        $this->post(route('admin.cities.store'), [
            'province_code' => $province->code,
            'name' => 'Kota Tes CRUD',
            'meta' => null,
        ])->assertRedirect(route('admin.cities.index'));

        $city = City::query()->where('name', 'Kota Tes CRUD')->firstOrFail();
        $this->assertStringStartsWith((string) $province->code, (string) $city->code);
        $this->assertSame(4, strlen((string) $city->code));
    }

    public function test_store_district_generates_code_from_city(): void
    {
        $this->actingAsAdministrator();

        $city = City::query()->orderBy('id')->firstOrFail();

        $this->post(route('admin.districts.store'), [
            'city_code' => $city->code,
            'name' => 'Kecamatan Tes CRUD',
            'meta' => null,
        ])->assertRedirect(route('admin.districts.index'));

        $district = District::query()->where('name', 'Kecamatan Tes CRUD')->firstOrFail();
        $this->assertStringStartsWith((string) $city->code, (string) $district->code);
        $this->assertSame(6, strlen((string) $district->code));
    }

    public function test_store_village_generates_code_from_district(): void
    {
        $this->actingAsAdministrator();

        $city = City::query()->orderBy('id')->firstOrFail();

        // Kecamatan belum di-seed; buat lewat endpoint agar kodenya ter-generate.
        $this->post(route('admin.districts.store'), [
            'city_code' => $city->code,
            'name' => 'Kecamatan Induk',
            'meta' => null,
        ])->assertRedirect(route('admin.districts.index'));
        $district = District::query()->where('name', 'Kecamatan Induk')->firstOrFail();

        $this->post(route('admin.villages.store'), [
            'district_code' => $district->code,
            'name' => 'Desa Tes CRUD',
            'meta' => null,
        ])->assertRedirect(route('admin.villages.index'));

        $village = Village::query()->where('name', 'Desa Tes CRUD')->firstOrFail();
        $this->assertStringStartsWith((string) $district->code, (string) $village->code);
        $this->assertSame(10, strlen((string) $village->code));
    }

    public function test_update_city_regenerates_code_when_province_changes(): void
    {
        $this->actingAsAdministrator();

        $city = City::query()->orderBy('id')->firstOrFail();
        $otherProvince = Province::query()
            ->where('code', '!=', $city->province_code)
            ->orderBy('id')
            ->firstOrFail();

        $this->put(route('admin.cities.update', $city), [
            'province_code' => $otherProvince->code,
            'name' => $city->name,
            'meta' => null,
        ])->assertRedirect(route('admin.cities.index'));

        $city->refresh();
        $this->assertSame((string) $otherProvince->code, (string) $city->province_code);
        $this->assertStringStartsWith((string) $otherProvince->code, (string) $city->code);
    }

    public function test_update_city_cascades_codes_to_districts_and_villages(): void
    {
        $this->actingAsAdministrator();

        // Bangun rantai kota → kecamatan → desa lewat endpoint agar kode ter-generate.
        $province = Province::query()->orderBy('id')->firstOrFail();
        $this->post(route('admin.cities.store'), [
            'province_code' => $province->code,
            'name' => 'Kota Induk',
            'meta' => null,
        ])->assertRedirect(route('admin.cities.index'));
        $city = City::query()->where('name', 'Kota Induk')->firstOrFail();

        $this->post(route('admin.districts.store'), [
            'city_code' => $city->code,
            'name' => 'Kecamatan Induk',
            'meta' => null,
        ])->assertRedirect(route('admin.districts.index'));
        $district = District::query()->where('name', 'Kecamatan Induk')->firstOrFail();

        $this->post(route('admin.villages.store'), [
            'district_code' => $district->code,
            'name' => 'Desa Induk',
            'meta' => null,
        ])->assertRedirect(route('admin.villages.index'));
        $village = Village::query()->where('name', 'Desa Induk')->firstOrFail();

        $oldDistrictSuffix = substr((string) $district->code, strlen((string) $city->code));
        $oldVillageSuffix = substr((string) $village->code, strlen((string) $district->code));

        // Pindahkan kota ke provinsi lain → kode kota + seluruh turunan harus ganti prefix.
        $otherProvince = Province::query()
            ->where('code', '!=', $city->province_code)
            ->orderBy('id')
            ->firstOrFail();

        $this->put(route('admin.cities.update', $city), [
            'province_code' => $otherProvince->code,
            'name' => $city->name,
            'meta' => null,
        ])->assertRedirect(route('admin.cities.index'));

        $city->refresh();
        $district->refresh();
        $village->refresh();

        $this->assertStringStartsWith((string) $otherProvince->code, (string) $city->code);
        // Kecamatan: FK ikut cascade DB + kode sendiri di-recode mengikuti kota baru.
        $this->assertSame((string) $city->code, (string) $district->city_code);
        $this->assertSame($city->code.$oldDistrictSuffix, (string) $district->code);
        // Desa: FK ikut cascade DB + kode sendiri di-recode mengikuti kecamatan baru.
        $this->assertSame((string) $district->code, (string) $village->district_code);
        $this->assertSame($district->code.$oldVillageSuffix, (string) $village->code);
    }

    public function test_update_district_cascades_codes_to_villages(): void
    {
        $this->actingAsAdministrator();

        $province = Province::query()->orderBy('id')->firstOrFail();

        // Dua kota di provinsi yang sama sebagai asal & tujuan pindah kecamatan.
        $this->post(route('admin.cities.store'), [
            'province_code' => $province->code, 'name' => 'Kota Asal', 'meta' => null,
        ])->assertRedirect(route('admin.cities.index'));
        $cityFrom = City::query()->where('name', 'Kota Asal')->firstOrFail();

        $this->post(route('admin.cities.store'), [
            'province_code' => $province->code, 'name' => 'Kota Tujuan', 'meta' => null,
        ])->assertRedirect(route('admin.cities.index'));
        $cityTo = City::query()->where('name', 'Kota Tujuan')->firstOrFail();

        $this->post(route('admin.districts.store'), [
            'city_code' => $cityFrom->code, 'name' => 'Kecamatan Pindah', 'meta' => null,
        ])->assertRedirect(route('admin.districts.index'));
        $district = District::query()->where('name', 'Kecamatan Pindah')->firstOrFail();

        $this->post(route('admin.villages.store'), [
            'district_code' => $district->code, 'name' => 'Desa Pindah', 'meta' => null,
        ])->assertRedirect(route('admin.villages.index'));
        $village = Village::query()->where('name', 'Desa Pindah')->firstOrFail();

        $oldVillageSuffix = substr((string) $village->code, strlen((string) $district->code));

        $this->put(route('admin.districts.update', $district), [
            'city_code' => $cityTo->code,
            'name' => $district->name,
            'meta' => null,
        ])->assertRedirect(route('admin.districts.index'));

        $district->refresh();
        $village->refresh();

        $this->assertStringStartsWith((string) $cityTo->code, (string) $district->code);
        $this->assertSame((string) $district->code, (string) $village->district_code);
        $this->assertSame($district->code.$oldVillageSuffix, (string) $village->code);
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

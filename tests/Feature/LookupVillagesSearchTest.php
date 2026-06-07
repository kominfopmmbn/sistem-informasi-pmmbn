<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;
use Tests\TestCase;

/** Endpoint pencarian desa/kelurahan rata (tanpa kecamatan induk) untuk select domisili: cari per nama ATAU kode pos. */
class LookupVillagesSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProvincesSeeder::class);
        $this->seed(CitiesSeeder::class);
    }

    private function sampleCity(): City
    {
        $province = Province::query()->orderBy('id')->firstOrFail();

        return City::query()
            ->where('province_code', $province->code)
            ->orderBy('id')
            ->firstOrFail();
    }

    /**
     * Buat satu kecamatan + dua desa dengan kode pos diketahui (kode pos disimpan di kolom `meta` JSON, key `pos`).
     *
     * @return array{0: Village, 1: Village}
     */
    private function seedVillages(): array
    {
        $city = $this->sampleCity();

        $district = District::query()->create([
            'code' => $city->code.'001',
            'city_code' => $city->code,
            'name' => 'KECAMATAN TES',
        ]);

        // `meta` di-cast `array` oleh model laravolt → kirim array (encode sekali); kirim string akan double-encode dan merusak json_extract.
        $mawar = Village::query()->create([
            'code' => $district->code.'001',
            'district_code' => $district->code,
            'name' => 'MAWAR',
            'meta' => ['lat' => '-6.1', 'long' => '106.1', 'pos' => '40123'],
        ]);

        $melati = Village::query()->create([
            'code' => $district->code.'002',
            'district_code' => $district->code,
            'name' => 'MELATI',
            'meta' => ['lat' => '-6.2', 'long' => '106.2', 'pos' => '50456'],
        ]);

        return [$mawar, $melati];
    }

    public function test_returns_select2_json_structure(): void
    {
        $this->seedVillages();

        $response = $this->getJson(route('select.villages-search'));

        $response->assertOk();
        $response->assertJsonStructure([
            'results' => [
                ['id', 'text', 'code'],
            ],
            'pagination' => ['more'],
        ]);
        $this->assertIsBool($response->json('pagination.more'));
    }

    public function test_search_by_name(): void
    {
        [$mawar] = $this->seedVillages();

        $response = $this->getJson(route('select.villages-search', ['q' => 'MAW']));

        $response->assertOk();
        $ids = collect($response->json('results'))->pluck('id')->all();
        $this->assertContains($mawar->code, $ids);
    }

    public function test_search_by_postal_code(): void
    {
        [$mawar, $melati] = $this->seedVillages();

        $response = $this->getJson(route('select.villages-search', ['q' => '40123']));

        $response->assertOk();
        $ids = collect($response->json('results'))->pluck('id')->all();
        $this->assertContains($mawar->code, $ids);
        $this->assertNotContains($melati->code, $ids);
    }

    public function test_result_label_includes_postal_code(): void
    {
        [$mawar] = $this->seedVillages();

        $response = $this->getJson(route('select.villages-search', ['q' => 'MAWAR']));

        $row = collect($response->json('results'))->firstWhere('id', $mawar->code);
        $this->assertNotNull($row);
        // Format label: "{kode pos} - {kelurahan} - {kecamatan} - {kabupaten} - {provinsi}".
        $this->assertStringStartsWith('40123 - MAWAR - KECAMATAN TES - ', $row['text']);
    }

    public function test_rejects_invalid_q(): void
    {
        $response = $this->getJson(route('select.villages-search', ['q' => str_repeat('a', 101)]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['q']);
    }
}

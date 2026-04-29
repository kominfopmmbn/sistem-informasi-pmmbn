<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravolt\Indonesia\Models\Village;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Laravolt\Indonesia\Seeds\DistrictsSeeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;
use Laravolt\Indonesia\Seeds\VillagesSeeder;
use Tests\TestCase;

class LookupVillagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProvincesSeeder::class);
        $this->seed(CitiesSeeder::class);
        $this->seed(DistrictsSeeder::class);
        $this->seed(VillagesSeeder::class);
    }

    private function sampleVillage(): Village
    {
        return Village::query()->orderBy('id')->firstOrFail();
    }

    public function test_select_villages_returns_json_for_valid_district(): void
    {
        $village = $this->sampleVillage();

        $response = $this->getJson(route('select.villages', [
            'district_code' => rtrim((string) $village->district_code),
        ]));

        $response->assertOk();
        $response->assertJsonStructure([
            'results' => [
                ['id', 'text'],
            ],
            'pagination' => ['more'],
        ]);

        $ids = collect($response->json('results'))->pluck('id')->all();
        $this->assertContains($village->code, $ids);
        $this->assertIsBool($response->json('pagination.more'));
    }

    public function test_select_villages_filters_by_q(): void
    {
        $village = $this->sampleVillage();

        $response = $this->getJson(route('select.villages', [
            'district_code' => rtrim((string) $village->district_code),
            'q' => mb_substr($village->name, 0, 3),
        ]));

        $response->assertOk();
        $ids = collect($response->json('results'))->pluck('id')->all();
        $this->assertContains($village->code, $ids);
    }

    public function test_select_villages_requires_district_code(): void
    {
        $response = $this->getJson(route('select.villages'));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['district_code']);
    }

    public function test_select_villages_rejects_unknown_district_code(): void
    {
        $response = $this->getJson(route('select.villages', [
            'district_code' => '9999999',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['district_code']);
    }

    public function test_select_villages_rejects_invalid_q(): void
    {
        $village = $this->sampleVillage();

        $response = $this->getJson(route('select.villages', [
            'district_code' => rtrim((string) $village->district_code),
            'q' => str_repeat('a', 101),
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['q']);
    }
}

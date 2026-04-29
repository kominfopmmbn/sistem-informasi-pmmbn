<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;
use Tests\TestCase;

class IndonesiaLookupCitiesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProvincesSeeder::class);
        $this->seed(CitiesSeeder::class);
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

    public function test_select_cities_returns_json_for_valid_province(): void
    {
        ['province' => $province, 'city' => $city] = $this->sampleProvinceAndCity();

        $response = $this->getJson(route('indonesia.select.cities', [
            'province_id' => $province->id,
        ]));

        $response->assertOk();
        $response->assertJsonStructure([
            'results' => [
                ['id', 'text'],
            ],
            'pagination' => ['more'],
        ]);

        $ids = collect($response->json('results'))->pluck('id')->all();
        $this->assertContains($city->id, $ids);
        $this->assertIsBool($response->json('pagination.more'));
    }

    public function test_select_cities_accepts_q_as_search_alias(): void
    {
        ['province' => $province, 'city' => $city] = $this->sampleProvinceAndCity();

        $response = $this->getJson(route('indonesia.select.cities', [
            'province_id' => $province->id,
            'q' => mb_substr($city->name, 0, 3),
        ]));

        $response->assertOk();
        $ids = collect($response->json('results'))->pluck('id')->all();
        $this->assertContains($city->id, $ids);
    }

    public function test_select_cities_requires_province_id(): void
    {
        $response = $this->getJson(route('indonesia.select.cities'));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['province_id']);
    }

    public function test_select_cities_rejects_unknown_province_id(): void
    {
        $response = $this->getJson(route('indonesia.select.cities', [
            'province_id' => 999_999,
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['province_id']);
    }

    public function test_select_cities_filters_by_term(): void
    {
        ['province' => $province, 'city' => $city] = $this->sampleProvinceAndCity();

        $response = $this->getJson(route('indonesia.select.cities', [
            'province_id' => $province->id,
            'term' => mb_substr($city->name, 0, 3),
        ]));

        $response->assertOk();
        $ids = collect($response->json('results'))->pluck('id')->all();
        $this->assertContains($city->id, $ids);
    }
}

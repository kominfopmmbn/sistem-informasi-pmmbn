<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Laravolt\Indonesia\Seeds\DistrictsSeeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;
use Tests\TestCase;

class LookupDistrictsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProvincesSeeder::class);
        $this->seed(CitiesSeeder::class);
        $this->seed(DistrictsSeeder::class);
    }

    /** @return array{city: City, district: District} */
    private function sampleCityAndDistrict(): array
    {
        $district = District::query()->orderBy('id')->firstOrFail();
        $city = City::query()
            ->where('code', $district->city_code)
            ->firstOrFail();

        return ['city' => $city, 'district' => $district];
    }

    public function test_select_districts_returns_json_for_valid_city(): void
    {
        ['city' => $city, 'district' => $district] = $this->sampleCityAndDistrict();

        $response = $this->getJson(route('select.districts', [
            'city_code' => $city->code,
        ]));

        $response->assertOk();
        $response->assertJsonStructure([
            'results' => [
                ['id', 'text'],
            ],
            'pagination' => ['more'],
        ]);

        $ids = collect($response->json('results'))->pluck('id')->all();
        $this->assertContains($district->code, $ids);
        $this->assertIsBool($response->json('pagination.more'));
    }

    public function test_select_districts_filters_by_q(): void
    {
        ['city' => $city, 'district' => $district] = $this->sampleCityAndDistrict();

        $response = $this->getJson(route('select.districts', [
            'city_code' => $city->code,
            'q' => mb_substr($district->name, 0, 3),
        ]));

        $response->assertOk();
        $ids = collect($response->json('results'))->pluck('id')->all();
        $this->assertContains($district->code, $ids);
    }

    public function test_select_districts_requires_city_code(): void
    {
        $response = $this->getJson(route('select.districts'));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['city_code']);
    }

    public function test_select_districts_rejects_unknown_city_code(): void
    {
        $response = $this->getJson(route('select.districts', [
            'city_code' => 'ZZZZ',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['city_code']);
    }

    public function test_select_districts_rejects_invalid_q(): void
    {
        ['city' => $city] = $this->sampleCityAndDistrict();

        $response = $this->getJson(route('select.districts', [
            'city_code' => $city->code,
            'q' => str_repeat('a', 101),
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['q']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\College;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;
use Tests\TestCase;

class LookupCollegesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProvincesSeeder::class);
        $this->seed(CitiesSeeder::class);
    }

    /** @return array{province: Province, city: City, college: College} */
    private function sampleCollege(): array
    {
        $province = Province::query()->orderBy('id')->firstOrFail();
        $city = City::query()
            ->where('province_code', $province->code)
            ->orderBy('id')
            ->firstOrFail();

        $college = College::query()->create([
            'name' => 'Universitas Lookup Tes',
            'province_code' => $province->code,
            'city_code' => $city->code,
            'lat' => -6.3612,
            'long' => 106.8268,
        ]);

        return ['province' => $province, 'city' => $city, 'college' => $college];
    }

    public function test_select_colleges_returns_json(): void
    {
        ['college' => $college] = $this->sampleCollege();

        $response = $this->getJson(route('select.colleges'));

        $response->assertOk();
        $response->assertJsonStructure([
            'results' => [
                ['id', 'text'],
            ],
            'pagination' => ['more'],
        ]);

        $ids = collect($response->json('results'))->pluck('id')->all();
        $this->assertContains($college->id, $ids);
        $texts = collect($response->json('results'))->pluck('text', 'id');
        $this->assertSame($college->name, $texts[$college->id]);
        $this->assertIsBool($response->json('pagination.more'));
    }

    public function test_select_colleges_filters_by_q(): void
    {
        ['college' => $college] = $this->sampleCollege();

        $response = $this->getJson(route('select.colleges', [
            'q' => 'Lookup Tes',
        ]));

        $response->assertOk();
        $ids = collect($response->json('results'))->pluck('id')->all();
        $this->assertContains($college->id, $ids);
    }

    public function test_select_colleges_rejects_invalid_q(): void
    {
        $response = $this->getJson(route('select.colleges', [
            'q' => str_repeat('a', 101),
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['q']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\College;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;
use Tests\TestCase;

class HomeMapSectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProvincesSeeder::class);
        $this->seed(CitiesSeeder::class);
    }

    private function makeCollege(Province $province, string $name): College
    {
        $city = City::query()
            ->where('province_code', $province->code)
            ->orderBy('id')
            ->firstOrFail();

        return College::query()->create([
            'name' => $name,
            'province_code' => $province->code,
            'city_code' => $city->code,
            'lat' => -6.3612,
            'long' => 106.8268,
        ]);
    }

    public function test_home_map_data_is_built_from_colleges(): void
    {
        $provinces = Province::query()->orderBy('id')->take(2)->get();

        // Dua kampus di provinsi[0] berbagi kota yang sama; satu kampus di provinsi[1].
        $this->makeCollege($provinces[0], 'Universitas Peta Satu');
        $this->makeCollege($provinces[0], 'Universitas Peta Dua');
        $this->makeCollege($provinces[1], 'Universitas Peta Tiga');

        Member::query()->create(['full_name' => 'Anggota Satu']);
        Member::query()->create(['full_name' => 'Anggota Dua']);

        $response = $this->get(route('home.index'));

        $response->assertOk();
        $response->assertViewHas('provinceCount', 2);
        $response->assertViewHas('collegeCount', 3);
        $response->assertViewHas('cityCount', 2);   // dua kota unik (satu per provinsi)
        $response->assertViewHas('memberCount', 2);

        $campusData = $response->viewData('campusData');
        $this->assertCount(2, $campusData);
        $this->assertSame(3, array_sum(array_map(fn ($p) => count($p['campuses']), $campusData)));
    }

    public function test_home_stats_are_zero_when_empty(): void
    {
        $response = $this->get(route('home.index'));

        $response->assertOk();
        $response->assertViewHas('provinceCount', 0);
        $response->assertViewHas('collegeCount', 0);
        $response->assertViewHas('cityCount', 0);
        $response->assertViewHas('memberCount', 0);
    }

    public function test_college_cache_is_reset_when_college_data_changes(): void
    {
        $province = Province::query()->orderBy('id')->firstOrFail();

        $this->makeCollege($province, 'Universitas Peta Satu');
        $this->get(route('home.index'))->assertViewHas('collegeCount', 1);

        // Kampus baru harus tercermin di kunjungan berikutnya (cache ter-reset via model event).
        $this->makeCollege($province, 'Universitas Peta Dua');
        $this->get(route('home.index'))->assertViewHas('collegeCount', 2);
    }

    public function test_home_data_is_served_from_cache_on_repeat_visit(): void
    {
        $province = Province::query()->orderBy('id')->firstOrFail();
        $this->makeCollege($province, 'Universitas Cache');
        Member::query()->create(['full_name' => 'Anggota Cache']);

        $this->get(route('home.index'))->assertOk();

        // Kunjungan pertama mengisi cache (via helper rememberHomeData + lock).
        $this->assertTrue(Cache::has(College::HOME_COLLEGES_CACHE_KEY));
        $this->assertTrue(Cache::has(Member::HOME_MEMBER_COUNT_CACHE_KEY));

        $this->get(route('home.index'))
            ->assertViewHas('collegeCount', 1)
            ->assertViewHas('memberCount', 1);
    }

    public function test_member_count_cache_is_reset_when_member_data_changes(): void
    {
        $this->get(route('home.index'))->assertViewHas('memberCount', 0);

        $first = Member::query()->create(['full_name' => 'Anggota Satu']);
        Member::query()->create(['full_name' => 'Anggota Dua']);
        $this->get(route('home.index'))->assertViewHas('memberCount', 2);

        // Soft delete mengurangi count → cache harus ter-reset.
        $first->delete();
        $this->get(route('home.index'))->assertViewHas('memberCount', 1);
    }
}

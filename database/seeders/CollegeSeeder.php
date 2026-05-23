<?php

namespace Database\Seeders;

use App\Models\College;
use Illuminate\Database\Seeder;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;

class CollegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colleges = [
            ['name' => 'Universitas Indonesia', 'province' => 'JAWA BARAT', 'city' => 'DEPOK'],
            ['name' => 'Institut Teknologi Bandung', 'province' => 'JAWA BARAT', 'city' => 'BANDUNG'],
            ['name' => 'Universitas Gadjah Mada', 'province' => 'YOGYAKARTA', 'city' => 'YOGYAKARTA'],
            ['name' => 'Universitas Airlangga', 'province' => 'JAWA TIMUR', 'city' => 'SURABAYA'],
            ['name' => 'Universitas Brawijaya', 'province' => 'JAWA TIMUR', 'city' => 'MALANG'],
            ['name' => 'Institut Teknologi Sepuluh Nopember', 'province' => 'JAWA TIMUR', 'city' => 'SURABAYA'],
            ['name' => 'Universitas Diponegoro', 'province' => 'JAWA TENGAH', 'city' => 'SEMARANG'],
            ['name' => 'Universitas Padjadjaran', 'province' => 'JAWA BARAT', 'city' => 'BANDUNG'],
            ['name' => 'Universitas Hasanuddin', 'province' => 'SULAWESI SELATAN', 'city' => 'MAKASSAR'],
            ['name' => 'Universitas Sumatera Utara', 'province' => 'SUMATERA UTARA', 'city' => 'MEDAN'],
            ['name' => 'Universitas Andalas', 'province' => 'SUMATERA BARAT', 'city' => 'PADANG'],
            ['name' => 'Universitas Lambung Mangkurat', 'province' => 'KALIMANTAN SELATAN', 'city' => 'BANJARMASIN'],
            ['name' => 'Universitas Mulawarman', 'province' => 'KALIMANTAN TIMUR', 'city' => 'SAMARINDA'],
            ['name' => 'Universitas Udayana', 'province' => 'BALI', 'city' => 'DENPASAR'],
            ['name' => 'Universitas Negeri Malang', 'province' => 'JAWA TIMUR', 'city' => 'MALANG'],
        ];

        foreach ($colleges as $row) {
            $location = $this->resolveLocation($row['province'], $row['city']);

            College::updateOrCreate(
                ['name' => $row['name']],
                $location
            );
        }
    }

    /** @return array{province_code: string, city_code: string} */
    private function resolveLocation(string $provinceFragment, string $cityFragment): array
    {
        $province = Province::query()
            ->where('name', 'like', '%'.$provinceFragment.'%')
            ->orderBy('id')
            ->firstOrFail();

        $city = City::query()
            ->where('province_code', $province->code)
            ->where('name', 'like', '%'.$cityFragment.'%')
            ->orderBy('id')
            ->firstOrFail();

        return [
            'province_code' => $province->code,
            'city_code' => $city->code,
        ];
    }
}

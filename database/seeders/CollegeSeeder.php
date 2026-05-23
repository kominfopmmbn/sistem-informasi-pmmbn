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
            ['name' => 'Universitas Indonesia', 'province' => 'JAWA BARAT', 'city' => 'DEPOK', 'lat' => -6.3612, 'long' => 106.8268],
            ['name' => 'Institut Teknologi Bandung', 'province' => 'JAWA BARAT', 'city' => 'BANDUNG', 'lat' => -6.8915, 'long' => 107.6107],
            ['name' => 'Universitas Gadjah Mada', 'province' => 'YOGYAKARTA', 'city' => 'YOGYAKARTA', 'lat' => -7.7717, 'long' => 110.3774],
            ['name' => 'Universitas Airlangga', 'province' => 'JAWA TIMUR', 'city' => 'SURABAYA', 'lat' => -7.2804, 'long' => 112.7965],
            ['name' => 'Universitas Brawijaya', 'province' => 'JAWA TIMUR', 'city' => 'MALANG', 'lat' => -7.9528, 'long' => 112.6138],
            ['name' => 'Institut Teknologi Sepuluh Nopember', 'province' => 'JAWA TIMUR', 'city' => 'SURABAYA', 'lat' => -7.2816, 'long' => 112.7951],
            ['name' => 'Universitas Diponegoro', 'province' => 'JAWA TENGAH', 'city' => 'SEMARANG', 'lat' => -7.0515, 'long' => 110.4381],
            ['name' => 'Universitas Padjadjaran', 'province' => 'JAWA BARAT', 'city' => 'BANDUNG', 'lat' => -6.9218, 'long' => 107.7706],
            ['name' => 'Universitas Hasanuddin', 'province' => 'SULAWESI SELATAN', 'city' => 'MAKASSAR', 'lat' => -5.1337, 'long' => 119.4880],
            ['name' => 'Universitas Sumatera Utara', 'province' => 'SUMATERA UTARA', 'city' => 'MEDAN', 'lat' => 3.5648, 'long' => 98.6785],
            ['name' => 'Universitas Andalas', 'province' => 'SUMATERA BARAT', 'city' => 'PADANG', 'lat' => -0.9143, 'long' => 100.3543],
            ['name' => 'Universitas Lambung Mangkurat', 'province' => 'KALIMANTAN SELATAN', 'city' => 'BANJARMASIN', 'lat' => -3.3194, 'long' => 114.5906],
            ['name' => 'Universitas Mulawarman', 'province' => 'KALIMANTAN TIMUR', 'city' => 'SAMARINDA', 'lat' => -0.4646, 'long' => 117.1481],
            ['name' => 'Universitas Udayana', 'province' => 'BALI', 'city' => 'DENPASAR', 'lat' => -8.6705, 'long' => 115.2126],
            ['name' => 'Universitas Negeri Malang', 'province' => 'JAWA TIMUR', 'city' => 'MALANG', 'lat' => -7.9694, 'long' => 112.6323],
        ];

        foreach ($colleges as $row) {
            $location = $this->resolveLocation($row['province'], $row['city']);

            College::updateOrCreate(
                ['name' => $row['name']],
                array_merge($location, [
                    'lat' => $row['lat'],
                    'long' => $row['long'],
                ])
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

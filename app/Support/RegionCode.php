<?php

namespace App\Support;

use App\Models\City;
use App\Models\District;
use App\Models\Province;
use App\Models\Village;

/**
 * Generator kode wilayah (Kemendagri) berdasarkan formula hirarki:
 * provinsi (2) → kota (4) → kecamatan (6) → desa (10). Setiap kode anak =
 * kode induk sebagai prefix + urutan berikutnya yang di-zero-pad.
 */
class RegionCode
{
    /** Kode provinsi baru: max(kode) + 1, 2 digit. */
    public static function nextProvinceCode(): string
    {
        $max = Province::query()->pluck('code')->map(static fn ($code) => (int) $code)->max() ?? 0;

        return str_pad((string) ($max + 1), 2, '0', STR_PAD_LEFT);
    }

    /** Kode kota/kabupaten baru di dalam sebuah provinsi (provinsi + 2 digit). */
    public static function nextCityCode(string $provinceCode): string
    {
        return self::nextChildCode(
            codes: City::query()->where('province_code', $provinceCode)->pluck('code'),
            parentCode: $provinceCode,
            suffixWidth: 2,
        );
    }

    /** Kode kecamatan baru di dalam sebuah kota/kabupaten (kota + 2 digit). */
    public static function nextDistrictCode(string $cityCode): string
    {
        return self::nextChildCode(
            codes: District::query()->where('city_code', $cityCode)->pluck('code'),
            parentCode: $cityCode,
            suffixWidth: 2,
        );
    }

    /** Kode desa/kelurahan baru di dalam sebuah kecamatan (kecamatan + 4 digit). */
    public static function nextVillageCode(string $districtCode): string
    {
        return self::nextChildCode(
            codes: Village::query()->where('district_code', $districtCode)->pluck('code'),
            parentCode: $districtCode,
            suffixWidth: 4,
        );
    }

    /**
     * Ubah kode kota lalu samakan (prefix-swap) kode seluruh turunannya (kecamatan & desa).
     * Kolom FK anak (city_code/district_code) sudah di-cascade DB via onUpdate; di sini hanya
     * kolom `code` yang dihitung ulang. Panggil di dalam DB::transaction.
     */
    public static function recodeCity(City $city, string $newCityCode): void
    {
        $oldCityCode = (string) $city->code;
        $districts = District::query()->where('city_code', $oldCityCode)->get();

        $city->update(['code' => $newCityCode, 'updated_at' => now()]);

        foreach ($districts as $district) {
            $newDistrictCode = $newCityCode.substr((string) $district->code, strlen($oldCityCode));
            self::recodeDistrict(district: $district, newDistrictCode: $newDistrictCode);
        }
    }

    /**
     * Ubah kode kecamatan lalu samakan (prefix-swap) kode desa turunannya.
     * Panggil di dalam DB::transaction.
     */
    public static function recodeDistrict(District $district, string $newDistrictCode): void
    {
        $oldDistrictCode = (string) $district->code;
        $villages = Village::query()->where('district_code', $oldDistrictCode)->get();

        $district->update(['code' => $newDistrictCode, 'updated_at' => now()]);

        foreach ($villages as $village) {
            $newVillageCode = $newDistrictCode.substr((string) $village->code, strlen($oldDistrictCode));
            $village->update(['code' => $newVillageCode, 'updated_at' => now()]);
        }
    }

    /**
     * Ambil suffix numerik setelah prefix induk dari tiap kode anak, cari max, tambah 1,
     * lalu zero-pad selebar suffixWidth dan tempelkan di belakang kode induk.
     *
     * @param \Illuminate\Support\Collection<int, string> $codes
     */
    private static function nextChildCode($codes, string $parentCode, int $suffixWidth): string
    {
        $prefixLength = strlen($parentCode);
        $max = $codes->map(static fn ($code) => (int) substr((string) $code, $prefixLength))->max() ?? 0;

        return $parentCode.str_pad((string) ($max + 1), $suffixWidth, '0', STR_PAD_LEFT);
    }
}

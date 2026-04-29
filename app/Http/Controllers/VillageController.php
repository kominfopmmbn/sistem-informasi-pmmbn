<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVillageRequest;
use App\Http\Requests\UpdateVillageRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\City;
use App\Models\District;
use App\Models\Province;
use App\Models\Village;

class VillageController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'province_code' => ['nullable', 'string', 'exists:provinces,code'],
            'city_code' => ['nullable', 'string', 'exists:cities,code'],
            'district_code' => ['nullable', 'string', 'exists:districts,code'],
        ]);

        $q = isset($filters['q']) ? trim((string) $filters['q']) : '';
        $provinceCode = isset($filters['province_code']) ? (string) $filters['province_code'] : null;
        $cityCode = isset($filters['city_code']) ? (string) $filters['city_code'] : null;
        $districtCode = isset($filters['district_code']) ? (string) $filters['district_code'] : null;

        [$provinceCode, $cityCode, $districtCode] = $this->normalizeVillageFilters(
            $provinceCode !== '' ? $provinceCode : null,
            $cityCode !== '' ? $cityCode : null,
            $districtCode !== '' ? $districtCode : null,
        );

        $query = Village::query()
            ->with(['district.city.province'])
            ->orderBy('name');

        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where('name', 'like', $like);
        }

        if ($districtCode !== null) {
            $query->where('district_code', $districtCode);
        } elseif ($cityCode !== null) {
            $query->whereHas('district', static fn ($q) => $q->where('city_code', $cityCode));
        } elseif ($provinceCode !== null) {
            $query->whereHas('district.city', static fn ($q) => $q->where('province_code', $provinceCode));
        }

        $villages = $query->paginate(15)->withQueryString();

        $provinces = Province::query()->orderBy('name')->get();

        $filterState = [
            'q' => $q,
            'province_code' => $provinceCode,
            'city_code' => $cityCode,
            'district_code' => $districtCode,
        ];

        $filterCityName = '';
        if ($filterState['city_code'] !== null) {
            $filterCityName = City::query()->where('code', $filterState['city_code'])->value('name') ?? '';
        }

        $filterDistrictName = '';
        if ($filterState['district_code'] !== null) {
            $filterDistrictName = District::query()->where('code', $filterState['district_code'])->value('name') ?? '';
        }

        return view('admin.regions.villages.index', compact(
            'villages',
            'provinces',
            'filterState',
            'filterCityName',
            'filterDistrictName',
        ));
    }

    public function create(): View
    {
        $provinces = Province::query()->orderBy('name')->get();

        return view('admin.regions.villages.create', compact('provinces'));
    }

    public function store(StoreVillageRequest $request): RedirectResponse
    {
        Village::query()->create($this->withTimestamps($request->validated(), true));

        return redirect()
            ->route('admin.villages.index')
            ->with('success', 'Desa/kelurahan berhasil ditambahkan.');
    }

    public function edit(Village $village): View
    {
        $village->load(['district.city.province']);
        $provinces = Province::query()->orderBy('name')->get();

        return view('admin.regions.villages.edit', [
            'village' => $village,
            'provinces' => $provinces,
        ]);
    }

    public function update(UpdateVillageRequest $request, Village $village): RedirectResponse
    {
        $village->update($this->withTimestamps($request->validated(), false));

        return redirect()
            ->route('admin.villages.index')
            ->with('success', 'Desa/kelurahan berhasil diperbarui.');
    }

    public function destroy(Village $village): RedirectResponse
    {
        $village->delete();

        return redirect()
            ->route('admin.villages.index')
            ->with('success', 'Desa/kelurahan berhasil dihapus.');
    }

    /** Samakan kombinasi provinsi / kota / kecamatan; hilangkan filter turunan yang tidak konsisten. */
    private function normalizeVillageFilters(?string $provinceCode, ?string $cityCode, ?string $districtCode): array
    {
        if ($provinceCode !== null && $cityCode !== null) {
            if (! City::query()->where('code', $cityCode)->where('province_code', $provinceCode)->exists()) {
                $cityCode = null;
                $districtCode = null;
            }
        }

        if ($cityCode !== null && $districtCode !== null) {
            if (! District::query()->where('code', $districtCode)->where('city_code', $cityCode)->exists()) {
                $districtCode = null;
            }
        }

        if ($provinceCode !== null && $cityCode === null && $districtCode !== null) {
            $district = District::query()->where('code', $districtCode)->with('city')->first();
            if ($district === null || $district->city === null || $district->city->province_code !== $provinceCode) {
                $districtCode = null;
            }
        }

        if ($provinceCode === null && $cityCode !== null) {
            $city = City::query()->where('code', $cityCode)->first();
            $provinceCode = $city?->province_code;
        }

        if ($cityCode === null && $districtCode !== null) {
            $district = District::query()->where('code', $districtCode)->with('city')->first();
            $cityCode = $district?->city_code;
            if ($provinceCode === null && $district?->city !== null) {
                $provinceCode = $district->city->province_code;
            }
        }

        return [$provinceCode, $cityCode, $districtCode];
    }

    private function withTimestamps(array $data, bool $isCreate): array
    {
        $now = now();
        if ($isCreate) {
            return array_merge($data, ['created_at' => $now, 'updated_at' => $now]);
        }

        return array_merge($data, ['updated_at' => $now]);
    }
}

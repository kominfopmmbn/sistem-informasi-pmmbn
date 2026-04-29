<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDistrictRequest;
use App\Http\Requests\UpdateDistrictRequest;
use App\Models\District;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\City;
use App\Models\Province;

class DistrictController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'province_code' => ['nullable', 'string', 'size:2', 'exists:provinces,code'],
            'city_code' => ['nullable', 'string', 'size:4', 'exists:cities,code'],
        ]);

        $q = isset($filters['q']) ? trim((string) $filters['q']) : '';
        $provinceCode = isset($filters['province_code']) ? (string) $filters['province_code'] : null;
        $cityCode = isset($filters['city_code']) ? (string) $filters['city_code'] : null;

        if ($provinceCode !== null && $provinceCode !== '' && $cityCode !== null && $cityCode !== '') {
            $cityMatches = City::query()
                ->where('code', $cityCode)
                ->where('province_code', $provinceCode)
                ->exists();
            if (! $cityMatches) {
                $cityCode = null;
            }
        }

        $query = District::query()
            ->with(['city.province'])
            ->orderBy('name');

        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where('name', 'like', $like);
        }

        if ($cityCode !== null && $cityCode !== '') {
            $query->where('city_code', $cityCode);
        } elseif ($provinceCode !== null && $provinceCode !== '') {
            $query->whereHas('city', static fn ($q) => $q->where('province_code', $provinceCode));
        }

        $districts = $query->paginate(15)->withQueryString();

        $provinces = Province::query()->orderBy('name')->get();

        $filterState = [
            'q' => $q,
            'province_code' => ($provinceCode !== null && $provinceCode !== '') ? $provinceCode : null,
            'city_code' => ($cityCode !== null && $cityCode !== '') ? $cityCode : null,
        ];

        $filterCityName = '';
        if ($filterState['city_code'] !== null) {
            $filterCityName = City::query()->where('code', $filterState['city_code'])->value('name') ?? '';
        }

        return view('admin.regions.districts.index', compact(
            'districts',
            'provinces',
            'filterState',
            'filterCityName',
        ));
    }

    public function create(): View
    {
        $provinces = Province::query()->orderBy('name')->get();

        return view('admin.regions.districts.create', compact('provinces'));
    }

    public function store(StoreDistrictRequest $request): RedirectResponse
    {
        District::query()->create($this->withTimestamps($request->validated(), true));

        return redirect()
            ->route('admin.districts.index')
            ->with('success', 'Kecamatan berhasil ditambahkan.');
    }

    public function edit(District $district): View
    {
        $district->load(['city.province']);
        $provinces = Province::query()->orderBy('name')->get();

        return view('admin.regions.districts.edit', [
            'district' => $district,
            'provinces' => $provinces,
        ]);
    }

    public function update(UpdateDistrictRequest $request, District $district): RedirectResponse
    {
        $district->update($this->withTimestamps($request->validated(), false));

        return redirect()
            ->route('admin.districts.index')
            ->with('success', 'Kecamatan berhasil diperbarui.');
    }

    public function destroy(District $district): RedirectResponse
    {
        if ($district->villages()->exists()) {
            return redirect()
                ->route('admin.districts.index')
                ->with('error', 'Kecamatan tidak dapat dihapus karena masih memiliki desa/kelurahan.');
        }

        $district->delete();

        return redirect()
            ->route('admin.districts.index')
            ->with('success', 'Kecamatan berhasil dihapus.');
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

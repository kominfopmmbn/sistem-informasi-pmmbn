<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CityController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'province_code' => ['nullable', 'string', 'size:2', 'exists:provinces,code'],
        ]);

        $q = isset($filters['q']) ? trim((string) $filters['q']) : '';
        $provinceCode = isset($filters['province_code']) ? (string) $filters['province_code'] : null;

        $query = City::query()
            ->with('province')
            ->orderBy('name');

        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where('name', 'like', $like);
        }

        if ($provinceCode !== null && $provinceCode !== '') {
            $query->where('province_code', $provinceCode);
        }

        $cities = $query->paginate(15)->withQueryString();

        $provinces = Province::query()->orderBy('name')->get();

        $filterState = [
            'q' => $q,
            'province_code' => $provinceCode !== '' ? $provinceCode : null,
        ];

        return view('admin.regions.cities.index', compact('cities', 'provinces', 'filterState'));
    }

    public function create(): View
    {
        $provinces = Province::query()->orderBy('name')->get();

        return view('admin.regions.cities.create', compact('provinces'));
    }

    public function store(StoreCityRequest $request): RedirectResponse
    {
        City::query()->create($this->withTimestamps($request->validated(), true));

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'Kota/kabupaten berhasil ditambahkan.');
    }

    public function edit(City $city): View
    {
        $provinces = Province::query()->orderBy('name')->get();

        return view('admin.regions.cities.edit', [
            'city' => $city,
            'provinces' => $provinces,
        ]);
    }

    public function update(UpdateCityRequest $request, City $city): RedirectResponse
    {
        $city->update($this->withTimestamps($request->validated(), false));

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'Kota/kabupaten berhasil diperbarui.');
    }

    public function destroy(City $city): RedirectResponse
    {
        if ($city->districts()->exists()) {
            return redirect()
                ->route('admin.cities.index')
                ->with('error', 'Kota/kabupaten tidak dapat dihapus karena masih memiliki kecamatan.');
        }

        $city->delete();

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'Kota/kabupaten berhasil dihapus.');
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

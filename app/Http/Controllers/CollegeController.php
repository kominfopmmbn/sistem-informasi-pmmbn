<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCollegeRequest;
use App\Http\Requests\UpdateCollegeRequest;
use App\Models\College;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;

class CollegeController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'province_id' => ['nullable', 'integer', 'exists:indonesia_provinces,id'],
            'city_id' => ['nullable', 'integer'],
        ]);

        $q = isset($filters['q']) ? trim($filters['q']) : '';
        $provinceId = isset($filters['province_id']) ? (int) $filters['province_id'] : null;
        $cityId = isset($filters['city_id']) ? (int) $filters['city_id'] : null;

        if ($provinceId === null) {
            $cityId = null;
        } elseif ($cityId !== null) {
            $province = Province::query()->find($provinceId);
            $cityMatches = City::query()
                ->whereKey($cityId)
                ->where('province_code', $province->code)
                ->exists();
            if (! $cityMatches) {
                $cityId = null;
            }
        }

        $query = College::query()
            ->with(['province', 'city'])
            ->orderBy('name');

        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where('name', 'like', $like);
        }

        if ($provinceId !== null) {
            $query->where('province_id', $provinceId);
        }

        if ($cityId !== null) {
            $query->where('city_id', $cityId);
        }

        $colleges = $query->paginate(15)->withQueryString();

        $provinces = Province::query()->orderBy('name')->get();

        $filterCityName = '';
        if ($cityId !== null) {
            $filterCityName = City::query()->find($cityId)?->name ?? '';
        }

        $filterState = [
            'q' => $q,
            'province_id' => $provinceId,
            'city_id' => $cityId,
        ];

        return view('admin.colleges.index', compact(
            'colleges',
            'provinces',
            'filterState',
            'filterCityName',
        ));
    }

    public function create(): View
    {
        $provinces = Province::query()->orderBy('name')->get();

        return view('admin.colleges.create', compact('provinces'));
    }

    public function store(StoreCollegeRequest $request): RedirectResponse
    {
        College::create($request->validated());

        return redirect()
            ->route('admin.colleges.index')
            ->with('success', 'Perguruan tinggi berhasil ditambahkan.');
    }

    public function edit(College $college): View
    {
        $college->load(['province', 'city']);
        $provinces = Province::query()->orderBy('name')->get();

        return view('admin.colleges.edit', compact('college', 'provinces'));
    }

    public function update(UpdateCollegeRequest $request, College $college): RedirectResponse
    {
        $college->update($request->validated());

        return redirect()
            ->route('admin.colleges.index')
            ->with('success', 'Perguruan tinggi berhasil diperbarui.');
    }

    public function destroy(College $college): RedirectResponse
    {
        $college->delete();

        return redirect()
            ->route('admin.colleges.index')
            ->with('success', 'Perguruan tinggi berhasil dihapus.');
    }
}

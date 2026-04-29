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
            'province_code' => ['nullable', 'string', 'size:2', 'exists:provinces,code'],
            'city_code' => ['nullable', 'string', 'size:4', 'exists:cities,code'],
        ]);

        $q = isset($filters['q']) ? trim($filters['q']) : '';
        $provinceCode = isset($filters['province_code']) ? (string) $filters['province_code'] : null;
        $cityCode = isset($filters['city_code']) ? (string) $filters['city_code'] : null;

        if ($provinceCode === null) {
            $cityCode = null;
        } elseif ($cityCode !== null) {
            $cityMatches = City::query()
                ->where('code', $cityCode)
                ->where('province_code', $provinceCode)
                ->exists();
            if (! $cityMatches) {
                $cityCode = null;
            }
        }

        $query = College::query()
            ->with(['province', 'city'])
            ->orderBy('name');

        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where('name', 'like', $like);
        }

        if ($provinceCode !== null) {
            $query->where('province_code', $provinceCode);
        }

        if ($cityCode !== null) {
            $query->where('city_code', $cityCode);
        }

        $colleges = $query->paginate(15)->withQueryString();

        $provinces = Province::query()->orderBy('name')->get();

        $filterCityName = '';
        if ($cityCode !== null) {
            $filterCityName = City::query()->where('code', $cityCode)->value('name') ?? '';
        }

        $filterState = [
            'q' => $q,
            'province_code' => $provinceCode,
            'city_code' => $cityCode,
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

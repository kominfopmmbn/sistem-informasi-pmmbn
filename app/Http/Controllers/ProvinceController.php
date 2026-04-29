<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProvinceRequest;
use App\Http\Requests\UpdateProvinceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravolt\Indonesia\Models\Province;

class ProvinceController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $q = isset($filters['q']) ? trim((string) $filters['q']) : '';

        $query = Province::query()->orderBy('name');

        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where('name', 'like', $like);
        }

        $provinces = $query->paginate(15)->withQueryString();

        $filterState = ['q' => $q];

        return view('admin.regions.provinces.index', compact('provinces', 'filterState'));
    }

    public function create(): View
    {
        return view('admin.regions.provinces.create');
    }

    public function store(StoreProvinceRequest $request): RedirectResponse
    {
        Province::query()->create($this->withTimestamps($request->validated(), true));

        return redirect()
            ->route('admin.provinces.index')
            ->with('success', 'Provinsi berhasil ditambahkan.');
    }

    public function edit(Province $province): View
    {
        return view('admin.regions.provinces.edit', ['province' => $province]);
    }

    public function update(UpdateProvinceRequest $request, Province $province): RedirectResponse
    {
        $province->update($this->withTimestamps($request->validated(), false));

        return redirect()
            ->route('admin.provinces.index')
            ->with('success', 'Provinsi berhasil diperbarui.');
    }

    public function destroy(Province $province): RedirectResponse
    {
        if ($province->cities()->exists()) {
            return redirect()
                ->route('admin.provinces.index')
                ->with('error', 'Provinsi tidak dapat dihapus karena masih memiliki kota/kabupaten.');
        }

        $province->delete();

        return redirect()
            ->route('admin.provinces.index')
            ->with('success', 'Provinsi berhasil dihapus.');
    }

    /** Laravolt model mematikan timestamps; kolom di DB tetap diisi agar konsisten. */
    private function withTimestamps(array $data, bool $isCreate): array
    {
        $now = now();
        if ($isCreate) {
            return array_merge($data, ['created_at' => $now, 'updated_at' => $now]);
        }

        return array_merge($data, ['updated_at' => $now]);
    }
}

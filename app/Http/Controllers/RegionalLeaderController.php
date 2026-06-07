<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegionalLeaderRequest;
use App\Http\Requests\UpdateRegionalLeaderRequest;
use App\Models\RegionalLeader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegionalLeaderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $q = isset($filters['q']) ? trim($filters['q']) : '';

        $query = RegionalLeader::query()->orderBy('name');

        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where(function ($sub) use ($like): void {
                $sub->where('name', 'like', $like)
                    ->orWhere('code', 'like', $like);
            });
        }

        $regionalLeaders = $query->paginate(15)->withQueryString();

        $filterState = [
            'q' => $q,
        ];

        return view('admin.regional-leaders.index', compact(
            'regionalLeaders',
            'filterState',
        ));
    }

    public function create(): View
    {
        return view('admin.regional-leaders.create');
    }

    public function store(StoreRegionalLeaderRequest $request): RedirectResponse
    {
        RegionalLeader::create($request->validated());

        return redirect()
            ->route('admin.regional-leaders.index')
            ->with('success', 'Pimpinan wilayah berhasil ditambahkan.');
    }

    public function edit(RegionalLeader $regionalLeader): View
    {
        return view('admin.regional-leaders.edit', compact('regionalLeader'));
    }

    public function update(UpdateRegionalLeaderRequest $request, RegionalLeader $regionalLeader): RedirectResponse
    {
        $regionalLeader->update($request->validated());

        return redirect()
            ->route('admin.regional-leaders.index')
            ->with('success', 'Pimpinan wilayah berhasil diperbarui.');
    }

    public function destroy(RegionalLeader $regionalLeader): RedirectResponse
    {
        $regionalLeader->delete();

        return redirect()
            ->route('admin.regional-leaders.index')
            ->with('success', 'Pimpinan wilayah berhasil dihapus.');
    }
}

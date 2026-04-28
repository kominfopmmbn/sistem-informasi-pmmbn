<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrgRegionRequest;
use App\Http\Requests\UpdateOrgRegionRequest;
use App\Models\OrgRegion;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrgRegionController extends Controller
{
    public function index(): View
    {
        $orgRegions = OrgRegion::query()
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.org_regions.index', compact('orgRegions'));
    }

    public function create(): View
    {
        return view('admin.org_regions.create');
    }

    public function store(StoreOrgRegionRequest $request): RedirectResponse
    {
        OrgRegion::create($request->validated());

        return redirect()
            ->route('admin.org-regions.index')
            ->with('success', 'Wilayah organisasi berhasil ditambahkan.');
    }

    public function edit(OrgRegion $orgRegion): View
    {
        return view('admin.org_regions.edit', compact('orgRegion'));
    }

    public function update(UpdateOrgRegionRequest $request, OrgRegion $orgRegion): RedirectResponse
    {
        $orgRegion->update($request->validated());

        return redirect()
            ->route('admin.org-regions.index')
            ->with('success', 'Wilayah organisasi berhasil diperbarui.');
    }

    public function destroy(OrgRegion $orgRegion): RedirectResponse
    {
        $orgRegion->delete();

        return redirect()
            ->route('admin.org-regions.index')
            ->with('success', 'Wilayah organisasi berhasil dihapus.');
    }
}

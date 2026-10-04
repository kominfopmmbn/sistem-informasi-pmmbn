<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(): View
    {
        $banners = Banner::query()
            ->with('media')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.banners.index', compact('banners'));
    }

    public function create(): View
    {
        return view('admin.banners.create');
    }

    public function store(StoreBannerRequest $request): RedirectResponse
    {
        // Satu transaksi: banner tanpa gambar tidak boleh tersimpan bila upload gagal.
        DB::transaction(function () use ($request): void {
            $banner = Banner::create($this->dataFromRequest(request: $request));
            $banner->addMediaFromRequest('image')->toMediaCollection(Banner::IMAGE_COLLECTION);
        });

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner berhasil ditambahkan.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(UpdateBannerRequest $request, Banner $banner): RedirectResponse
    {
        $banner->update($this->dataFromRequest(request: $request, banner: $banner));

        if ($request->hasFile('image')) {
            $banner->addMediaFromRequest('image')->toMediaCollection(Banner::IMAGE_COLLECTION);
        }

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner berhasil diperbarui.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        // Hapus file fisik dari disk; soft delete tidak menghapus koleksi otomatis.
        $banner->clearMediaCollection(Banner::IMAGE_COLLECTION);

        $banner->delete();

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function dataFromRequest(Request $request, ?Banner $banner = null): array
    {
        $title = $request->string('title')->toString();

        return [
            'title' => $title,
            'slug' => $request->filled('slug')
                ? $request->string('slug')->toString()
                : $this->uniqueSlugFromTitle(title: $title, ignoreBannerId: $banner?->getKey()),
            'link_url' => $request->input('link_url'),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->integer('sort_order'),
        ];
    }

    private function uniqueSlugFromTitle(string $title, ?int $ignoreBannerId = null): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'banner';
        }

        $slug = $base;
        $suffix = 2;

        while (Banner::withTrashed()
            ->when($ignoreBannerId !== null, fn ($q) => $q->where('id', '!=', $ignoreBannerId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}

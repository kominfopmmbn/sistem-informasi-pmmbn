<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgramRequest;
use App\Http\Requests\UpdateProgramRequest;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProgramController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $q = isset($filters['q']) ? trim($filters['q']) : '';

        $query = Program::query()->orderBy('sort_order')->orderBy('title');

        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where('title', 'like', $like);
        }

        $programs = $query->paginate(15)->withQueryString();

        $filterState = ['q' => $q];

        return view('admin.programs.index', compact('programs', 'filterState'));
    }

    public function create(): View
    {
        return view('admin.programs.create');
    }

    public function store(StoreProgramRequest $request): RedirectResponse
    {
        $program = Program::create($this->dataFromRequest($request));

        $this->attachCover($request, $program);
        $this->attachGallery($request, $program);
        $this->syncGoals($program, $request->input('goals'));

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program berhasil ditambahkan.');
    }

    public function edit(Program $program): View
    {
        $program->load(['goals', 'media']);

        return view('admin.programs.edit', compact('program'));
    }

    public function update(UpdateProgramRequest $request, Program $program): RedirectResponse
    {
        $program->update($this->dataFromRequest($request, $program));

        $this->attachCover($request, $program);
        $this->attachGallery($request, $program);
        $this->syncGoals($program, $request->input('goals'));

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program berhasil diperbarui.');
    }

    public function destroy(Program $program): RedirectResponse
    {
        // Hapus file fisik dari disk; soft delete program tidak menghapus koleksi otomatis.
        $program->clearMediaCollection(Program::COVER_COLLECTION);
        $program->clearMediaCollection(Program::GALLERY_COLLECTION);

        $program->delete();

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program berhasil dihapus.');
    }

    /** Hapus satu gambar galeri (hanya milik program ini). */
    public function destroyGalleryMedia(Program $program, Media $media): RedirectResponse
    {
        $owned = $program->media()
            ->whereKey($media->getKey())
            ->where('collection_name', Program::GALLERY_COLLECTION)
            ->first();

        abort_if($owned === null, 404);

        $media->delete();

        return redirect()
            ->route('admin.programs.edit', $program)
            ->with('success', 'Gambar galeri berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function dataFromRequest(Request $request, ?Program $program = null): array
    {
        return [
            'title' => $request->string('title')->toString(),
            'slug' => $this->uniqueSlugFromTitle($request->string('title')->toString(), $program?->getKey()),
            'excerpt' => $request->string('excerpt')->toString(),
            'about_heading' => $request->input('about_heading'),
            'about_content' => $request->input('about_content'),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->integer('sort_order'),
        ];
    }

    private function attachCover(Request $request, Program $program): void
    {
        if ($request->hasFile('cover_photo')) {
            $program->addMediaFromRequest('cover_photo')->toMediaCollection(Program::COVER_COLLECTION);
        } elseif ($request->boolean('remove_cover')) {
            $program->clearMediaCollection(Program::COVER_COLLECTION);
        }
    }

    private function attachGallery(Request $request, Program $program): void
    {
        $files = $request->file('gallery');
        if ($files === null) {
            return;
        }

        $files = is_array($files) ? $files : [$files];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $program->addMedia($file)->toMediaCollection(Program::GALLERY_COLLECTION);
            }
        }
    }

    /**
     * Sinkron daftar tujuan: hapus semua lalu sisipkan ulang dengan urutan sesuai posisi array.
     *
     * @param  array<int, string|null>|null  $goals
     */
    private function syncGoals(Program $program, ?array $goals): void
    {
        DB::transaction(function () use ($program, $goals): void {
            $program->goals()->delete();

            $order = 0;
            foreach (array_values($goals ?? []) as $content) {
                $content = trim((string) $content);
                if ($content === '') {
                    continue;
                }

                $program->goals()->create([
                    'content' => $content,
                    'sort_order' => $order,
                ]);

                $order++;
            }
        });
    }

    private function uniqueSlugFromTitle(string $title, ?int $ignoreProgramId = null): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'program';
        }

        $slug = $base;
        $suffix = 2;

        while (Program::withTrashed()
            ->when($ignoreProgramId !== null, fn ($q) => $q->where('id', '!=', $ignoreProgramId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}

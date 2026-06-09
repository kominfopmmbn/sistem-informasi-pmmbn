@php
    use App\Models\Program;

    $program = $program ?? null;
    $aboutForEditor = old('about_content', $program?->about_content ?? '') ?? '';

    $goalValues = old('goals', $program ? $program->goals->pluck('content')->all() : []);
    if (!is_array($goalValues)) {
        $goalValues = [];
    }
    $goalValues = array_values(array_filter(
        array_map(fn ($v) => (string) $v, $goalValues),
        fn ($v) => trim($v) !== ''
    ));
    if (empty($goalValues)) {
        $goalValues = [''];
    }

    $isActive = old('is_active', $program ? $program->is_active : true);
    $galleryMedia = $program ? $program->getMedia(Program::GALLERY_COLLECTION) : collect();

    $imageMaxFileMb = max(1, (int) ceil(config('media-library.max_file_size') / 1024 / 1024));
    $imageAccepted = Program::imageDropzoneAcceptedFiles();
    $galleryHasError = $errors->has('gallery')
        || collect($errors->keys())->contains(fn ($key) => str_starts_with((string) $key, 'gallery.'));
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
@endpush

@push('scripts')
    <script>
        window.__programAboutHtml = @json($aboutForEditor);
    </script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('assets/js/admin-program-form.js') }}"></script>
@endpush

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible mb-6" role="alert">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<h6 class="mb-1">1. Informasi utama</h6>
<p class="text-body-secondary small mb-4">Judul tampil di kartu daftar dan halaman detail. Slug dibuat otomatis dari judul.</p>

<div class="row g-6 mb-6">
    <div class="col-md-8">
        <label class="form-label" for="title">Judul <span class="text-danger">*</span></label>
        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
            value="{{ old('title', $program?->title) }}" required maxlength="255">
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="sort_order">Urutan tampil</label>
        <input type="number" name="sort_order" id="sort_order" min="0"
            class="form-control @error('sort_order') is-invalid @enderror"
            value="{{ old('sort_order', $program->sort_order ?? 0) }}">
        <div class="form-text">Makin kecil makin awal tampil.</div>
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="excerpt">Deskripsi singkat <span class="text-danger">*</span></label>
        <textarea name="excerpt" id="excerpt" rows="2" maxlength="1000"
            class="form-control @error('excerpt') is-invalid @enderror"
            placeholder="Ringkasan singkat untuk kartu daftar & sidebar…" required>{{ old('excerpt', $program?->excerpt) }}</textarea>
        @error('excerpt')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked($isActive)>
            <span class="form-check-label">Tampilkan di website</span>
        </label>
    </div>
</div>

<div class="border-bottom mb-6"></div>

<h6 class="mb-1">2. Tentang Program</h6>
<p class="text-body-secondary small mb-4">Penjelasan lengkap program (rich text), tampil di bagian "Tentang Program".</p>

<div class="row g-6 mb-6">
    <div class="col-12">
        <label class="form-label" for="about_heading">Judul bagian</label>
        <input type="text" name="about_heading" id="about_heading"
            class="form-control @error('about_heading') is-invalid @enderror"
            value="{{ old('about_heading', $program?->about_heading) }}" maxlength="255"
            placeholder="mis. Apa itu Sekolah Moderasi?">
        @error('about_heading')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label">Isi</label>
        <input type="hidden" name="about_content" id="program_about_content" value="">
        <div id="program-about-editor" class="border rounded-bottom overflow-hidden" style="min-height: 220px"></div>
        @error('about_content')
            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="border-bottom mb-6"></div>

<h6 class="mb-1">3. Tujuan Program</h6>
<p class="text-body-secondary small mb-4">Daftar poin "Yang Akan Kamu Capai". Tambahkan satu baris per tujuan; baris kosong diabaikan.</p>

<div class="mb-6">
    <div id="program-goals-list">
        @foreach ($goalValues as $goal)
            <div class="program-goal-row d-flex align-items-center gap-2 mb-2">
                <input type="text" name="goals[]" class="form-control" maxlength="1000" value="{{ $goal }}"
                    placeholder="Tulis satu tujuan…">
                <button type="button" class="btn btn-label-danger btn-icon program-goal-remove" title="Hapus baris">
                    <i class="icon-base bx bx-x"></i>
                </button>
            </div>
        @endforeach
    </div>
    <button type="button" id="program-goals-add" class="btn btn-sm btn-label-primary mt-1">
        <i class="icon-base bx bx-plus me-1"></i> Tambah tujuan
    </button>
    @error('goals')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @error('goals.*')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<template id="program-goal-template">
    <div class="program-goal-row d-flex align-items-center gap-2 mb-2">
        <input type="text" name="goals[]" class="form-control" maxlength="1000" placeholder="Tulis satu tujuan…">
        <button type="button" class="btn btn-label-danger btn-icon program-goal-remove" title="Hapus baris">
            <i class="icon-base bx bx-x"></i>
        </button>
    </div>
</template>

<div class="border-bottom mb-6"></div>

<h6 class="mb-1">4. Sampul</h6>
<p class="text-body-secondary small mb-4">Gambar utama: dipakai sebagai gambar kartu daftar dan latar hero halaman detail. Format JPG/PNG/WEBP, maks. {{ $imageMaxFileMb }} MB.</p>

<div class="row g-6 mb-6">
    <div class="col-md-8">
        <input type="file" name="cover_photo" id="program_cover_photo" class="d-none" accept="{{ $imageAccepted }}">
        <div id="program-cover-dropzone"
            class="dropzone needsclick border rounded-3 @error('cover_photo') border-danger @enderror"
            data-max-filesize-mb="{{ $imageMaxFileMb }}" data-accepted-files="{{ $imageAccepted }}">
            <div class="dz-message needsclick text-center py-6">
                Seret gambar ke sini atau klik untuk memilih
                <span class="note needsclick d-block small text-body-secondary mt-2">Format gambar, maks.
                    {{ $imageMaxFileMb }} MB</span>
            </div>
        </div>
        @error('cover_photo')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    @if ($program?->hasMedia(Program::COVER_COLLECTION))
        <div class="col-md-4">
            <div class="border rounded p-3">
                <p class="small text-body-secondary mb-2">Sampul saat ini</p>
                <img src="{{ $program->getFirstMediaUrl(Program::COVER_COLLECTION) }}" alt=""
                    class="rounded mb-2" style="max-height: 120px; max-width: 100%; object-fit: cover;">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" name="remove_cover" id="remove_cover" value="1"
                        @checked(old('remove_cover'))>
                    <label class="form-check-label" for="remove_cover">Hapus sampul</label>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="border-bottom mb-6"></div>

<h6 class="mb-1">5. Galeri</h6>
<p class="text-body-secondary small mb-4">Foto "Dokumentasi Kegiatan". Bisa unggah beberapa gambar sekaligus (maks. {{ Program::GALLERY_MAX_PER_SUBMIT }} per unggah, {{ $imageMaxFileMb }} MB per gambar). Gambar pertama jadi foto utama.</p>

<div class="mb-2">
    <label class="form-label">Tambah gambar galeri</label>
    <input type="file" name="gallery[]" id="program_gallery" class="d-none" multiple accept="{{ $imageAccepted }}">
    <div id="program-gallery-dropzone"
        class="dropzone needsclick border rounded-3{{ $galleryHasError ? ' border-danger' : '' }}"
        data-max-files="{{ Program::GALLERY_MAX_PER_SUBMIT }}" data-max-filesize-mb="{{ $imageMaxFileMb }}"
        data-accepted-files="{{ $imageAccepted }}">
        <div class="dz-message needsclick text-center py-6">
            Seret gambar ke sini atau klik untuk memilih
            <span class="note needsclick d-block small text-body-secondary mt-2">Hingga
                {{ Program::GALLERY_MAX_PER_SUBMIT }} gambar per simpan (maks. {{ $imageMaxFileMb }} MB per gambar).</span>
        </div>
    </div>
    @error('gallery')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @foreach ($errors->keys() as $_galleryErrKey)
        @continue(! str_starts_with($_galleryErrKey, 'gallery.'))
        @foreach ($errors->get($_galleryErrKey) as $message)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @endforeach
    @endforeach
</div>

@if ($galleryMedia->isNotEmpty())
    <div class="row g-3 mt-1">
        @foreach ($galleryMedia as $media)
            <div class="col-6 col-md-3 col-lg-2">
                <div class="border rounded p-2 h-100 d-flex flex-column">
                    <img src="{{ $media->getUrl() }}" alt="" class="rounded mb-2"
                        style="aspect-ratio: 4/3; object-fit: cover; width: 100%;">
                    @can('programs.update')
                        <button type="submit" form="program-gallery-delete-{{ $media->getKey() }}"
                            class="btn btn-sm btn-label-danger mt-auto"
                            onclick="return confirm('Hapus gambar ini?');">
                            <i class="icon-base bx bx-trash me-1"></i> Hapus
                        </button>
                    @endcan
                </div>
            </div>
        @endforeach
    </div>
@endif

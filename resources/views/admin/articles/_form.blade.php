@php
    $article = $article ?? null;
    $defaultTagValues = $article?->tags
        ? $article->tags->pluck('id')->map(fn ($id) => (string) $id)->all()
        : [];
    $selectedTagsInput = old('tags', $defaultTagValues);
    if (!is_array($selectedTagsInput)) {
        $selectedTagsInput = [];
    }
    $selectedTagsInput = array_values(array_map(fn ($v) => trim((string) $v), $selectedTagsInput));
    $extraTagTitles = [];
    foreach ($selectedTagsInput as $item) {
        if ($item === '') {
            continue;
        }
        if (ctype_digit($item) && $tags->contains('id', (int) $item)) {
            continue;
        }
        if (ctype_digit($item)) {
            continue;
        }
        $extraTagTitles[] = $item;
    }
    $extraTagTitles = array_values(array_unique($extraTagTitles));
    $publishedAtDefault = $article?->published_at?->format('Y-m-d H:i');
    $publishedAtValue = old('published_at', $publishedAtDefault ?? '');
    $contentForEditor = old('content', $article?->content ?? '') ?? '';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/highlight/highlight.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
@endpush

@push('scripts')
    <script>
        window.__articleContentHtml = @json($contentForEditor);
    </script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/highlight/highlight.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/js/admin-article-form.js') }}"></script>
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

<h6 class="mb-1">1. Sampul</h6>
<p class="text-body-secondary small mb-4">Gambar utama artikel (opsional). Unggah gambar baru untuk mengganti sampul yang ada.</p>

<div class="mb-6" id="article-cover-section">
    <input type="file" name="cover_photo" id="article_cover_photo" class="d-none" accept="image/*">
    <div id="article-cover-dropzone" class="dropzone needsclick border rounded-3">
        <div class="dz-message needsclick text-center py-6">
            Seret gambar ke sini atau klik untuk memilih
            <span class="note needsclick d-block small text-body-secondary mt-2">Format gambar, maks. 5 MB</span>
        </div>
    </div>
    @if ($article?->hasMedia(\App\Models\Article::COVER_COLLECTION))
        <div class="mt-4 border rounded p-3 bg-lighter">
            <p class="small text-body-secondary mb-2">Sampul saat ini</p>
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <img src="{{ $article->getFirstMediaUrl(\App\Models\Article::COVER_COLLECTION) }}" alt=""
                        class="rounded" style="max-height: 120px; max-width: 200px; object-fit: cover;">
                </div>
                <div class="col">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="remove_cover" id="remove_cover"
                            value="1" @checked(old('remove_cover'))>
                        <label class="form-check-label" for="remove_cover">Hapus sampul (tanpa mengganti file baru)</label>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="border-bottom mb-6"></div>

<h6 class="mb-1">2. Informasi utama</h6>
<p class="text-body-secondary small mb-4">Judul dan kategori tampil di daftar serta halaman publik nanti. Slug dibuat otomatis dari judul.</p>

<div class="row g-6 mb-6">
    <div class="col-md-6">
        <label class="form-label" for="title">Judul <span class="text-danger">*</span></label>
        <input type="text" name="title" id="title"
            class="form-control @error('title') is-invalid @enderror"
            value="{{ old('title', $article?->title) }}" required>
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="subtitle">Subjudul</label>
        <input type="text" name="subtitle" id="subtitle" class="form-control @error('subtitle') is-invalid @enderror"
            value="{{ old('subtitle', $article?->subtitle) }}">
        @error('subtitle')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="category_id">Kategori <span class="text-danger">*</span></label>
        <div class="select2-primary @error('category_id') is-invalid @enderror">
            <select name="category_id" id="category_id"
                class="select2 form-select @error('category_id') is-invalid @enderror" required>
                <option value="">— Pilih —</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                        @selected((string) old('category_id', $article?->category_id) === (string) $category->id)>{{ $category->title }}</option>
                @endforeach
            </select>
        </div>
        @error('category_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="published_at">Waktu publikasi</label>
        <input type="text" name="published_at" id="published_at" autocomplete="off"
            class="form-control @error('published_at') is-invalid @enderror"
            placeholder="YYYY-MM-DD HH:MM"
            value="{{ $publishedAtValue }}">
        @error('published_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Untuk <strong>Simpan</strong> (terbit), waktu publikasi wajib. Untuk <strong>Simpan draf</strong>, boleh dikosongkan atau diisi sebagai jadwal catatan.</div>
    </div>
</div>

<div class="border-bottom mb-6"></div>

<h6 class="mb-1">3. Konten</h6>
<p class="text-body-secondary small mb-4">Isi utama artikel (rich text). Wajib diisi jika Anda memilih <strong>Simpan</strong> (bukan draf).</p>

<input type="hidden" name="content" id="article_content" value="">

<div class="mb-6">
    <div id="full-editor" class="article-quill-full border rounded-bottom overflow-hidden" style="min-height: 280px">
    </div>
    @error('content')
        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="border-bottom mb-6"></div>

<h6 class="mb-1">4. Tag</h6>
<p class="text-body-secondary small mb-4">Pilih dari daftar atau ketik tag baru (boleh beberapa).</p>

<div class="row g-6">
    <div class="col-12">
        <label class="form-label" for="article_tags">Tag</label>
        <div class="select2-primary @error('tags') is-invalid @enderror">
            <select name="tags[]" id="article_tags" class="select2 form-select @error('tags') is-invalid @enderror"
                multiple>
                @foreach ($tags as $tag)
                    <option value="{{ $tag->id }}" @selected(in_array((string) $tag->id, $selectedTagsInput, true))>
                        {{ $tag->title }}</option>
                @endforeach
                @foreach ($extraTagTitles as $extraTitle)
                    <option value="{{ $extraTitle }}" selected>{{ $extraTitle }}</option>
                @endforeach
            </select>
        </div>
        @error('tags')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        @error('tags.*')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

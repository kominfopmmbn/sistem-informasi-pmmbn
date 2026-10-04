@php
    use App\Models\Banner;

    $banner = $banner ?? null;
    $isActive = (bool) old('is_active', $banner ? $banner->is_active : true);
    $imageMaxFileMb = max(1, (int) ceil(config('media-library.max_file_size') / 1024 / 1024));
    $imageAccept = collect(explode(',', Banner::imageMimeList()))->map(fn ($ext) => '.'.$ext)->implode(',');
    $existingImage = $banner?->getFirstMedia(Banner::IMAGE_COLLECTION);
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
    <style>
        /* Panggung pratinjau meniru backdrop modal popup publik. */
        .banner-preview-stage {
            background-color: rgba(var(--bs-dark-rgb), .88);
            min-height: 15rem;
        }

        /* Link share dibungkus utuh agar slug di ujung URL tetap terbaca. */
        .banner-share-url {
            resize: none;
            overflow: hidden;
            word-break: break-all;
        }

        .banner-preview-stage img {
            max-height: 22rem;
            object-fit: contain;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('assets/js/admin-copy.js') }}"></script>
    <script src="{{ asset('assets/js/admin-banner-form.js') }}"></script>
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

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card mb-6">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0">Gambar banner @if (! $banner)<span class="text-danger">*</span>@endif</h5>
                <small class="text-body-secondary">JPG, PNG, atau WEBP · maks. {{ $imageMaxFileMb }} MB</small>
            </div>
            <div class="card-body">
                <input type="file" name="image" id="banner_image" class="d-none" accept="{{ $imageAccept }}">
                <div id="banner-image-dropzone"
                    class="dropzone needsclick @error('image') border-danger @enderror"
                    data-max-filesize-mb="{{ $imageMaxFileMb }}" data-accepted-files="{{ $imageAccept }}"
                    @if ($existingImage) data-existing-url="{{ $existingImage->getUrl() }}"
                        data-existing-name="{{ $existingImage->file_name }}"
                        data-existing-size="{{ $existingImage->size }}" @endif>
                    <div class="dz-message needsclick">
                        <p class="h5 needsclick pt-4 mb-2">Seret gambar ke sini</p>
                        <p class="text-body-secondary needsclick mb-3">atau</p>
                        <span class="needsclick btn btn-sm btn-label-primary">Pilih gambar</span>
                    </div>
                </div>
                @if ($banner)
                    <div class="form-text">Gambar saat ini tetap dipakai bila tidak diganti.</div>
                @endif
                @error('image')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="card mb-6">
            <div class="card-header">
                <h5 class="card-title mb-0">Konten</h5>
            </div>
            <div class="card-body">
                <div class="mb-6">
                    <label class="form-label" for="title">Judul <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $banner?->title) }}" required maxlength="255"
                        placeholder="mis. Pendaftaran Anggota 2026">
                    <div class="form-text">Untuk daftar admin dan teks alternatif gambar.</div>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="form-label" for="link_url">Link form pendaftaran</label>
                    <input type="url" name="link_url" id="link_url"
                        class="form-control @error('link_url') is-invalid @enderror"
                        value="{{ old('link_url', $banner?->link_url) }}" maxlength="2048" placeholder="https://…">
                    <div class="form-text">Tujuan tombol "Daftar Sekarang". Kosongkan bila banner hanya gambar.</div>
                    @error('link_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card mb-6">
            <div class="card-header">
                <h5 class="card-title mb-0">Publikasi</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center gap-4">
                    <label class="mb-0" for="is_active">
                        <span class="d-block fw-medium text-heading">Aktif</span>
                        <small class="text-body-secondary">Tampil di popup website</small>
                    </label>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                            @checked($isActive)>
                    </div>
                </div>
                <div class="border-top pt-4 mt-4">
                    <label class="form-label" for="sort_order">Urutan tampil</label>
                    <input type="number" name="sort_order" id="sort_order" min="0"
                        class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', $banner->sort_order ?? 0) }}">
                    <div class="form-text">Makin kecil makin awal di carousel.</div>
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="card mb-6">
            <div class="card-header">
                <h5 class="card-title mb-0">Link share</h5>
            </div>
            <div class="card-body">
                <label class="form-label" for="slug">Parameter link</label>
                <div class="input-group @error('slug') has-validation @enderror">
                    <span class="input-group-text">?banner=</span>
                    <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
                        value="{{ old('slug', $banner?->slug) }}" maxlength="255"
                        data-saved="{{ $banner?->slug }}">
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-text">Kosongkan untuk dibuat otomatis dari judul.</div>

                <label class="form-label mt-4" for="banner-share-url">Link untuk dibagikan</label>
                <div class="input-group">
                    <textarea readonly rows="2" class="form-control banner-share-url" id="banner-share-url"
                        data-share-base="{{ route('home.index') }}" onfocus="this.select()">{{ $banner?->shareUrl() }}</textarea>
                    <button type="button" class="btn btn-outline-primary" data-copy-target="banner-share-url">Salin</button>
                </div>
                <div id="banner-share-note" class="form-text" aria-live="polite"></div>
            </div>
        </div>

        <div class="card mb-6">
            <div class="card-header">
                <h5 class="card-title mb-0">Pratinjau popup</h5>
            </div>
            <div class="card-body">
                <div class="banner-preview-stage rounded-3 p-4 d-flex flex-column justify-content-center">
                    <div class="d-flex justify-content-end mb-2">
                        <span class="btn-close btn-close-white" aria-hidden="true"></span>
                    </div>
                    <img id="banner-preview-image" alt="" class="d-none w-100 rounded-3"
                        src="{{ $existingImage?->getUrl() }}">
                    <p id="banner-preview-empty" class="text-white-50 small text-center my-auto py-6 mb-0">
                        Pilih gambar untuk melihat pratinjau.</p>
                    <div id="banner-preview-cta" class="text-center mt-3 d-none">
                        <span class="btn btn-primary rounded-pill px-6" aria-hidden="true">Daftar Sekarang</span>
                    </div>
                </div>
                <div class="form-text">Begini tampilannya bagi pengunjung situs.</div>
            </div>
        </div>
    </div>
</div>

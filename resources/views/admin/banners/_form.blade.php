@php
    use App\Models\Banner;

    $banner = $banner ?? null;
    $isActive = old('is_active', $banner ? $banner->is_active : true);
    $imageMaxFileMb = max(1, (int) ceil(config('media-library.max_file_size') / 1024 / 1024));
    $imageAccept = collect(explode(',', Banner::imageMimeList()))->map(fn ($ext) => '.'.$ext)->implode(',');
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
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

<div class="row g-6">
    <div class="col-md-8">
        <label class="form-label" for="title">Judul <span class="text-danger">*</span></label>
        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
            value="{{ old('title', $banner?->title) }}" required maxlength="255">
        <div class="form-text">Untuk daftar admin dan teks alternatif gambar.</div>
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="sort_order">Urutan tampil</label>
        <input type="number" name="sort_order" id="sort_order" min="0"
            class="form-control @error('sort_order') is-invalid @enderror"
            value="{{ old('sort_order', $banner->sort_order ?? 0) }}">
        <div class="form-text">Makin kecil makin awal tampil.</div>
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="slug">Parameter link</label>
        <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
            value="{{ old('slug', $banner?->slug) }}" maxlength="255" placeholder="mis. pendaftaran-2026">
        <div class="form-text">Dipakai di link share <code>?banner=…</code>. Kosongkan untuk dibuat otomatis dari judul.</div>
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="link_url">Link form pendaftaran</label>
        <input type="url" name="link_url" id="link_url" class="form-control @error('link_url') is-invalid @enderror"
            value="{{ old('link_url', $banner?->link_url) }}" maxlength="2048" placeholder="https://…">
        <div class="form-text">Tujuan tombol "Daftar Sekarang". Kosongkan bila banner hanya gambar.</div>
        @error('link_url')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-8">
        <label class="form-label">Gambar banner @if (! $banner)<span class="text-danger">*</span>@endif</label>
        <input type="file" name="image" id="banner_image" class="d-none" accept="{{ $imageAccept }}">
        <div id="banner-image-dropzone"
            class="dropzone needsclick border rounded-3 @error('image') border-danger @enderror"
            data-max-filesize-mb="{{ $imageMaxFileMb }}" data-accepted-files="{{ $imageAccept }}">
            <div class="dz-message needsclick text-center py-6">
                Seret gambar ke sini atau klik untuk memilih
                <span class="note needsclick d-block small text-body-secondary mt-2">Format JPG/PNG/WEBP, maks.
                    {{ $imageMaxFileMb }} MB</span>
            </div>
        </div>
        @if ($banner)
            <div class="form-text">Kosongkan bila tidak diganti.</div>
        @endif
        @error('image')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    @if ($banner?->hasMedia(Banner::IMAGE_COLLECTION))
        <div class="col-md-4">
            <div class="border rounded p-3">
                <p class="small text-body-secondary mb-2">Gambar saat ini</p>
                <img src="{{ $banner->getFirstMediaUrl(Banner::IMAGE_COLLECTION) }}" alt=""
                    class="rounded" style="max-height: 160px; max-width: 100%; object-fit: contain;">
            </div>
        </div>
    @endif
    <div class="col-12">
        <label class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked($isActive)>
            <span class="form-check-label">Aktif (tampil di popup website)</span>
        </label>
    </div>
</div>

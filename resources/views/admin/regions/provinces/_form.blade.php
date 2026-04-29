@php
    /** @var \Laravolt\Indonesia\Models\Province|null $province */
    $metaDisplay = old('meta');
    if ($metaDisplay === null && isset($province) && $province->meta !== null) {
        $metaDisplay = json_encode($province->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    $metaDisplay = $metaDisplay ?? '';
@endphp

<div class="row g-6">
    <div class="col-12 col-md-4">
        <label class="form-label" for="region_province_code">Kode</label>
        <input type="text" name="code" id="region_province_code"
            class="form-control @error('code') is-invalid @enderror"
            value="{{ old('code', isset($province) ? $province->code : '') }}" required maxlength="2"
            pattern="[0-9]{2}" inputmode="numeric" autocomplete="off">
        <div class="form-text">2 digit numerik (contoh: 11 untuk Aceh).</div>
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-8">
        <label class="form-label" for="region_province_name">Nama</label>
        <input type="text" name="name" id="region_province_name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', isset($province) ? $province->name : '') }}" required maxlength="255" autocomplete="off">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="region_province_meta">Meta (JSON, opsional)</label>
        <textarea name="meta" id="region_province_meta" rows="4"
            class="form-control font-monospace small @error('meta') is-invalid @enderror"
            placeholder="{}">{{ $metaDisplay }}</textarea>
        @error('meta')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

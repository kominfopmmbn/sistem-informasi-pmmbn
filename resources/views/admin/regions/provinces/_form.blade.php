@php
    /** @var \Laravolt\Indonesia\Models\Province|null $province */
    $metaDisplay = old('meta');
    if ($metaDisplay === null && isset($province) && $province->meta !== null) {
        $metaDisplay = json_encode($province->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    $metaDisplay = $metaDisplay ?? '';
@endphp

<div class="row g-6">
    <div class="col-12">
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

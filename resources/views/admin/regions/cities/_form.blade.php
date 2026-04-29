@php
    /** @var \Illuminate\Support\Collection<int, \Laravolt\Indonesia\Models\Province> $provinces */
    /** @var \Laravolt\Indonesia\Models\City|null $city */
    $metaDisplay = old('meta');
    if ($metaDisplay === null && isset($city) && $city->meta !== null) {
        $metaDisplay = json_encode($city->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    $metaDisplay = $metaDisplay ?? '';
    $provinceCode = old('province_code', isset($city) ? $city->province_code : '');
@endphp

<div class="row g-6">
    <div class="col-12 col-md-4">
        <label class="form-label" for="region_city_code">Kode</label>
        <input type="text" name="code" id="region_city_code"
            class="form-control @error('code') is-invalid @enderror"
            value="{{ old('code', isset($city) ? $city->code : '') }}" required maxlength="4"
            pattern="[0-9]{4}" inputmode="numeric" autocomplete="off">
        <div class="form-text">4 digit numerik.</div>
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-8">
        <label class="form-label" for="region_city_name">Nama</label>
        <input type="text" name="name" id="region_city_name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', isset($city) ? $city->name : '') }}" required maxlength="255" autocomplete="off">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="region_city_province">Provinsi</label>
        <div class="select2-primary @error('province_code') is-invalid @enderror">
            <div class="position-relative w-100">
                <select name="province_code" id="region_city_province"
                    class="select2 form-select @error('province_code') is-invalid @enderror" required
                    data-placeholder="Pilih provinsi">
                    <option value=""></option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province->code }}" @selected((string) $provinceCode === (string) $province->code)>
                            {{ $province->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @error('province_code')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="region_city_meta">Meta (JSON, opsional)</label>
        <textarea name="meta" id="region_city_meta" rows="4"
            class="form-control font-monospace small @error('meta') is-invalid @enderror"
            placeholder="{}">{{ $metaDisplay }}</textarea>
        @error('meta')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/js/admin-regions-forms.js') }}"></script>
@endpush

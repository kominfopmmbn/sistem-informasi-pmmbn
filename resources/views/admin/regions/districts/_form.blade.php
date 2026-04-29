@php
    /** @var \Illuminate\Support\Collection<int, \Laravolt\Indonesia\Models\Province> $provinces */
    /** @var \Laravolt\Indonesia\Models\District|null $district */
    $metaDisplay = old('meta');
    if ($metaDisplay === null && isset($district) && $district->meta !== null) {
        $metaDisplay = json_encode($district->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    $metaDisplay = $metaDisplay ?? '';

    $provinceHelper = old('province_code');
    $cityCode = old('city_code', isset($district) ? $district->city_code : '');
    $cityName = '';
    if (isset($district) && $district->relationLoaded('city') && $district->city !== null) {
        $cityName = $district->city->name;
        if ($provinceHelper === null) {
            $provinceHelper = $district->city->province_code;
        }
    }
    if ($provinceHelper === null) {
        $provinceHelper = '';
    }
    if ($provinceHelper === '' && $cityCode !== '' && $cityCode !== null) {
        $provinceHelper = \Laravolt\Indonesia\Models\City::query()->where('code', $cityCode)->value('province_code') ?? '';
    }
@endphp

<div class="row g-6">
    <div class="col-12 col-md-4">
        <label class="form-label" for="region_district_code">Kode</label>
        <input type="text" name="code" id="region_district_code"
            class="form-control @error('code') is-invalid @enderror"
            value="{{ old('code', isset($district) ? $district->code : '') }}" required maxlength="7"
            pattern="[0-9]{7}" inputmode="numeric" autocomplete="off">
        <div class="form-text">7 digit numerik.</div>
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-8">
        <label class="form-label" for="region_district_name">Nama</label>
        <input type="text" name="name" id="region_district_name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', isset($district) ? $district->name : '') }}" required maxlength="255" autocomplete="off">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="region_district_province_helper">Provinsi</label>
        <div class="select2-primary">
            <div class="position-relative w-100">
                <select id="region_district_province_helper" class="select2 form-select" data-placeholder="Pilih provinsi">
                    <option value=""></option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province->code }}" @selected((string) $provinceHelper === (string) $province->code)>
                            {{ $province->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <p id="region_district_province_hint" class="form-text text-body-secondary mb-0">Untuk memilih kota/kabupaten.</p>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="region_district_city">Kota / kabupaten</label>
        <div class="select2-primary @error('city_code') is-invalid @enderror">
            <div class="position-relative w-100">
                <select name="city_code" id="region_district_city"
                    class="select2 form-select @error('city_code') is-invalid @enderror"
                    data-search-url="{{ route('select.cities') }}"
                    data-placeholder="Pilih kota/kabupaten"
                    @if ($provinceHelper === '' || $provinceHelper === null) disabled @endif required>
                    @if ($cityCode !== '' && $cityCode !== null)
                        <option value="{{ $cityCode }}" selected>{{ $cityName }}</option>
                    @else
                        <option value=""></option>
                    @endif
                </select>
            </div>
        </div>
        @error('city_code')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="region_district_meta">Meta (JSON, opsional)</label>
        <textarea name="meta" id="region_district_meta" rows="4"
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

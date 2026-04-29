@php
    /** @var \Illuminate\Support\Collection<int, \Laravolt\Indonesia\Models\Province> $provinces */
    /** @var \Laravolt\Indonesia\Models\Village|null $village */

    $metaDisplay = old('meta');
    if ($metaDisplay === null && isset($village) && $village->meta !== null) {
        $metaDisplay = json_encode($village->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    $metaDisplay = $metaDisplay ?? '';

    $districtCode = old('district_code', isset($village) ? $village->district_code : '');
    $cityCode = '';
    $provinceHelper = '';
    $districtName = '';

    if (isset($village) && $village->relationLoaded('district') && $village->district !== null) {
        $districtName = $village->district->name;
        $cityCode = $village->district->city_code;
        if ($village->district->relationLoaded('city') && $village->district->city !== null) {
            $provinceHelper = $village->district->city->province_code;
        }
    }

    if (($districtCode !== '' && $districtCode !== null) && ($cityCode === '' || $provinceHelper === '')) {
        $dRow = \Laravolt\Indonesia\Models\District::query()->with('city')->where('code', $districtCode)->first();
        if ($dRow !== null) {
            $districtName = $districtName ?: $dRow->name;
            $cityCode = $dRow->city_code;
            $provinceHelper = $dRow->city?->province_code ?? '';
        }
    }
@endphp

<div class="row g-6">
    <div class="col-12 col-md-4">
        <label class="form-label" for="region_village_code">Kode</label>
        <input type="text" name="code" id="region_village_code"
            class="form-control @error('code') is-invalid @enderror"
            value="{{ old('code', isset($village) ? $village->code : '') }}" required maxlength="10"
            pattern="[0-9]{10}" inputmode="numeric" autocomplete="off">
        <div class="form-text">10 digit numerik.</div>
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-8">
        <label class="form-label" for="region_village_name">Nama</label>
        <input type="text" name="name" id="region_village_name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', isset($village) ? $village->name : '') }}" required maxlength="255" autocomplete="off">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-4">
        <label class="form-label" for="region_village_province_helper">Provinsi</label>
        <div class="select2-primary">
            <div class="position-relative w-100">
                <select id="region_village_province_helper" class="select2 form-select" data-placeholder="Pilih provinsi">
                    <option value=""></option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province->code }}" @selected((string) $provinceHelper === (string) $province->code)>
                            {{ $province->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <label class="form-label" for="region_village_city">Kota / kabupaten</label>
        <div class="select2-primary">
            <div class="position-relative w-100">
                <select id="region_village_city" class="select2 form-select"
                    data-search-url="{{ route('select.cities') }}"
                    @if ($provinceHelper === '' || $provinceHelper === null) disabled @endif>
                    @if ($cityCode !== '' && $cityCode !== null)
                        @php
                            $cityLabel = '';
                            if (isset($village) && $village->district?->city && (string) $village->district->city_code === (string) $cityCode) {
                                $cityLabel = $village->district->city->name;
                            } else {
                                $cityLabel = \Laravolt\Indonesia\Models\City::query()->where('code', $cityCode)->value('name') ?? '';
                            }
                        @endphp
                        <option value="{{ $cityCode }}" selected>{{ $cityLabel }}</option>
                    @else
                        <option value=""></option>
                    @endif
                </select>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <label class="form-label" for="region_village_district">Kecamatan</label>
        <div class="select2-primary @error('district_code') is-invalid @enderror">
            <div class="position-relative w-100">
                <select name="district_code" id="region_village_district"
                    class="select2 form-select @error('district_code') is-invalid @enderror"
                    data-search-url="{{ route('select.districts') }}"
                    @if ($cityCode === '' || $cityCode === null) disabled @endif required>
                    @if ($districtCode !== '' && $districtCode !== null)
                        <option value="{{ $districtCode }}" selected>{{ $districtName }}</option>
                    @else
                        <option value=""></option>
                    @endif
                </select>
            </div>
        </div>
        @error('district_code')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="region_village_meta">Meta (JSON, opsional)</label>
        <textarea name="meta" id="region_village_meta" rows="4"
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

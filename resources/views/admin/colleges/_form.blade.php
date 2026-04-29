@php
    /** @var \Illuminate\Support\Collection<int, \Laravolt\Indonesia\Models\Province> $provinces */
    $provinceId = old('province_id', isset($college) ? $college->province_id : '');
    $cityId = old('city_id', isset($college) ? $college->city_id : '');
    $cityName = '';
    if (isset($college) && $college->relationLoaded('city') && $college->city !== null) {
        $cityName = $college->city->name;
    } elseif ($cityId !== null && $cityId !== '') {
        $cityRow = \Laravolt\Indonesia\Models\City::query()->find($cityId);
        $cityName = $cityRow?->name ?? '';
    }
@endphp

<div class="row g-6">
    <div class="col-12">
        <label class="form-label" for="college_name">Nama perguruan tinggi</label>
        <input type="text" name="name" id="college_name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', isset($college) ? $college->name : '') }}" required maxlength="255"
            autocomplete="organization">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="college_province_id">Provinsi</label>
        <div class="select2-primary @error('province_id') is-invalid @enderror">
            <div class="position-relative w-100">
                <select name="province_id" id="college_province_id"
                    class="select2 form-select @error('province_id') is-invalid @enderror" required
                    data-placeholder="Pilih provinsi">
                    <option value=""></option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province->getKey() }}" @selected((string) $provinceId === (string) $province->getKey())>
                            {{ $province->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @error('province_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="college_city_id">Kota / Kabupaten</label>
        <div class="select2-primary @error('city_id') is-invalid @enderror">
            <div class="position-relative w-100">
                <select name="city_id" id="college_city_id"
                    class="select2 form-select @error('city_id') is-invalid @enderror"
                    data-search-url="{{ route('indonesia.select.cities') }}"
                    data-placeholder="Pilih kota/kabupaten" @if ($cityId !== null && $cityId !== '') data-initial-id="{{ $cityId }}" data-initial-name="{{ $cityName }}" @endif
                    @if (! filled($provinceId)) disabled @endif required>
                    @if ($cityId !== null && $cityId !== '')
                        <option value="{{ $cityId }}" selected>{{ $cityName }}</option>
                    @else
                        <option value=""></option>
                    @endif
                </select>
            </div>
        </div>
        <p id="college_city_hint"
            class="form-text text-body-secondary mb-0 @if (filled($provinceId)) d-none @endif">
            Pilih provinsi terlebih dahulu untuk memilih kota/kabupaten.
        </p>
        @error('city_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/js/admin-college-form.js') }}"></script>
@endpush

@extends('front.layouts.app', ['bodyClass' => 'page-card-member'])

@section('title', 'Aktivasi Anggota')

@php
    use App\Enums\Gender;
@endphp

@section('content')
    <div class="container">
        <div class="card-member-hero-section">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <p class="mb-2 fs-6 text-light opacity-75">Tentang &gt; KTA</p>
                <h1 class="display-5 fw-bold mb-3">Kartu Tanda Anggota</h1>
                <p class="fs-6 lh-base">Organisasi pergerakan mahasiswa yang berkomitmen menumbuhkan moderasi beragama dan
                    memperkuat semangat bela negara di tengah keberagaman Indonesia.</p>
            </div>
        </div>
    </div>

    <div class="container my-5 pt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <form id="member-form" method="post" enctype="multipart/form-data" action="{{ url()->current() }}">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label" for="member_nim">NIM</label>
                            <input type="text" name="nim" id="member_nim"
                                class="form-control form-control-custom @error('nim') is-invalid @enderror"
                                value="{{ old('nim') }}" maxlength="255" autocomplete="off">
                            @error('nim')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="member_email">Email</label>
                            <input type="email" name="email" id="member_email"
                                class="form-control form-control-custom @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" maxlength="255" autocomplete="email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="member_full_name">Nama lengkap</label>
                            <input type="text" name="full_name" id="member_full_name"
                                class="form-control form-control-custom @error('full_name') is-invalid @enderror"
                                value="{{ old('full_name') }}" maxlength="255" autocomplete="name">
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="member_nickname">Nama panggilan</label>
                            <input type="text" name="nickname" id="member_nickname"
                                class="form-control form-control-custom @error('nickname') is-invalid @enderror"
                                value="{{ old('nickname') }}" maxlength="255" autocomplete="nickname">
                            @error('nickname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="member_province_code">Provinsi tempat lahir</label>
                            <div class="select2-primary @error('province_code') is-invalid @enderror">
                                <div class="position-relative w-100">
                                    <select name="province_code" id="member_province_code"
                                        class="select2 form-select form-control-custom @error('province_code') is-invalid @enderror"
                                        data-placeholder="Pilih provinsi">
                                        <option value=""></option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->code }}" @selected((string) old('province_code') === (string) $province->code)>
                                                {{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @error('province_code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="member_place_of_birth_code">Kota / kabupaten tempat
                                lahir</label>
                            <div class="select2-primary @error('place_of_birth_code') is-invalid @enderror">
                                <div class="position-relative w-100">
                                    <select name="place_of_birth_code" id="member_place_of_birth_code"
                                        class="select2 form-select form-select-custom @error('place_of_birth_code') is-invalid @enderror"
                                        data-search-url="{{ route('select.cities') }}"
                                        data-placeholder="Pilih kota/kabupaten"
                                        @if ($placeCode !== null && $placeCode !== '') data-initial-code="{{ $placeCode }}" data-initial-name="{{ $placeName }}" @endif
                                        @if (! filled(old('province_code'))) disabled @endif>
                                        @if ($placeCode !== null && $placeCode !== '')
                                            <option value="{{ $placeCode }}" selected>{{ $placeName }}</option>
                                        @else
                                            <option value=""></option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <p id="member_city_hint"
                                class="form-text small text-secondary mb-0 @if (filled(old('province_code'))) d-none @endif">
                                Pilih provinsi terlebih dahulu untuk memilih kota/kabupaten.
                            </p>
                            @error('place_of_birth_code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="member_date_of_birth">Tanggal lahir</label>
                            <input type="date" name="date_of_birth" id="member_date_of_birth"
                                class="form-control form-control-custom @error('date_of_birth') is-invalid @enderror"
                                value="{{ old('date_of_birth') }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="member_gender_id">Jenis kelamin</label>
                            <div class="select2-primary @error('gender_id') is-invalid @enderror">
                                <div class="position-relative w-100">
                                    <select name="gender_id" id="member_gender_id"
                                        class="select2 form-select form-select-custom @error('gender_id') is-invalid @enderror"
                                        data-placeholder="Pilih">
                                        <option value=""></option>
                                        @foreach (Gender::cases() as $g)
                                            <option value="{{ $g->value }}" @selected((string) old('gender_id') === (string) $g->value)>
                                                {{ $g->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @error('gender_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="member_org_region_id">Wilayah organisasi</label>
                            <div class="select2-primary @error('org_region_id') is-invalid @enderror">
                                <div class="position-relative w-100">
                                    <select name="org_region_id" id="member_org_region_id"
                                        class="select2 form-select form-select-custom @error('org_region_id') is-invalid @enderror"
                                        data-placeholder="Pilih (opsional)">
                                        <option value=""></option>
                                        @foreach ($orgRegions as $region)
                                            <option value="{{ $region->id }}" @selected((string) old('org_region_id') === (string) $region->id)>
                                                {{ $region->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @error('org_region_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="member_phone_number">Nomor telepon</label>
                            <input type="text" name="phone_number" id="member_phone_number"
                                class="form-control form-control-custom @error('phone_number') is-invalid @enderror"
                                value="{{ old('phone_number') }}" maxlength="255" autocomplete="tel">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Dokumen pendukung</label>
                            <input type="file" name="supporting_documents[]" id="member_supporting_documents"
                                class="d-none" multiple accept="{{ $supportingAccept }}">
                            @if ($maxNewSupportingFiles > 0)
                                <div id="member-supporting-dropzone"
                                    class="dropzone needsclick border rounded-3{{ $supportingDocsHasError ? ' border-danger' : '' }}"
                                    data-max-files="{{ $maxNewSupportingFiles }}"
                                    data-max-filesize-mb="{{ $supportingMaxFileMb }}"
                                    data-accepted-files="{{ $supportingAcceptedDropzone }}">
                                    <div class="dz-message needsclick text-center py-4 px-3">
                                        Seret berkas ke sini atau klik untuk memilih
                                        <span
                                            class="note needsclick d-block small text-secondary mt-2">PDF, Office, gambar,
                                            ZIP, atau teks — hingga {{ $maxNewSupportingFiles }} berkas per pengiriman
                                            (maks. {{ $supportingMaxFileMb }} MB per berkas).</span>
                                    </div>
                                </div>
                            @endif
                            @error('supporting_documents')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @foreach ($errors->keys() as $_errKey)
                                @continue(! str_starts_with($_errKey, 'supporting_documents.'))
                                @foreach ($errors->get($_errKey) as $message)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-custom d-inline-flex align-items-center">
                            Daftar <i class="fa-solid fa-paper-plane ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('assets/js/admin-member-form.js') }}"></script>
@endpush

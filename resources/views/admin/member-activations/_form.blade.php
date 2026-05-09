@php
    use App\Enums\Gender;
    use App\Models\Member;

    /** @var \Illuminate\Support\Collection<int, \Laravolt\Indonesia\Models\Province> $provinces */
    /** @var \Illuminate\Support\Collection<int, \App\Models\OrgRegion> $orgRegions */
    $provinceCode = old(
        'province_code',
        isset($member) && $member->placeOfBirthCity ? $member->placeOfBirthCity->province_code : '',
    );
    $placeCode = old('place_of_birth_code', isset($member) ? $member->place_of_birth_code : '');
    $placeName = '';
    if (isset($member) && $member->relationLoaded('placeOfBirthCity') && $member->placeOfBirthCity !== null) {
        $placeName = $member->placeOfBirthCity->name;
    } elseif ($placeCode !== null && $placeCode !== '') {
        $placeRow = \App\Models\City::query()->where('code', $placeCode)->first();
        $placeName = $placeRow?->name ?? '';
    }
    $genderOld = old('gender_id', isset($member) && $member->gender_id !== null ? $member->gender_id->value : '');
    $orgRegionOld = old('org_region_id', isset($member) ? $member->org_region_id ?? '' : '');
    $existingSupportingCount = isset($member) ? $member->getMedia(Member::SUPPORTING_DOCUMENTS_COLLECTION)->count() : 0;
    $maxNewSupportingFiles = max(
        0,
        min(
            Member::SUPPORTING_DOCUMENTS_MAX_PER_SUBMIT,
            Member::SUPPORTING_DOCUMENTS_MAX_TOTAL - $existingSupportingCount,
        ),
    );
    $supportingMaxFileMb = max(1, (int) ceil(config('media-library.max_file_size') / 1024 / 1024));
    $supportingAccept = Member::supportingDocumentFileInputAccept();
    $supportingAcceptedDropzone = Member::supportingDocumentDropzoneAcceptedFiles();
    $supportingDocsHasError =
        $errors->has('supporting_documents') ||
        collect($errors->keys())->contains(fn($key) => str_starts_with((string) $key, 'supporting_documents.'));
@endphp

<div class="row g-6">
    <div class="col-12 col-md-6">
        <label class="form-label" for="member_nim">NIM</label>
        <input type="text" name="nim" id="member_nim" class="form-control @error('nim') is-invalid @enderror"
            value="{{ old('nim', isset($member) ? $member->nim : '') }}" maxlength="255" autocomplete="off">
        @error('nim')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="member_email">Email</label>
        <input type="email" name="email" id="member_email" class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', isset($member) ? $member->email : '') }}" maxlength="255" autocomplete="email">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="member_full_name">Nama lengkap</label>
        <input type="text" name="full_name" id="member_full_name"
            class="form-control @error('full_name') is-invalid @enderror"
            value="{{ old('full_name', isset($member) ? $member->full_name : '') }}" maxlength="255"
            autocomplete="name">
        @error('full_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="member_nickname">Nama panggilan</label>
        <input type="text" name="nickname" id="member_nickname"
            class="form-control @error('nickname') is-invalid @enderror"
            value="{{ old('nickname', isset($member) ? $member->nickname : '') }}" maxlength="255"
            autocomplete="nickname">
        @error('nickname')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="member_province_code">Provinsi tempat lahir</label>
        <div class="select2-primary @error('province_code') is-invalid @enderror">
            <div class="position-relative w-100">
                <select name="province_code" id="member_province_code"
                    class="select2 form-select @error('province_code') is-invalid @enderror"
                    data-placeholder="Pilih provinsi (opsional)">
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
    <div class="col-12 col-md-6">
        <label class="form-label" for="member_place_of_birth_code">Kota / kabupaten tempat lahir</label>
        <div class="select2-primary @error('place_of_birth_code') is-invalid @enderror">
            <div class="position-relative w-100">
                <select name="place_of_birth_code" id="member_place_of_birth_code"
                    class="select2 form-select @error('place_of_birth_code') is-invalid @enderror"
                    data-search-url="{{ route('select.cities') }}" data-placeholder="Pilih kota/kabupaten"
                    @if ($placeCode !== null && $placeCode !== '') data-initial-code="{{ $placeCode }}" data-initial-name="{{ $placeName }}" @endif
                    @if (!filled($provinceCode)) disabled @endif>
                    @if ($placeCode !== null && $placeCode !== '')
                        <option value="{{ $placeCode }}" selected>{{ $placeName }}</option>
                    @else
                        <option value=""></option>
                    @endif
                </select>
            </div>
        </div>
        <p id="member_city_hint"
            class="form-text text-body-secondary mb-0 @if (filled($provinceCode)) d-none @endif">
            Pilih provinsi terlebih dahulu untuk memilih kota/kabupaten.
        </p>
        @error('place_of_birth_code')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="member_date_of_birth">Tanggal lahir</label>
        <input type="date" name="date_of_birth" id="member_date_of_birth"
            class="form-control @error('date_of_birth') is-invalid @enderror"
            value="{{ old('date_of_birth', isset($member) && $member->date_of_birth ? $member->date_of_birth->format('Y-m-d') : '') }}">
        @error('date_of_birth')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="member_gender_id">Jenis kelamin</label>
        <div class="select2-primary @error('gender_id') is-invalid @enderror">
            <div class="position-relative w-100">
                <select name="gender_id" id="member_gender_id"
                    class="select2 form-select @error('gender_id') is-invalid @enderror"
                    data-placeholder="Pilih (opsional)">
                    <option value=""></option>
                    @foreach (Gender::cases() as $g)
                        <option value="{{ $g->value }}" @selected((string) $genderOld === (string) $g->value)>
                            {{ $g->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @error('gender_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="member_org_region_id">Wilayah organisasi</label>
        <div class="select2-primary @error('org_region_id') is-invalid @enderror">
            <div class="position-relative w-100">
                <select name="org_region_id" id="member_org_region_id"
                    class="select2 form-select @error('org_region_id') is-invalid @enderror"
                    data-placeholder="Pilih (opsional)">
                    <option value=""></option>
                    @foreach ($orgRegions as $region)
                        <option value="{{ $region->id }}" @selected((string) $orgRegionOld === (string) $region->id)>
                            {{ $region->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @error('org_region_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="member_phone_number">Nomor telepon</label>
        <input type="text" name="phone_number" id="member_phone_number"
            class="form-control @error('phone_number') is-invalid @enderror"
            value="{{ old('phone_number', isset($member) ? $member->phone_number : '') }}" maxlength="255"
            autocomplete="tel">
        @error('phone_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Dokumen pendukung</label>
        <input type="file" name="supporting_documents[]" id="member_supporting_documents" class="d-none" multiple
            accept="{{ $supportingAccept }}">
        @if ($maxNewSupportingFiles > 0)
            @if ($member->currentStatus?->isPending())
                <div id="member-supporting-dropzone"
                    class="dropzone needsclick border rounded-3{{ $supportingDocsHasError ? ' border-danger' : '' }}"
                    data-max-files="{{ $maxNewSupportingFiles }}" data-max-filesize-mb="{{ $supportingMaxFileMb }}"
                    data-accepted-files="{{ $supportingAcceptedDropzone }}">
                    <div class="dz-message needsclick text-center py-6">
                        Seret berkas ke sini atau klik untuk memilih
                        <span class="note needsclick d-block small text-body-secondary mt-2">PDF, Office, gambar, ZIP,
                            atau
                            teks — hingga {{ $maxNewSupportingFiles }} berkas baru per simpan (maks.
                            {{ $supportingMaxFileMb }} MB per berkas).</span>
                    </div>
                </div>
            @endif
        @else
            <div class="alert alert-secondary mb-0" role="status">
                Kuota dokumen pendukung penuh ({{ Member::SUPPORTING_DOCUMENTS_MAX_TOTAL }} berkas). Hapus salah satu
                lampiran di bawah untuk bisa menambah lagi.
            </div>
        @endif
        @error('supporting_documents')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        @foreach ($errors->keys() as $_errKey)
            @continue(!str_starts_with($_errKey, 'supporting_documents.'))
            @foreach ($errors->get($_errKey) as $message)
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @endforeach
        @endforeach
        @if ($member->currentStatus?->isPending())
            <p class="form-text text-body-secondary mb-0 mt-2">
                Opsional. Total maks. {{ Member::SUPPORTING_DOCUMENTS_MAX_TOTAL }} berkas per anggota (termasuk yang sudah
                ada).
            </p>
        @endif
        @isset($member)
            @php
                $supportingMedia = $member->getMedia(Member::SUPPORTING_DOCUMENTS_COLLECTION);
            @endphp
            @if ($supportingMedia->isNotEmpty())
                <ul class="list-unstyled mb-0 mt-2">
                    @foreach ($supportingMedia as $m)
                        <li class="d-flex flex-wrap align-items-center gap-2 py-2">
                            <a href="{{ $m->getUrl() }}" target="_blank" rel="noopener noreferrer"
                                class="text-break">{{ $m->file_name }}</a>
                            @can('members.update')
                                <button type="submit" form="member-supporting-delete-{{ $m->getKey() }}"
                                    class="btn btn-sm btn-label-danger" onclick="return confirm('Hapus dokumen ini?');">
                                    Hapus
                                </button>
                            @endcan
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="form-text text-body-secondary mb-0 mt-2">Belum ada dokumen pendukung.</p>
            @endif
        @endisset
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('assets/js/admin-member-form.js') }}"></script>
@endpush

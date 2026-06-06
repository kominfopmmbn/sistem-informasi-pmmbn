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
            {{-- show success message --}}
            @if (session('success'))
                <div class="col-12">
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            <div class="col-lg-8 col-md-10">
                @foreach ($memberActivation?->media()->where('collection_name', \App\Models\Member::SUPPORTING_DOCUMENTS_COLLECTION)->get() ?? collect() as $m)
                    <form id="member-supporting-delete-{{ $m->getKey() }}"
                        action="{{ route('admin.member-activations.supporting-media.destroy', ['member_activation' => $memberActivation, 'media' => $m]) }}"
                        method="POST" class="d-none" aria-hidden="true">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach
                <form id="member-form" method="post" enctype="multipart/form-data" novalidate
                    action="{{ route('about.member-activation.store') }}">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label" for="member_nim">NIM <span class="text-danger">*</span></label>
                            <input type="text" name="nim" id="member_nim"
                                class="form-control form-control-custom @error('nim') is-invalid border-danger @enderror"
                                required value="{{ old('nim', $memberActivation?->nim ?? '') }}" maxlength="255">
                            <div class="invalid-feedback @error('nim') d-block @enderror">@error('nim'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="member_email">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="member_email"
                                class="form-control form-control-custom @error('email') is-invalid border-danger @enderror"
                                required value="{{ old('email', $memberActivation?->email ?? '') }}" maxlength="255">
                            <div class="invalid-feedback">
                                @error('email')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="member_full_name">Nama lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" id="member_full_name"
                                class="form-control form-control-custom @error('full_name') is-invalid border-danger @enderror"
                                required value="{{ old('full_name', $memberActivation?->full_name ?? '') }}"
                                maxlength="255">
                            <div class="invalid-feedback @error('full_name') d-block @enderror">@error('full_name'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="member_place_of_birth_code">Tempat Lahir <span class="text-danger">*</span></label>
                            <div class="select2-primary @error('place_of_birth_code') is-invalid border-danger @enderror"
                                required>
                                <div class="position-relative w-100">
                                    <select name="place_of_birth_code" id="member_place_of_birth_code"
                                        class="select2 form-select form-select-custom @error('place_of_birth_code') is-invalid border-danger @enderror"
                                        data-search-url="{{ route('select.cities') }}"
                                        data-placeholder="Pilih kota/kabupaten"
                                        required
                                        @if ($placeCode !== null && $placeCode !== '') data-initial-code="{{ $placeCode }}" data-initial-name="{{ $placeName }}" @endif>
                                        @if ($placeCode !== null && $placeCode !== '')
                                            <option value="{{ $placeCode }}" selected>{{ $placeName }}</option>
                                        @else
                                            <option value=""></option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="invalid-feedback @error('place_of_birth_code') d-block @enderror">@error('place_of_birth_code'){{ $message }}@enderror</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="member_date_of_birth">Tanggal lahir <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" id="member_date_of_birth"
                                class="form-control form-control-custom @error('date_of_birth') is-invalid border-danger @enderror"
                                required value="{{ old('date_of_birth', $memberActivation?->date_of_birth->format('Y-m-d') ?? '') }}">
                            <div class="invalid-feedback @error('date_of_birth') d-block @enderror">@error('date_of_birth'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="member_gender_id">Jenis kelamin <span class="text-danger">*</span></label>
                            <div class="select2-primary @error('gender_id') is-invalid border-danger @enderror" required>
                                <div class="position-relative w-100">
                                    <select name="gender_id" id="member_gender_id"
                                        class="select2 form-select form-select-custom @error('gender_id') is-invalid border-danger @enderror"
                                        required data-placeholder="Pilih">
                                        <option value=""></option>
                                        @foreach (Gender::cases() as $g)
                                            <option value="{{ $g->value }}" @selected((string) old('gender_id', $memberActivation?->gender_id?->value ?? '') === (string) $g->value)>
                                                {{ $g->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="invalid-feedback @error('gender_id') d-block @enderror">@error('gender_id'){{ $message }}@enderror</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="member_phone_number">Nomor telepon <span class="text-danger">*</span></label>
                            <input type="text" name="phone_number" id="member_phone_number"
                                class="form-control form-control-custom @error('phone_number') is-invalid border-danger @enderror"
                                required value="{{ old('phone_number', $memberActivation?->phone_number ?? '') }}"
                                maxlength="255">
                            <div class="invalid-feedback @error('phone_number') d-block @enderror">@error('phone_number'){{ $message }}@enderror</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="member_college_id">Perguruan tinggi <span class="text-danger">*</span></label>
                            <div class="select2-primary @error('college_id') form-control-custom is-invalid border-danger @enderror"
                                required>
                                <div class="position-relative w-100">
                                    <select name="college_id" id="member_college_id"
                                        class="select2 form-select form-select-custom @error('college_id') is-invalid border-danger @enderror"
                                        data-search-url="{{ route('select.colleges') }}"
                                        data-placeholder="Pilih perguruan tinggi"
                                        required>
                                        @if ($collegeId !== '' && $collegeId !== null)
                                            <option value="{{ $collegeId }}" selected>{{ $collegeLabel }}</option>
                                        @else
                                            <option value=""></option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="invalid-feedback @error('college_id') d-block @enderror">@error('college_id'){{ $message }}@enderror</div>
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
                                        <span class="note needsclick d-block small text-secondary mt-2">PDF, Office,
                                            gambar,
                                            ZIP, atau teks — hingga {{ $maxNewSupportingFiles }} berkas per pengiriman
                                            (maks. {{ $supportingMaxFileMb }} MB per berkas).</span>
                                    </div>
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
                            @php
                                $supportingMedia = $memberActivation?->media()->where('collection_name', \App\Models\Member::SUPPORTING_DOCUMENTS_COLLECTION)->get() ?? collect();
                            @endphp
                            @if ($supportingMedia->isNotEmpty())
                                <ul class="list-unstyled mb-0 mt-2">
                                    @foreach ($supportingMedia as $m)
                                        <li class="d-flex flex-wrap align-items-center gap-2 py-2">
                                            <a href="{{ $m->getUrl() }}" target="_blank" rel="noopener noreferrer"
                                                class="text-break">{{ $m->file_name }}</a>
                                            {{-- @can('members.update') --}}
                                                <button type="submit" form="member-supporting-delete-{{ $m->getKey() }}"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Hapus dokumen ini?');">
                                                    Hapus
                                                </button>
                                            {{-- @endcan --}}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="form-text text-body-secondary mb-0 mt-2">Belum ada dokumen pendukung.</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-custom d-inline-flex align-items-center" id="btn-register">
                            Daftar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('front.about.member-activation-verification-email-modal')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('assets/js/admin-member-form.js') }}?v={{ filemtime(public_path('assets/js/admin-member-form.js')) }}"></script>
    <script>
        let isVerfiedEmail = false;

        // Ambil teks label sebuah field (tanpa tanda bintang) untuk menyusun pesan validasi.
        const requiredFieldLabel = (id) => {
            const $label = $('label[for="' + id + '"]');
            if (!$label.length) {
                return 'Kolom ini';
            }
            const text = $label.clone().find('span').remove().end().text().replace(/\s+/g, ' ').trim();
            return text || 'Kolom ini';
        };

        // Cari div .invalid-feedback milik field (bekerja untuk input teks maupun select2).
        const requiredFieldFeedback = ($el) => $el.closest('[class*="col-"]').find('.invalid-feedback').first();

        const setFieldInvalid = ($el, message) => {
            $el.addClass('is-invalid border-danger');
            $el.closest('.select2-primary').addClass('is-invalid border-danger');
            requiredFieldFeedback($el).text(message).addClass('d-block');
        };

        const clearFieldInvalid = ($el) => {
            $el.removeClass('is-invalid border-danger');
            $el.closest('.select2-primary').removeClass('is-invalid border-danger');
            requiredFieldFeedback($el).text('').removeClass('d-block');
        };

        // Validasi semua field wajib: beri border merah + pesan pada yang kosong.
        const validateRequiredFields = () => {
            let $first = null;
            $('#member-form').find('input[required], select[required], textarea[required]').each(function() {
                const $el = $(this);
                const value = ($el.val() || '').toString().trim();
                if (value === '') {
                    setFieldInvalid($el, requiredFieldLabel(this.id) + ' wajib diisi');
                    if (!$first) {
                        $first = $el;
                    }
                } else {
                    clearFieldInvalid($el);
                }
            });

            if ($first) {
                const $col = $first.closest('[class*="col-"]');
                const target = ($col.length ? $col : $first)[0];
                if (target && typeof target.scrollIntoView === 'function') {
                    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
            return true;
        };

        $(document).ready(function() {
            // Hapus tanda error begitu field wajib mulai diisi.
            $('#member-form').on('input', 'input[required], textarea[required]', function() {
                if (($(this).val() || '').toString().trim() !== '') {
                    clearFieldInvalid($(this));
                }
            });
            $('#member_place_of_birth_code, #member_gender_id, #member_college_id')
                .on('change', function() {
                    if (($(this).val() || '').toString().trim() !== '') {
                        clearFieldInvalid($(this));
                    }
                });

            $('#member-form').submit(function(event) {
                event.preventDefault();
                // Jika sudah verified, langsung submit form ke server
                if (isVerfiedEmail) {
                    this.submit(); // ✅ Native DOM submit, bypass jQuery event
                    return;
                }

                // Validasi field wajib (border merah + pesan di bawah input)
                if (!validateRequiredFields()) {
                    return;
                }

                // send verification email otp
                sendOtpVerificationEmail({
                    beforeSend: function() {
                        $('#btn-register').prop('disabled', true);
                        $('#btn-register').html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...'
                        );
                    },
                    success: function(response) {
                        $('#email-for-verification').text($('#member_email').val());
                        $('#member-activation-verification-email-modal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        $('#member_email').addClass('is-invalid border-danger');
                        $('#member_email').next('.invalid-feedback').text(
                            xhr.responseJSON?.message ?? xhr.responseText ??
                            'Gagal mengirim email verifikasi'
                        );
                    },
                    complete: function() {
                        $('#btn-register').prop('disabled', false);
                        $('#btn-register').html('Daftar');
                    }
                })
            });

            $('#member-activation-verification-email-modal').on('show.bs.modal', function() {
                $('#otp').val('').removeClass('is-invalid border-danger');
                $('#otp').next('.invalid-feedback').text('');
                startTimerResendOtp();
            });

            $('#resend-otp').click(function() {
                sendOtpVerificationEmail({
                    beforeSend: function() {
                        $('#resend-otp').prop('disabled', true).addClass('disabled');
                        $('#resend-otp').html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...'
                        );
                    },
                    success: function(response) {
                        startTimerResendOtp();
                    },
                    error: function(xhr, status, error) {
                        $('#otp').addClass('is-invalid border-danger');
                        $('#otp').next('.invalid-feedback').text(
                            xhr.responseJSON?.message ?? xhr.responseText ??
                            'Gagal mengirim ulang OTP'
                        );
                    },
                    complete: function() {
                        $('#resend-otp').prop('disabled', false).removeClass('disabled');
                        $('#resend-otp').html(
                            'Kirim ulang OTP <span class="time-remaining"></span>');
                    }
                });
            });

            $('#member-activation-verification-email-form').submit(function(event) {
                event.preventDefault();

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: {
                        email: $('#member_email').val(),
                        otp: $('#otp').val(),
                        _token: "{{ csrf_token() }}",
                    },
                    beforeSend: function() {
                        $('#btn-verify').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Verifying...'
                            );
                    },
                    success: function(response) {
                        $('#member-activation-verification-email-modal').modal('hide');
                        isVerfiedEmail = true;
                        $('#member-form').submit();
                    },
                    error: function(xhr, status, error) {
                        $('#otp').addClass('is-invalid border-danger');
                        $('#otp').next('.invalid-feedback').text(
                            xhr.responseJSON?.message ??
                            'Terjadi kesalahan saat verifikasi email'
                        );
                    },
                    complete: function() {
                        $('#btn-verify').prop('disabled', false).html('Verifikasi');
                    }
                });
            });
        });

        const sendOtpVerificationEmail = ({
            beforeSend = null,
            success = null,
            error = null,
            complete = null
        } = {}) => {
            $.ajax({
                url: "{{ route('about.member-activation.send-verification-email') }}",
                type: 'POST',
                data: {
                    email: $('#member_email').val(),
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: beforeSend,
                success: success,
                error: error,
                complete: complete,
            });
        }

        const formatTime = (time) => {
            const minutes = Math.floor(time / 60);
            const seconds = time % 60;
            return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        const startTimerResendOtp = () => {
            let timeRemaining = 120;
            const interval = setInterval(function() {
                timeRemaining--;
                $('.time-remaining').text(formatTime(timeRemaining));
                if (timeRemaining <= 0) {
                    clearInterval(interval);
                    $('#resend-otp').prop('disabled', false).removeClass('disabled');
                    $('.time-remaining').text('');
                }
            }, 1000);
        }
    </script>
@endpush

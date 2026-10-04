@extends('admin.layouts.app')

@section('title', 'Detail anggota')

@php
    use App\Models\Member;

    $kta = $member->kta;
    $village = $member->village;
    $college = $member->college;

    $phoneDigits = preg_replace('/\D/', '', (string) $member->phone_number);
    $whatsappNumber = match (true) {
        str_starts_with($phoneDigits, '0') => '62'.substr($phoneDigits, 1),
        str_starts_with($phoneDigits, '8') => '62'.$phoneDigits,
        default => $phoneDigits,
    };

    $sections = [
        'Data diri' => [
            'Nama lengkap' => $member->full_name,
            'Jenis kelamin' => $member->gender_id?->label(),
            'Tempat lahir' => $member->placeOfBirthCity?->name,
            'Tanggal lahir' => $member->date_of_birth?->translatedFormat('j F Y'),
        ],
        'Pendidikan & organisasi' => [
            'Perguruan tinggi' => $college?->name,
            'Lokasi kampus' => $college
                ? collect([$college->city?->name, $college->province?->name])->filter()->implode(', ')
                : null,
            'Pimpinan wilayah' => $member->regionalLeader?->name,
        ],
        'Domisili' => [
            'Alamat' => $member->address,
            'Desa / Kelurahan' => $village?->name,
            'Kecamatan' => $village?->district?->name,
            'Kabupaten / Kota' => $village?->district?->city?->name,
            'Provinsi' => $village?->district?->city?->province?->name,
            'Kode pos' => $village?->postal_code,
        ],
    ];

    $supportingMedia = $member->getMedia(Member::SUPPORTING_DOCUMENTS_COLLECTION);

    $missing = collect([
        'NIM' => $member->nim,
        'Email' => $member->email,
        'Nomor telepon' => $member->phone_number,
        'Jenis kelamin' => $member->gender_id,
        'Tempat lahir' => $member->place_of_birth_code,
        'Tanggal lahir' => $member->date_of_birth,
        'Perguruan tinggi' => $member->college_id,
        'Pimpinan wilayah' => $member->regional_leader_id,
        'Desa / Kelurahan' => $member->village_code,
        'Alamat' => $member->address,
        'Dokumen pendukung' => $supportingMedia->isEmpty() ? null : true,
    ])->filter(fn ($value) => blank($value))->keys();

    $documentIcon = fn (string $ext): array => match (strtolower($ext)) {
        'pdf' => ['bxs-file-pdf', 'danger'],
        'doc', 'docx', 'txt' => ['bx-file', 'primary'],
        'xls', 'xlsx' => ['bx-spreadsheet', 'success'],
        'ppt', 'pptx' => ['bx-file', 'warning'],
        'zip' => ['bx-archive', 'secondary'],
        default => ['bx-file-blank', 'secondary'],
    };
@endphp

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <h4 class="fw-bold mb-0 py-3">
            <a href="{{ route('admin.members.index') }}" class="text-body-secondary fw-light">Anggota /</a>
            Detail
        </h4>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.members.index') }}" class="btn btn-label-secondary">
                <i class="icon-base bx bx-arrow-back me-1"></i> Kembali
            </a>
            @can('members.update')
                <a href="{{ route('admin.members.edit', $member) }}" class="btn btn-primary">
                    <i class="icon-base bx bx-edit-alt me-1"></i> Ubah
                </a>
            @endcan
        </div>
    </div>

    <div class="card mb-6 overflow-hidden">
        <div class="row g-0">
            <div class="col-lg-5">
                <div class="bg-body h-100 p-6 d-flex flex-column justify-content-center">
                    @if ($kta)
                        <div class="member-kta-preview" data-kta-preview>
                            <iframe src="{{ route('kta.show', ['ktaNumber' => $kta->number, 'type' => 'view']) }}"
                                title="Pratinjau KTA {{ $kta->number }}" tabindex="-1" loading="lazy"
                                aria-hidden="true"></iframe>
                            <a href="{{ route('kta.show', ['ktaNumber' => $kta->number]) }}" target="_blank"
                                class="member-kta-preview-link" aria-label="Buka KTA {{ $kta->number }} (PDF)"></a>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-5">
                            <div>
                                <div class="small text-body-secondary">Nomor KTA</div>
                                <div class="fs-4 fw-semibold text-heading member-tabular">{{ $kta->number }}</div>
                                <div class="small text-body-secondary">
                                    {{ $kta->is_manual ? 'Nomor manual (anggota khusus)' : 'Nomor otomatis' }}
                                </div>
                            </div>
                            <a href="{{ route('kta.show', ['ktaNumber' => $kta->number]) }}" target="_blank"
                                class="btn btn-primary">
                                <i class="icon-base bx bx-printer me-1"></i> Buka PDF
                            </a>
                        </div>
                    @else
                        <div class="member-kta-empty d-flex flex-column align-items-center justify-content-center text-center">
                            <div class="avatar avatar-lg mb-4">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="icon-base bx bx-id-card icon-lg"></i>
                                </span>
                            </div>
                            <h5 class="mb-2">KTA belum terbit</h5>
                            <p class="text-body-secondary mb-4">
                                KTA terbit otomatis saat aktivasi anggota disetujui. Untuk anggota khusus, isi nomor
                                KTA manual di halaman Ubah.
                            </p>
                            @can('members.update')
                                <a href="{{ route('admin.members.edit', $member) }}" class="btn btn-label-primary">
                                    Isi nomor KTA
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card-body p-6">
                    <h4 class="mb-1">{{ $member->full_name ?: 'Tanpa nama' }}</h4>
                    <div class="text-body-secondary mb-3">
                        NIM <span class="text-heading member-tabular">{{ $member->nim ?: 'belum diisi' }}</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mb-6">
                        @if ($kta)
                            <span class="badge bg-label-success">Sudah Verifikasi</span>
                            @if ($kta->is_manual)
                                <span class="badge bg-label-info">Khusus</span>
                            @endif
                        @else
                            <span class="badge bg-label-warning">Belum Verifikasi</span>
                        @endif
                    </div>

                    <ul class="list-unstyled mb-6">
                        <li class="d-flex align-items-center gap-4 mb-4">
                            <div class="avatar avatar-sm flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-secondary">
                                    <i class="icon-base bx bx-envelope"></i>
                                </span>
                            </div>
                            <div class="member-min-w-0">
                                <div class="small text-body-secondary">Email</div>
                                @if ($member->email)
                                    <a href="mailto:{{ $member->email }}" class="text-break">{{ $member->email }}</a>
                                @else
                                    <span class="text-body-secondary">Belum diisi</span>
                                @endif
                            </div>
                        </li>
                        <li class="d-flex align-items-center gap-4">
                            <div class="avatar avatar-sm flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-secondary">
                                    <i class="icon-base bx bx-phone"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small text-body-secondary">Nomor telepon</div>
                                @if ($member->phone_number)
                                    <a href="tel:{{ $phoneDigits }}" class="member-tabular">{{ $member->phone_number }}</a>
                                @else
                                    <span class="text-body-secondary">Belum diisi</span>
                                @endif
                            </div>
                            @if ($whatsappNumber !== '')
                                <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener noreferrer"
                                    class="btn btn-sm btn-label-success flex-shrink-0">
                                    <i class="icon-base bx bxl-whatsapp me-1"></i> WhatsApp
                                </a>
                            @endif
                        </li>
                    </ul>

                    @if ($missing->isNotEmpty())
                        <div class="alert alert-warning d-flex gap-3 mb-0" role="status">
                            <i class="icon-base bx bx-error-circle flex-shrink-0 mt-1"></i>
                            <div class="text-heading">
                                <div class="fw-medium">{{ $missing->count() }} data belum diisi</div>
                                <div>{{ $missing->implode(', ') }}.</div>
                                @can('members.update')
                                    <a href="{{ route('admin.members.edit', $member) }}" class="fw-medium">Lengkapi data</a>
                                @endcan
                            </div>
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-2 text-heading">
                            <i class="icon-base bx bx-check-circle text-success"></i>
                            <span>Semua data utama dan dokumen sudah terisi ({{ $supportingMedia->count() }} dokumen).</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-6">
        <div class="card-body p-6">
            <div class="row g-6">
                @foreach ($sections as $heading => $fields)
                    <div class="col-md-6 col-xl-4">
                        <h5 class="mb-4">{{ $heading }}</h5>
                        <dl class="mb-0">
                            @foreach ($fields as $label => $value)
                                <dt class="small fw-normal text-body-secondary">{{ $label }}</dt>
                                <dd class="mb-4 text-heading text-break">
                                    @if (filled($value))
                                        {!! nl2br(e($value)) !!}
                                    @else
                                        <span class="badge bg-label-warning">Belum diisi</span>
                                    @endif
                                </dd>
                            @endforeach
                        </dl>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mb-6">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Dokumen pendukung</h5>
            <span class="badge bg-label-secondary">{{ $supportingMedia->count() }} berkas</span>
        </div>
        @if ($supportingMedia->isNotEmpty())
            <ul class="list-group list-group-flush">
                @foreach ($supportingMedia as $m)
                    @php
                        [$icon, $tone] = $documentIcon($m->extension);
                    @endphp
                    <li class="list-group-item d-flex align-items-center gap-4 px-6 py-3">
                        @if (str_starts_with((string) $m->mime_type, 'image/'))
                            <img src="{{ $m->getUrl() }}" alt="" class="member-doc-thumb rounded flex-shrink-0"
                                loading="lazy">
                        @else
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-{{ $tone }}">
                                    <i class="icon-base bx {{ $icon }}"></i>
                                </span>
                            </div>
                        @endif
                        <div class="flex-grow-1 member-min-w-0">
                            <a href="{{ $m->getUrl() }}" target="_blank" rel="noopener noreferrer"
                                class="d-block text-heading text-truncate">{{ $m->file_name }}</a>
                            <div class="small text-body-secondary">
                                {{ strtoupper($m->extension) }} · {{ $m->human_readable_size }} ·
                                diunggah {{ $m->created_at->translatedFormat('j M Y') }}
                            </div>
                        </div>
                        <a href="{{ $m->getUrl() }}" target="_blank" rel="noopener noreferrer"
                            class="btn btn-sm btn-icon btn-text-secondary flex-shrink-0"
                            title="Buka {{ $m->file_name }}" aria-label="Buka {{ $m->file_name }}">
                            <i class="icon-base bx bx-link-external"></i>
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="card-body pt-0">
                <p class="text-body-secondary mb-0">
                    Belum ada dokumen pendukung.
                    @can('members.update')
                        <a href="{{ route('admin.members.edit', $member) }}">Unggah di halaman Ubah</a>.
                    @endcan
                </p>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .member-tabular {
            font-variant-numeric: tabular-nums;
        }

        .member-min-w-0 {
            min-width: 0;
        }

        /* Kartu KTA asli (171.2mm x 107.96mm = 647 x 408 px) diperkecil mengikuti lebar kolom. */
        .member-kta-preview {
            position: relative;
            aspect-ratio: 171.2 / 107.96;
            filter: drop-shadow(0 0.5rem 1rem rgba(34, 48, 62, 0.16));
        }

        .member-kta-preview iframe {
            position: absolute;
            inset: 0 auto auto 0;
            width: 647px;
            height: 408px;
            border: 0;
            transform-origin: 0 0;
            transform: scale(var(--kta-scale, 0.6));
            pointer-events: none;
            background: transparent;
        }

        .member-kta-preview-link {
            position: absolute;
            inset: 0;
            border-radius: 0.75rem;
            transition: box-shadow 0.2s ease-out;
        }

        .member-kta-preview-link:hover,
        .member-kta-preview-link:focus-visible {
            box-shadow: 0 0 0 3px rgba(var(--bs-primary-rgb), 0.45);
        }

        .member-kta-empty {
            aspect-ratio: 171.2 / 107.96;
        }

        .member-doc-thumb {
            width: 2.375rem;
            height: 2.375rem;
            object-fit: cover;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.querySelectorAll('[data-kta-preview]').forEach((el) => {
            new ResizeObserver(([entry]) => {
                el.style.setProperty('--kta-scale', entry.contentRect.width / 647);
            }).observe(el);
        });
    </script>
@endpush

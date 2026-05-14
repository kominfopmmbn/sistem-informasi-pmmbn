@extends('admin.layouts.app')

@section('title', 'Ubah Aktivasi Anggota')

@section('content')
{{-- notes penolakan --}}
@if ($member->currentStatus?->isRejected())
    <div class="alert alert-danger">
        <p class="mb-0">Alasan ditolak: {{ $member->currentStatus?->notes }}</p>
    </div>
@endif
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Ubah Aktivasi Anggota</h5>
            {!! $member->currentStatus?->status_badge ?? '—' !!}
        </div>
        {{-- Form hapus dokumen tidak boleh bersarang di dalam #member-form (HTML mengunci form luar). Tombol pakai atribut form="..." --}}
        @can('members.update')
            @foreach ($member->getMedia(\App\Models\Member::SUPPORTING_DOCUMENTS_COLLECTION) as $supportingDeleteMedia)
                <form id="member-supporting-delete-{{ $supportingDeleteMedia->getKey() }}"
                    action="{{ route('admin.member-activations.supporting-media.destroy', ['member_activation' => $member, 'media' => $supportingDeleteMedia]) }}"
                    method="POST" class="d-none" aria-hidden="true">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        @endcan
        <form action="{{ route('admin.member-activations.update', ['member_activation' => $member]) }}" method="POST"
            id="member-form" class="card-body" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')
            @include('admin.member-activations._form', ['member' => $member])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @if ($member->currentStatus?->isPending())
                    @can('members.update')
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    @endcan
                @endif
                @can('members.view')
                    <a href="{{ route('admin.member-activations.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>

    @if ($member->currentStatus?->isPending())
        <form action="{{ route('admin.member-activations.accept', ['member_activation' => $member]) }}" method="post"
            onsubmit="return confirm('Terima Anggota Ini?');">
            @csrf
            @method('PATCH')
            <div class="card">
                <div class="card-datatable text-nowrap table-responsive">
                    <table class="datatables-basic table table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th></th>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Email</th>
                                <th>Nama Lengkap</th>
                                <th>Nama Panggilan</th>
                                <th>Tempat Lahir</th>
                                <th>Tanggal Lahir</th>
                                <th>Jenis Kelamin</th>
                                <th>Wilayah Organisasi</th>
                                <th>Nomor Telepon</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                <button type="submit" class="btn btn-primary">Terima</button>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                    data-bs-target="#rejectModal">Tolak</button>
            </div>
        </form>
    @endif
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Aktivasi Anggota</h5>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.member-activations.reject', ['member_activation' => $member]) }}" method="post" onsubmit="return confirm('Tolak Aktivasi Anggota Ini?');" id="rejectForm">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <label for="notes">Alasan ditolak</label>
                            <textarea name="notes" class="form-control" placeholder="Alasan ditolak"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger" form="rejectForm">Tolak</button>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script>
        $(document).ready(function() {
            @if ($member->currentStatus?->isPending())
                let dt_basic = new DataTable(document.querySelector('.datatables-basic'), {
                    width: '100%',
                    processing: true,
                    serverSide: true,
                    language: {
                        emptyTable: 'Data Member Tidak Ditemukan, Silahkan Cari dan Cocokan Secara Manual',
                    },
                    ajax: {
                        url: "{{ route('admin.member-activations.suggestion-member', ['member_activation' => $member]) }}",
                        data: function(d) {
                            d.query = $('input[name="query"]').val();
                        }
                    },
                    columns: [{
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nim',
                            name: 'nim'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'full_name',
                            name: 'full_name'
                        },
                        {
                            data: 'nickname',
                            name: 'nickname'
                        },
                        {
                            data: 'place_of_birth_code',
                            name: 'place_of_birth_code'
                        },
                        {
                            data: 'date_of_birth',
                            name: 'date_of_birth'
                        },
                        {
                            data: 'gender_id',
                            name: 'gender_id'
                        },
                        {
                            data: 'org_region.name',
                            name: 'org_region.name',
                            defaultContent: '—',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'phone_number',
                            name: 'phone_number'
                        },
                    ],
                    columnDefs: [{
                        // Satu baris terpilih (radio, bukan plugin checkbox multi-select)
                        targets: 0,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 3,
                        render: function(data) {
                            return `<input type="radio" name="member_id" class="form-check-input" value="${data}">`;
                        },
                    }, ],
                });
            @endif
        });
    </script>
@endpush

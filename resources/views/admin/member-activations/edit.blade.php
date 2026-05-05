@extends('admin.layouts.app')

@section('title', 'Ubah Aktivasi Anggota')

@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Ubah Aktivasi Anggota</h5>
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
                @can('members.update')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('members.view')
                    <a href="{{ route('admin.member-activations.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>

    <!-- DataTable with Buttons -->
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
                        data: 'org_region_id',
                        name: 'org_region_id'
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
                        return `<input type="radio" name="member_activation_suggestion" class="form-check-input" value="${data}">`;
                    },
                }, ],
            });
        });
    </script>
@endpush

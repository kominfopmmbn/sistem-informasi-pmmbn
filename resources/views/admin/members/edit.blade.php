@extends('admin.layouts.app')

@section('title', 'Ubah anggota')

@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Ubah anggota</h5>
        </div>
        {{-- Form hapus dokumen tidak boleh bersarang di dalam #member-form (HTML mengunci form luar). Tombol pakai atribut form="..." --}}
        @can('members.update')
            @foreach ($member->getMedia(\App\Models\Member::SUPPORTING_DOCUMENTS_COLLECTION) as $supportingDeleteMedia)
                <form id="member-supporting-delete-{{ $supportingDeleteMedia->getKey() }}"
                    action="{{ route('admin.members.supporting-media.destroy', [$member, $supportingDeleteMedia]) }}"
                    method="POST" class="d-none" aria-hidden="true">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        @endcan
        <form action="{{ route('admin.members.update', $member) }}" method="POST" id="member-form"
            class="card-body" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')
            @include('admin.members._form', ['member' => $member])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('members.update')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('members.view')
                    <a href="{{ route('admin.members.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

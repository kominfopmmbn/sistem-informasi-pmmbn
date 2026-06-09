@extends('admin.layouts.app')

@section('title', 'Ubah program')

@section('content')
    {{-- Form hapus per-gambar galeri (di luar form utama agar tidak nested). --}}
    @foreach ($program->getMedia(\App\Models\Program::GALLERY_COLLECTION) as $galleryDeleteMedia)
        <form id="program-gallery-delete-{{ $galleryDeleteMedia->getKey() }}"
            action="{{ route('admin.programs.media.destroy', [$program, $galleryDeleteMedia]) }}" method="POST"
            class="d-none" aria-hidden="true">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Ubah program</h5>
            <small class="text-body-secondary d-none d-md-inline text-truncate" style="max-width: 50%">{{ $program->title }}</small>
        </div>
        <form id="program-form" action="{{ route('admin.programs.update', $program) }}" method="POST"
            enctype="multipart/form-data" class="card-body" novalidate>
            @csrf
            @method('PUT')
            @include('admin.programs._form', ['program' => $program])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('programs.update')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('programs.view')
                    <a href="{{ route('admin.programs.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Tambah banner')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
        <div>
            <h4 class="mb-1">Tambah banner</h4>
            <p class="mb-0 text-body-secondary">Tampil sebagai popup di semua halaman publik selama aktif.</p>
        </div>
        <div class="d-flex flex-wrap gap-3">
            @can('banners.view')
                <a href="{{ route('admin.banners.index') }}" class="btn btn-label-secondary">Batal</a>
            @endcan
            @can('banners.create')
                <button type="submit" form="banner-form" class="btn btn-primary">Simpan</button>
            @endcan
        </div>
    </div>

    <form id="banner-form" action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data"
        novalidate>
        @csrf
        @include('admin.banners._form', ['banner' => null])
    </form>
@endsection

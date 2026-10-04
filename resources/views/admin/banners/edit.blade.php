@extends('admin.layouts.app')

@section('title', 'Ubah banner')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
        <div class="min-w-0">
            <h4 class="mb-1">Ubah banner</h4>
            <p class="mb-0 text-body-secondary text-truncate">{{ $banner->title }}</p>
        </div>
        <div class="d-flex flex-wrap gap-3">
            <a href="{{ $banner->shareUrl() }}" target="_blank" rel="noopener" class="btn btn-label-secondary">
                <i class="icon-base bx bx-link-external me-1"></i> Lihat di situs
            </a>
            @can('banners.view')
                <a href="{{ route('admin.banners.index') }}" class="btn btn-label-secondary">Batal</a>
            @endcan
            @can('banners.update')
                <button type="submit" form="banner-form" class="btn btn-primary">Simpan</button>
            @endcan
        </div>
    </div>

    <form id="banner-form" action="{{ route('admin.banners.update', $banner) }}" method="POST"
        enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        @include('admin.banners._form', ['banner' => $banner])
    </form>
@endsection

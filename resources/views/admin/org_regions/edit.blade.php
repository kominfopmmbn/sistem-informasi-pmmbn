@extends('admin.layouts.app')

@section('title', 'Edit wilayah organisasi')

@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit wilayah organisasi</h5>
            <small class="text-body-secondary d-none d-md-inline">{{ $orgRegion->name }}</small>
        </div>
        <form action="{{ route('admin.org-regions.update', $orgRegion) }}" method="POST" class="card-body" novalidate>
            @csrf
            @method('PUT')
            @include('admin.org_regions._form', ['orgRegion' => $orgRegion])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('org_regions.update')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('org_regions.view')
                    <a href="{{ route('admin.org-regions.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

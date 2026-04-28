@extends('admin.layouts.app')

@section('title', 'Tambah wilayah organisasi')

@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tambah wilayah organisasi</h5>
            <small class="text-body-secondary d-none d-md-inline">Referensi wilayah</small>
        </div>
        <form action="{{ route('admin.org-regions.store') }}" method="POST" class="card-body" novalidate>
            @csrf
            @include('admin.org_regions._form', ['orgRegion' => null])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('org_regions.create')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('org_regions.view')
                    <a href="{{ route('admin.org-regions.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

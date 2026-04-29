@extends('admin.layouts.app')

@section('title', 'Tambah kecamatan')

@section('content')
    <div class="card mb-6">
        <div class="card-header">
            <h5 class="mb-0">Tambah kecamatan</h5>
        </div>
        <form action="{{ route('admin.districts.store') }}" method="POST" class="card-body" novalidate>
            @csrf
            @include('admin.regions.districts._form', ['district' => null, 'provinces' => $provinces])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('districts.create')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('districts.view')
                    <a href="{{ route('admin.districts.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

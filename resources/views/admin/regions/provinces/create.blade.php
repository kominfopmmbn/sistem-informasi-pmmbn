@extends('admin.layouts.app')

@section('title', 'Tambah provinsi')

@section('content')
    <div class="card mb-6">
        <div class="card-header">
            <h5 class="mb-0">Tambah provinsi</h5>
        </div>
        <form action="{{ route('admin.provinces.store') }}" method="POST" class="card-body" novalidate>
            @csrf
            @include('admin.regions.provinces._form')

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('provinces.create')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('provinces.view')
                    <a href="{{ route('admin.provinces.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

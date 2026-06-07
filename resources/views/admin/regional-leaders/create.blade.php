@extends('admin.layouts.app')

@section('title', 'Tambah pimpinan wilayah')

@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tambah pimpinan wilayah</h5>
        </div>
        <form action="{{ route('admin.regional-leaders.store') }}" method="POST" class="card-body" novalidate>
            @csrf
            @include('admin.regional-leaders._form', ['regionalLeader' => null])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('regional-leaders.create')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('regional-leaders.view')
                    <a href="{{ route('admin.regional-leaders.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

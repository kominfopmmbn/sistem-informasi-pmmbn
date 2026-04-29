@extends('admin.layouts.app')

@section('title', 'Tambah kota/kabupaten')

@section('content')
    <div class="card mb-6">
        <div class="card-header">
            <h5 class="mb-0">Tambah kota/kabupaten</h5>
        </div>
        <form action="{{ route('admin.cities.store') }}" method="POST" class="card-body" novalidate>
            @csrf
            @include('admin.regions.cities._form', ['city' => null, 'provinces' => $provinces])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('cities.create')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('cities.view')
                    <a href="{{ route('admin.cities.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

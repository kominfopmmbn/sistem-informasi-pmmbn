@extends('admin.layouts.app')

@section('title', 'Tambah banner')

@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tambah banner</h5>
        </div>
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data"
            class="card-body" novalidate>
            @csrf
            @include('admin.banners._form', ['banner' => null])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('banners.create')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('banners.view')
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

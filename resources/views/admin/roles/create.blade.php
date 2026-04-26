@extends('admin.layouts.app')

@section('title', 'Tambah peran')

@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tambah peran</h5>
            <small class="text-body-secondary d-none d-md-inline">Hak akses (Spatie)</small>
        </div>
        <form action="{{ route('admin.roles.store') }}" method="POST" class="card-body" novalidate>
            @csrf
            @include('admin.roles._form', ['role' => null])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('roles.create')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('roles.view')
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

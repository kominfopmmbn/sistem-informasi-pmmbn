@extends('admin.layouts.app')

@section('title', 'Tambah pengguna')

@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tambah pengguna</h5>
            <small class="text-body-secondary d-none d-md-inline">Akun admin</small>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST" class="card-body" novalidate>
            @csrf
            @include('admin.users._form', ['user' => null, 'roles' => $roles])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('users.create')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('users.view')
                    <a href="{{ route('admin.users.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

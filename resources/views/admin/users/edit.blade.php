@extends('admin.layouts.app')

@section('title', 'Edit pengguna')

@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit pengguna</h5>
            <small class="text-body-secondary d-none d-md-inline text-truncate" style="max-width: 50%">{{ $user->email }}</small>
        </div>
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="card-body" novalidate>
            @csrf
            @method('PUT')
            @include('admin.users._form', ['user' => $user, 'roles' => $roles])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('users.update')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('users.view')
                    <a href="{{ route('admin.users.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

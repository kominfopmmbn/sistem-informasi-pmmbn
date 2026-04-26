@extends('admin.layouts.app')

@section('title', 'Edit peran')

@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit peran</h5>
            <small class="text-body-secondary d-none d-md-inline">{{ $role->name }}</small>
        </div>
        <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="card-body" novalidate>
            @csrf
            @method('PUT')
            @include('admin.roles._form', ['role' => $role])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('roles.update')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('roles.view')
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

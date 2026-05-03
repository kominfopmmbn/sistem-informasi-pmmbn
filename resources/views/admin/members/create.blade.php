@extends('admin.layouts.app')

@section('title', 'Tambah anggota')

@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tambah anggota</h5>
        </div>
        <form action="{{ route('admin.members.store') }}" method="POST" id="member-form" class="card-body"
            enctype="multipart/form-data" novalidate>
            @csrf
            @include('admin.members._form', ['member' => null])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('members.create')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('members.view')
                    <a href="{{ route('admin.members.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

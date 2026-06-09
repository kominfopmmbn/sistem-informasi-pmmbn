@extends('admin.layouts.app')

@section('title', 'Tambah program')

@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tambah program</h5>
        </div>
        <form id="program-form" action="{{ route('admin.programs.store') }}" method="POST"
            enctype="multipart/form-data" class="card-body" novalidate>
            @csrf
            @include('admin.programs._form', ['program' => null])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('programs.create')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('programs.view')
                    <a href="{{ route('admin.programs.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

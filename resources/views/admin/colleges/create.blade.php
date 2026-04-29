@extends('admin.layouts.app')

@section('title', 'Tambah perguruan tinggi')

@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tambah perguruan tinggi</h5>
        </div>
        <form action="{{ route('admin.colleges.store') }}" method="POST" id="college-form" class="card-body"
            novalidate>
            @csrf
            @include('admin.colleges._form', ['college' => null])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('colleges.create')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('colleges.view')
                    <a href="{{ route('admin.colleges.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

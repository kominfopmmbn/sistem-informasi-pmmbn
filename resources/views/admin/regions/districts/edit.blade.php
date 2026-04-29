@extends('admin.layouts.app')

@section('title', 'Edit kecamatan')

@section('content')
    <div class="card mb-6">
        <div class="card-header">
            <h5 class="mb-0">Edit kecamatan</h5>
        </div>
        <form action="{{ route('admin.districts.update', $district) }}" method="POST" class="card-body"
            novalidate>
            @csrf
            @method('PUT')
            @include('admin.regions.districts._form', ['district' => $district, 'provinces' => $provinces])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('districts.update')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('districts.view')
                    <a href="{{ route('admin.districts.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

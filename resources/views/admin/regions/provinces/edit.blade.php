@extends('admin.layouts.app')

@section('title', 'Edit provinsi')

@section('content')
    <div class="card mb-6">
        <div class="card-header">
            <h5 class="mb-0">Edit provinsi</h5>
        </div>
        <form action="{{ route('admin.provinces.update', $province) }}" method="POST" class="card-body"
            novalidate>
            @csrf
            @method('PUT')
            @include('admin.regions.provinces._form', ['province' => $province])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('provinces.update')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('provinces.view')
                    <a href="{{ route('admin.provinces.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

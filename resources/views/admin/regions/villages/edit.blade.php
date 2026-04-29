@extends('admin.layouts.app')

@section('title', 'Edit desa/kelurahan')

@section('content')
    <div class="card mb-6">
        <div class="card-header">
            <h5 class="mb-0">Edit desa/kelurahan</h5>
        </div>
        <form action="{{ route('admin.villages.update', $village) }}" method="POST" class="card-body" novalidate>
            @csrf
            @method('PUT')
            @include('admin.regions.villages._form', ['village' => $village, 'provinces' => $provinces])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('villages.update')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('villages.view')
                    <a href="{{ route('admin.villages.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

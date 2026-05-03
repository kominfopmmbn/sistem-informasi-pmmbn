@extends('admin.layouts.app')

@section('title', 'Tambah dokumen')

@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tambah dokumen</h5>
        </div>
        <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data" class="card-body"
            novalidate>
            @csrf
            @include('admin.documents._form', ['document' => null])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('documents.create')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('documents.view')
                    <a href="{{ route('admin.documents.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

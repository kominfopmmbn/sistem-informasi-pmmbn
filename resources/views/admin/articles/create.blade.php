@extends('admin.layouts.app')

@section('title', 'Tambah artikel')

@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tambah artikel</h5>
            <small class="text-body-secondary d-none d-md-inline">Isi data berita atau opini</small>
        </div>
        <form id="article-form" action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="card-body" novalidate>
            @csrf
            @include('admin.articles._form', ['article' => null])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                <button type="submit" name="save_action" value="publish" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.articles.index') }}" class="btn btn-label-secondary">Batal</a>
                <button type="submit" name="save_action" value="draft" class="btn btn-secondary">Simpan Draft</button>
            </div>
        </form>
    </div>
@endsection

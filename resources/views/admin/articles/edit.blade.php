@extends('admin.layouts.app')

@section('title', 'Edit artikel')

@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit artikel</h5>
            <small class="text-body-secondary d-none d-md-inline text-truncate" style="max-width: 50%">{{ $article->title }}</small>
        </div>
        <form id="article-form" action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="card-body" novalidate>
            @csrf
            @method('PUT')
            @include('admin.articles._form', ['article' => $article])

            <div class="pt-6 d-flex flex-wrap align-items-center gap-2">
                @can('articles.update')
                    <button type="submit" name="save_action" value="publish" class="btn btn-primary">Simpan</button>
                    <button type="submit" name="save_action" value="draft" class="btn btn-secondary">Simpan Draft</button>
                @endcan
                @can('articles.view')
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-label-secondary">Batal</a>
                @endcan
            </div>
        </form>
    </div>
@endsection

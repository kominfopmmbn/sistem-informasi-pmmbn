@extends('admin.layouts.app')

@section('title', 'Berita & Opini')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <h4 class="fw-bold mb-0 py-3">Berita & Opini</h4>
        {{-- @can('articles.create')
            <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">
                <i class="icon-base bx bx-plus me-1"></i> Tambah artikel
            </a>
        @endcan --}}
    </div>



    <form method="get" action="{{ route('admin.articles.index') }}" class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label" for="filter_q">Judul</label>
                    <input type="search" name="q" id="filter_q" class="form-control" placeholder="Cari judul…"
                        value="{{ $filterState['q'] }}">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label" for="filter_category_id">Kategori</label>
                    <div class="select2-primary">
                        <div class="position-relative w-100">
                            <select name="category_id" id="filter_category_id"
                                class="select2 form-select">
                                <option value="">Semua kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected((string) ($filterState['category_id'] ?? '') === (string) $cat->id)>
                                        {{ $cat->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label" for="filter_archive_status">Status</label>
                    <div class="select2-primary">
                        <div class="position-relative w-100">
                            <select name="archive_status" id="filter_archive_status" class="form-select select2">
                                <option value="active" @selected(($filterState['archive_status'] ?? 'active') === 'active')>Aktif</option>
                                <option value="archived" @selected(($filterState['archive_status'] ?? 'active') === 'archived')>Arsip</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3 d-flex flex-wrap justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">Terapkan</button>
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-label-secondary">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Tanggal Publikasi</th>
                        <th>Tanggal Dibuat</th>
                        <th>Dibuat oleh</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($articles as $article)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-medium">{{ $article->title }}</span>
                                @if ($article->subtitle)
                                    <div class="small text-body-secondary text-truncate" style="max-width: 28rem">
                                        {{ $article->subtitle }}</div>
                                @endif
                            </td>
                            <td>{{ $article->category?->title ?? '—' }}</td>
                            <td>
                                @if ($article->is_draft)
                                    <span class="badge bg-label-secondary">Draf</span>
                                    @if ($article->published_at)
                                        <div class="small text-body-secondary mt-1">Jadwal: {{ $article->published_at->translatedFormat('d M Y, H:i') }}</div>
                                    @endif
                                @elseif ($article->published_at)
                                    <span class="badge bg-label-success">{{ $article->published_at->translatedFormat('d M Y, H:i') }}</span>
                                @else
                                    <span class="text-body-secondary">—</span>
                                @endif
                                @if ($article->archived_at)
                                    <div class="small text-body-secondary mt-1">Diarsipkan:
                                        {{ $article->archived_at->translatedFormat('d M Y, H:i') }}</div>
                                @endif
                            </td>
                            <td>{{ $article->created_at->translatedFormat('d M Y, H:i') }}</td>
                            <td>{{ $article->createdBy?->name ?? '-' }}</td>
                            <td class="text-end">
                                @can('update', $article)
                                    <a href="{{ route('admin.articles.edit', $article) }}"
                                        class="btn btn-sm btn-icon btn-text-secondary" title="Edit">
                                        <i class="icon-base bx bx-edit-alt"></i>
                                    </a>
                                    @if ($article->archived_at)
                                        <form action="{{ route('admin.articles.unarchive', $article) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Kembalikan artikel ini dari arsip?');">
                                            @csrf
                                            @method('PATCH')
                                            @if (($filterState['q'] ?? '') !== '')
                                                <input type="hidden" name="q" value="{{ $filterState['q'] }}">
                                            @endif
                                            @if (!empty($filterState['category_id']))
                                                <input type="hidden" name="category_id"
                                                    value="{{ $filterState['category_id'] }}">
                                            @endif
                                            <input type="hidden" name="archive_status"
                                                value="{{ $filterState['archive_status'] ?? 'active' }}">
                                            <button type="submit" class="btn btn-sm btn-icon btn-text-secondary"
                                                title="Batal arsip">
                                                <i class="icon-base bx bx-revision"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.articles.archive', $article) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Arsipkan artikel ini?');">
                                            @csrf
                                            @method('PATCH')
                                            @if (($filterState['q'] ?? '') !== '')
                                                <input type="hidden" name="q" value="{{ $filterState['q'] }}">
                                            @endif
                                            @if (!empty($filterState['category_id']))
                                                <input type="hidden" name="category_id"
                                                    value="{{ $filterState['category_id'] }}">
                                            @endif
                                            <input type="hidden" name="archive_status"
                                                value="{{ $filterState['archive_status'] ?? 'active' }}">
                                            <button type="submit" class="btn btn-sm btn-icon btn-text-secondary"
                                                title="Arsipkan">
                                                <i class="icon-base bx bx-archive"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                                @can('delete', $article)
                                    <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus artikel ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-text-danger" title="Hapus">
                                            <i class="icon-base bx bx-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                @if (!empty($hasActiveFilters))
                                    Tidak ada artikel yang cocok dengan filter.
                                @else
                                    Belum ada artikel.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($articles->hasPages())
            <div class="card-footer py-3 border-top">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script>
        'use strict';
        $(function() {
            const $cat = $('#filter_category_id');
            if ($cat.length && typeof $.fn.select2 !== 'undefined') {
                $cat.select2({
                    placeholder: 'Semua kategori',
                    allowClear: true,
                    dropdownParent: $cat.parent(),
                    width: '100%'
                });
            }
            const $archiveStatus = $('#filter_archive_status');
            if ($archiveStatus.length && typeof $.fn.select2 !== 'undefined') {
                $archiveStatus.select2({
                    placeholder: 'Semua status',
                    dropdownParent: $archiveStatus.parent(),
                    width: '100%'
                });
            }
        });
    </script>
@endpush

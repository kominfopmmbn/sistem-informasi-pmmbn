@php
    use App\Models\Document;
@endphp

@extends('admin.layouts.app')

@section('title', 'Dokumen')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <h4 class="fw-bold mb-0 py-3">Dokumen</h4>
        @can('documents.create')
            <a href="{{ route('admin.documents.create') }}" class="btn btn-primary">
                <i class="icon-base bx bx-plus me-1"></i> Tambah dokumen
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="get" action="{{ route('admin.documents.index') }}" class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="filter_q">Judul</label>
                    <input type="search" name="q" id="filter_q" class="form-control" placeholder="Cari judul…"
                        value="{{ $filterState['q'] }}">
                </div>
                <div class="col-12 col-md-6 d-flex flex-wrap justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">Terapkan</button>
                    <a href="{{ route('admin.documents.index') }}" class="btn btn-label-secondary">Reset</a>
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
                        <th>Berkas</th>
                        <th>Diperbarui</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($documents as $item)
                        @php
                            $mediaItem = $item->getFirstMedia(Document::FILE_COLLECTION);
                        @endphp
                        <tr>
                            <td>{{ $documents->firstItem() + $loop->index }}</td>
                            <td><span class="fw-medium">{{ $item->title }}</span></td>
                            <td>
                                @if ($mediaItem !== null)
                                    <a href="{{ $mediaItem->getUrl() }}" target="_blank" rel="noopener noreferrer"
                                        class="text-body-secondary">{{ $mediaItem->file_name }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $item->updated_at?->timezone(config('app.timezone'))?->format('d M Y H:i') ?? '—' }}
                            </td>
                            <td class="text-end">
                                @can('documents.update')
                                    <a href="{{ route('admin.documents.edit', $item) }}"
                                        class="btn btn-sm btn-icon btn-text-secondary" title="Edit">
                                        <i class="icon-base bx bx-edit-alt"></i>
                                    </a>
                                @endcan
                                @can('documents.delete')
                                    <form action="{{ route('admin.documents.destroy', $item) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Hapus dokumen ini?');">
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
                            <td colspan="5" class="text-center text-muted py-5">
                                Belum ada dokumen.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($documents->hasPages())
            <div class="card-footer py-3 border-top">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
@endsection

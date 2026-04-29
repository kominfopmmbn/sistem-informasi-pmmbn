@extends('admin.layouts.app')

@section('title', 'Provinsi')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <h4 class="fw-bold mb-0 py-3">Provinsi</h4>
        @can('provinces.create')
            <a href="{{ route('admin.provinces.create') }}" class="btn btn-primary">
                <i class="icon-base bx bx-plus me-1"></i> Tambah provinsi
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

    <form method="get" action="{{ route('admin.provinces.index') }}" id="provinces-index-filter-form" class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="filter_q">Nama</label>
                    <input type="search" name="q" id="filter_q" class="form-control" placeholder="Cari nama…"
                        value="{{ $filterState['q'] }}">
                </div>
                <div class="col-12 col-md-4 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">Terapkan</button>
                    <a href="{{ route('admin.provinces.index') }}" class="btn btn-label-secondary">Reset</a>
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
                        <th>Kode</th>
                        <th>Nama</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($provinces as $item)
                        <tr>
                            <td>{{ $provinces->firstItem() + $loop->index }}</td>
                            <td><code class="text-body">{{ $item->code }}</code></td>
                            <td><span class="fw-medium">{{ $item->name }}</span></td>
                            <td class="text-end">
                                @can('provinces.update')
                                    <a href="{{ route('admin.provinces.edit', $item) }}"
                                        class="btn btn-sm btn-icon btn-text-secondary" title="Edit">
                                        <i class="icon-base bx bx-edit-alt"></i>
                                    </a>
                                @endcan
                                @can('provinces.delete')
                                    <form action="{{ route('admin.provinces.destroy', $item) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Hapus provinsi ini?');">
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
                            <td colspan="4" class="text-center text-muted py-5">Tidak ada data provinsi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($provinces->hasPages())
            <div class="card-footer py-3 border-top">
                {{ $provinces->links() }}
            </div>
        @endif
    </div>
@endsection

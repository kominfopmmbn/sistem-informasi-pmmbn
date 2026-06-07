@extends('admin.layouts.app')

@section('title', 'Pimpinan Wilayah')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <h4 class="fw-bold mb-0 py-3">Pimpinan Wilayah</h4>
        @can('regional-leaders.create')
            <a href="{{ route('admin.regional-leaders.create') }}" class="btn btn-primary">
                <i class="icon-base bx bx-plus me-1"></i> Tambah pimpinan wilayah
            </a>
        @endcan
    </div>

    <form method="get" action="{{ route('admin.regional-leaders.index') }}" class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="filter_q">Kode / Nama</label>
                    <input type="search" name="q" id="filter_q" class="form-control" placeholder="Cari kode atau nama…"
                        value="{{ $filterState['q'] }}">
                </div>
                <div class="col-12 col-md-8 d-flex flex-wrap justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">Terapkan</button>
                    <a href="{{ route('admin.regional-leaders.index') }}" class="btn btn-label-secondary">Reset</a>
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
                    @forelse ($regionalLeaders as $item)
                        <tr>
                            <td>{{ $regionalLeaders->firstItem() + $loop->index }}</td>
                            <td><span class="fw-medium">{{ $item->code }}</span></td>
                            <td>{{ $item->name }}</td>
                            <td class="text-end">
                                @can('regional-leaders.update')
                                    <a href="{{ route('admin.regional-leaders.edit', $item) }}"
                                        class="btn btn-sm btn-icon btn-text-secondary" title="Edit">
                                        <i class="icon-base bx bx-edit-alt"></i>
                                    </a>
                                @endcan
                                @can('regional-leaders.delete')
                                    <form action="{{ route('admin.regional-leaders.destroy', $item) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Hapus pimpinan wilayah ini?');">
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
                            <td colspan="4" class="text-center text-muted py-5">
                                Belum ada pimpinan wilayah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($regionalLeaders->hasPages())
            <div class="card-footer py-3 border-top">
                {{ $regionalLeaders->links() }}
            </div>
        @endif
    </div>
@endsection

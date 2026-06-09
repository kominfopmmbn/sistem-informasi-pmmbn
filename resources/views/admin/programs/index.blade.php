@extends('admin.layouts.app')

@section('title', 'Program Unggulan')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <h4 class="fw-bold mb-0 py-3">Program Unggulan</h4>
        @can('programs.create')
            <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
                <i class="icon-base bx bx-plus me-1"></i> Tambah program
            </a>
        @endcan
    </div>

    <form method="get" action="{{ route('admin.programs.index') }}" class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="filter_q">Judul</label>
                    <input type="search" name="q" id="filter_q" class="form-control" placeholder="Cari judul…"
                        value="{{ $filterState['q'] }}">
                </div>
                <div class="col-12 col-md-8 d-flex flex-wrap justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">Terapkan</button>
                    <a href="{{ route('admin.programs.index') }}" class="btn btn-label-secondary">Reset</a>
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
                        <th>Status</th>
                        <th>Urutan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($programs as $program)
                        <tr>
                            <td>{{ $programs->firstItem() + $loop->index }}</td>
                            <td>
                                <span class="fw-medium">{{ $program->title }}</span>
                                <div class="small text-body-secondary text-truncate" style="max-width: 28rem">
                                    {{ \Illuminate\Support\Str::limit($program->excerpt, 80) }}</div>
                            </td>
                            <td>
                                @if ($program->is_active)
                                    <span class="badge bg-label-success">Aktif</span>
                                @else
                                    <span class="badge bg-label-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>{{ $program->sort_order }}</td>
                            <td class="text-end">
                                @can('programs.update')
                                    <a href="{{ route('admin.programs.edit', $program) }}"
                                        class="btn btn-sm btn-icon btn-text-secondary" title="Edit">
                                        <i class="icon-base bx bx-edit-alt"></i>
                                    </a>
                                @endcan
                                @can('programs.delete')
                                    <form action="{{ route('admin.programs.destroy', $program) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Hapus program ini?');">
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
                                @if (($filterState['q'] ?? '') !== '')
                                    Tidak ada program yang cocok dengan filter.
                                @else
                                    Belum ada program.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($programs->hasPages())
            <div class="card-footer py-3 border-top">
                {{ $programs->links() }}
            </div>
        @endif
    </div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Anggota')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <h4 class="fw-bold mb-0 py-3">Anggota</h4>
        @can('members.create')
            <a href="{{ route('admin.members.create') }}" class="btn btn-primary">
                <i class="icon-base bx bx-plus me-1"></i> Tambah anggota
            </a>
        @endcan
    </div>



    <form method="get" action="{{ route('admin.members.index') }}" class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="filter_q">NIM / nama / email</label>
                    <input type="search" name="q" id="filter_q" class="form-control" placeholder="Cari…"
                        value="{{ $filterState['q'] }}">
                </div>
                <div class="col-12 col-md-6 d-flex flex-wrap justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">Terapkan</button>
                    <a href="{{ route('admin.members.index') }}" class="btn btn-label-secondary">Reset</a>
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
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jenis kelamin</th>
                        <th>Wilayah org.</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($members as $item)
                        <tr>
                            <td>{{ $members->firstItem() + $loop->index }}</td>
                            <td>
                                @if ($item->nim)
                                    <code class="text-body">{{ $item->nim }}</code>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td><span class="fw-medium">{{ $item->full_name ?: '—' }}</span></td>
                            <td>{{ $item->email ?: '—' }}</td>
                            <td>{{ $item->gender_id?->label() ?? '—' }}</td>
                            <td>{{ $item->orgRegion?->name ?? '—' }}</td>
                            <td class="text-end">
                                @can('members.update')
                                    <a href="{{ route('admin.members.edit', $item) }}"
                                        class="btn btn-sm btn-icon btn-text-secondary" title="Edit">
                                        <i class="icon-base bx bx-edit-alt"></i>
                                    </a>
                                @endcan
                                @can('members.delete')
                                    <form action="{{ route('admin.members.destroy', $item) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Hapus anggota ini?');">
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
                                Belum ada anggota.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($members->hasPages())
            <div class="card-footer py-3 border-top">
                {{ $members->links() }}
            </div>
        @endif
    </div>
@endsection

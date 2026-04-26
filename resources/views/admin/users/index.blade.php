@extends('admin.layouts.app')

@section('title', 'Pengguna')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <h4 class="fw-bold mb-0 py-3">Pengguna</h4>
        @can('users.create')
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="icon-base bx bx-plus me-1"></i> Tambah pengguna
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

    <form method="get" action="{{ route('admin.users.index') }}" class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="filter_q">Nama atau email</label>
                    <input type="search" name="q" id="filter_q" class="form-control" placeholder="Cari…"
                        value="{{ $filterState['q'] }}">
                </div>
                <div class="col-12 col-md-6 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">Terapkan</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-label-secondary">Reset</a>
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
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Peran</th>
                        <th>Dibuat</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $users->firstItem() + $loop->index }}</td>
                            <td><span class="fw-medium">{{ $user->name }}</span></td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if ($user->roles->isNotEmpty())
                                    <span class="badge bg-label-primary">{{ $user->roles->first()->name }}</span>
                                @else
                                    <span class="text-body-secondary">—</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->translatedFormat('d M Y, H:i') }}</td>
                            <td class="text-end">
                                @can('users.update')
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="btn btn-sm btn-icon btn-text-secondary" title="Edit">
                                        <i class="icon-base bx bx-edit-alt"></i>
                                    </a>
                                @endcan
                                @can('users.delete')
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus pengguna ini?');">
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
                            <td colspan="6" class="text-center text-muted py-5">
                                @if ($hasActiveFilters)
                                    Tidak ada pengguna yang cocok dengan filter.
                                @else
                                    Belum ada pengguna.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="card-footer py-3 border-top">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection

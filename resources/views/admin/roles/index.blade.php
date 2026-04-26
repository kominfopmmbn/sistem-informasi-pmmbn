@extends('admin.layouts.app')

@section('title', 'Peran')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <h4 class="fw-bold mb-0 py-3">Peran</h4>
        @can('roles.create')
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="icon-base bx bx-plus me-1"></i> Tambah peran
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

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Jumlah permission</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($roles as $item)
                        <tr>
                            <td>{{ $roles->firstItem() + $loop->index }}</td>
                            <td><span class="fw-medium">{{ $item->name }}</span></td>
                            <td>{{ $item->permissions_count }}</td>
                            <td class="text-end">
                                @can('roles.update')
                                    <a href="{{ route('admin.roles.edit', $item) }}"
                                        class="btn btn-sm btn-icon btn-text-secondary" title="Edit">
                                        <i class="icon-base bx bx-edit-alt"></i>
                                    </a>
                                @endcan
                                @can('roles.delete')
                                    <form action="{{ route('admin.roles.destroy', $item) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus peran ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-text-danger" title="Hapus"
                                            @if ($item->name === 'Administrator') disabled
                                                aria-disabled="true" @endif>
                                            <i class="icon-base bx bx-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                Belum ada peran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($roles->hasPages())
            <div class="card-footer py-3 border-top">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
@endsection

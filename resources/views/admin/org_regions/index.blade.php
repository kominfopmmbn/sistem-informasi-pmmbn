@extends('admin.layouts.app')

@section('title', 'Wilayah organisasi')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <h4 class="fw-bold mb-0 py-3">Wilayah Organisasi</h4>
        @can('org_regions.create')
            <a href="{{ route('admin.org-regions.create') }}" class="btn btn-primary">
                <i class="icon-base bx bx-plus me-1"></i> Tambah wilayah
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
                        <th>Kode</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($orgRegions as $item)
                        <tr>
                            <td>{{ $orgRegions->firstItem() + $loop->index }}</td>
                            <td><span class="fw-medium">{{ $item->name }}</span></td>
                            <td><code class="text-body">{{ $item->code }}</code></td>
                            <td class="text-end">
                                @can('org_regions.update')
                                    <a href="{{ route('admin.org-regions.edit', $item) }}"
                                        class="btn btn-sm btn-icon btn-text-secondary" title="Edit">
                                        <i class="icon-base bx bx-edit-alt"></i>
                                    </a>
                                @endcan
                                @can('org_regions.delete')
                                    <form action="{{ route('admin.org-regions.destroy', $item) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Hapus wilayah ini?');">
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
                                Belum ada wilayah organisasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($orgRegions->hasPages())
            <div class="card-footer py-3 border-top">
                {{ $orgRegions->links() }}
            </div>
        @endif
    </div>
@endsection

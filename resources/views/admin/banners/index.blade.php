@extends('admin.layouts.app')

@section('title', 'Banner')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <h4 class="fw-bold mb-0 py-3">Banner</h4>
        @can('banners.create')
            <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                <i class="icon-base bx bx-plus me-1"></i> Tambah banner
            </a>
        @endcan
    </div>

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Judul</th>
                        <th>Link share</th>
                        <th>Status</th>
                        <th>Urutan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($banners as $banner)
                        <tr>
                            <td>{{ $banners->firstItem() + $loop->index }}</td>
                            <td>
                                <img src="{{ $banner->getFirstMediaUrl(\App\Models\Banner::IMAGE_COLLECTION) }}" alt=""
                                    class="rounded" style="height: 56px; width: 56px; object-fit: cover;">
                            </td>
                            <td>
                                <span class="fw-medium">{{ $banner->title }}</span>
                                @if ($banner->link_url)
                                    <div class="small text-body-secondary text-truncate" style="max-width: 20rem">
                                        {{ $banner->link_url }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="input-group input-group-sm" style="min-width: 18rem">
                                    <input type="text" readonly class="form-control" id="banner-share-{{ $banner->id }}"
                                        value="{{ $banner->shareUrl() }}" onfocus="this.select()">
                                    <button type="button" class="btn btn-outline-primary"
                                        data-copy-target="banner-share-{{ $banner->id }}">Salin</button>
                                </div>
                            </td>
                            <td>
                                @if ($banner->is_active)
                                    <span class="badge bg-label-success">Aktif</span>
                                @else
                                    <span class="badge bg-label-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>{{ $banner->sort_order }}</td>
                            <td class="text-end">
                                @can('banners.update')
                                    <a href="{{ route('admin.banners.edit', $banner) }}"
                                        class="btn btn-sm btn-icon btn-text-secondary" title="Edit">
                                        <i class="icon-base bx bx-edit-alt"></i>
                                    </a>
                                @endcan
                                @can('banners.delete')
                                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Hapus banner ini?');">
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
                            <td colspan="7" class="text-center text-muted py-5">Belum ada banner.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($banners->hasPages())
            <div class="card-footer py-3 border-top">
                {{ $banners->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-copy-target]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.dataset.copyTarget);
                var done = function () {
                    btn.textContent = 'Tersalin';
                    setTimeout(function () { btn.textContent = 'Salin'; }, 1500);
                };
                input.select();
                // Clipboard API hanya tersedia di HTTPS/localhost; selain itu pakai execCommand.
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(input.value).then(done);
                } else {
                    document.execCommand('copy');
                    done();
                }
            });
        });
    </script>
@endpush

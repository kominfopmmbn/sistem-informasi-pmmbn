@extends('admin.layouts.app')

@section('title', 'Perguruan tinggi')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <h4 class="fw-bold mb-0 py-3">Perguruan Tinggi</h4>
        @can('colleges.create')
            <a href="{{ route('admin.colleges.create') }}" class="btn btn-primary">
                <i class="icon-base bx bx-plus me-1"></i> Tambah perguruan tinggi
            </a>
        @endcan
    </div>



    <form method="get" action="{{ route('admin.colleges.index') }}" id="colleges-index-filter-form" class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label" for="filter_q">Nama</label>
                    <input type="search" name="q" id="filter_q" class="form-control" placeholder="Cari nama…"
                        value="{{ $filterState['q'] }}">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label" for="filter_province_code">Provinsi</label>
                    <div class="select2-primary">
                        <div class="position-relative w-100">
                            <select name="province_code" id="filter_province_code" class="select2 form-select"
                                data-placeholder="Semua provinsi">
                                <option value=""></option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->code }}" @selected($filterState['province_code'] !== null && (string) $filterState['province_code'] === (string) $province->code)>
                                        {{ $province->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label" for="filter_city_code">Kota / Kabupaten</label>
                    <div class="select2-primary">
                        <div class="position-relative w-100">
                            <select name="city_code" id="filter_city_code" class="select2 form-select"
                                data-search-url="{{ route('select.cities') }}"
                                data-placeholder="Semua kota/kabupaten"
                                @if ($filterState['city_code'] !== null && $filterState['city_code'] !== '') data-initial-code="{{ $filterState['city_code'] }}" data-initial-name="{{ $filterCityName }}" @endif
                                @if ($filterState['province_code'] === null) disabled @endif>
                                @if ($filterState['city_code'] !== null && $filterState['city_code'] !== '')
                                    <option value="{{ $filterState['city_code'] }}" selected>{{ $filterCityName }}</option>
                                @else
                                    <option value="">Pilih provinsi terlebih dahulu untuk memfilter kota/kabupaten.</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3 d-flex flex-wrap justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">Terapkan</button>
                    <a href="{{ route('admin.colleges.index') }}" class="btn btn-label-secondary">Reset</a>
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
                        <th>Provinsi</th>
                        <th>Kota</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($colleges as $item)
                        <tr>
                            <td>{{ $colleges->firstItem() + $loop->index }}</td>
                            <td><span class="fw-medium">{{ $item->name }}</span></td>
                            <td>{{ $item->province?->name ?? '—' }}</td>
                            <td>{{ $item->city?->name ?? '—' }}</td>
                            <td class="text-end">
                                @can('colleges.update')
                                    <a href="{{ route('admin.colleges.edit', $item) }}"
                                        class="btn btn-sm btn-icon btn-text-secondary" title="Edit">
                                        <i class="icon-base bx bx-edit-alt"></i>
                                    </a>
                                @endcan
                                @can('colleges.delete')
                                    <form action="{{ route('admin.colleges.destroy', $item) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Hapus perguruan tinggi ini?');">
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
                                Belum ada perguruan tinggi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($colleges->hasPages())
            <div class="card-footer py-3 border-top">
                {{ $colleges->links() }}
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/js/admin-colleges-index.js') }}"></script>
@endpush

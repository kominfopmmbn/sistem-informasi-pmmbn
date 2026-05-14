@extends('admin.layouts.app')

@section('title', 'Kecamatan')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <h4 class="fw-bold mb-0 py-3">Kecamatan</h4>
        @can('districts.create')
            <a href="{{ route('admin.districts.create') }}" class="btn btn-primary">
                <i class="icon-base bx bx-plus me-1"></i> Tambah kecamatan
            </a>
        @endcan
    </div>



    <form method="get" action="{{ route('admin.districts.index') }}" id="districts-index-filter-form"
        class="card mb-4">
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
                                    <option value="{{ $province->code }}"
                                        @selected($filterState['province_code'] !== null && (string) $filterState['province_code'] === (string) $province->code)>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label" for="filter_city_code">Kota / kabupaten</label>
                    <div class="select2-primary">
                        <div class="position-relative w-100">
                            <select name="city_code" id="filter_city_code" class="select2 form-select"
                                data-search-url="{{ route('select.cities') }}"
                                data-placeholder="Semua kota/kabupaten"
                                @if ($filterState['province_code'] === null) disabled @endif
                                @if ($filterState['city_code'] !== null && $filterState['city_code'] !== '') data-initial-code="{{ $filterState['city_code'] }}" data-initial-name="{{ $filterCityName }}" @endif>
                                @if ($filterState['city_code'] !== null && $filterState['city_code'] !== '')
                                    <option value="{{ $filterState['city_code'] }}" selected>{{ $filterCityName }}</option>
                                @else
                                    <option value=""></option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3 d-flex flex-wrap justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">Terapkan</button>
                    <a href="{{ route('admin.districts.index') }}" class="btn btn-label-secondary">Reset</a>
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
                        <th>Kota</th>
                        <th>Provinsi</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($districts as $item)
                        <tr>
                            <td>{{ $districts->firstItem() + $loop->index }}</td>
                            <td><code class="text-body">{{ $item->code }}</code></td>
                            <td><span class="fw-medium">{{ $item->name }}</span></td>
                            <td>{{ $item->city?->name_with_code ?? '—' }}</td>
                            <td>{{ $item->city?->province?->name_with_code ?? '—' }}</td>
                            <td class="text-end">
                                @can('districts.update')
                                    <a href="{{ route('admin.districts.edit', $item) }}"
                                        class="btn btn-sm btn-icon btn-text-secondary" title="Edit">
                                        <i class="icon-base bx bx-edit-alt"></i>
                                    </a>
                                @endcan
                                @can('districts.delete')
                                    <form action="{{ route('admin.districts.destroy', $item) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Hapus kecamatan ini?');">
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
                            <td colspan="6" class="text-center text-muted py-5">Tidak ada data kecamatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($districts->hasPages())
            <div class="card-footer py-3 border-top">
                {{ $districts->links() }}
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/js/admin-regions-districts-index.js') }}"></script>
@endpush

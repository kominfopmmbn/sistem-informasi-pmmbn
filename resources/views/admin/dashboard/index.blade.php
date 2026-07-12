@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h4 class="fw-bold py-3 mb-4">Dashboard</h4>

    @php
        $pendingUrl = route('admin.member-activations.index', ['status' => \App\Enums\MemberActivationStatus::PENDING->value]);
        $statCards = [
            ['permission' => 'members.view', 'url' => route('admin.members.index'), 'label' => 'Total Anggota', 'value' => $memberCount, 'color' => 'primary', 'icon' => 'bx-group', 'note' => null],
            ['permission' => 'member-activations.view', 'url' => $pendingUrl, 'label' => 'Aktivasi Pending', 'value' => $pendingActivationCount, 'color' => 'warning', 'icon' => 'bx-user-plus', 'note' => $pendingActivationCount > 0 ? 'Perlu direview' : null],
            ['permission' => 'colleges.view', 'url' => route('admin.colleges.index'), 'label' => 'Perguruan Tinggi', 'value' => $collegeCount, 'color' => 'info', 'icon' => 'bx-building-house', 'note' => null],
            ['permission' => 'articles.view', 'url' => route('admin.articles.index'), 'label' => 'Artikel Terbit', 'value' => $publishedArticleCount, 'color' => 'success', 'icon' => 'bx-news', 'note' => null],
        ];
    @endphp

    <div class="row g-4 mb-4">
        @foreach ($statCards as $card)
            @can($card['permission'])
                <div class="col-sm-6 col-xl-3">
                    <a href="{{ $card['url'] }}" class="card h-100 text-body text-decoration-none">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="card-info">
                                    <p class="text-heading mb-1">{{ $card['label'] }}</p>
                                    <h4 class="card-title mb-0">{{ number_format($card['value'], 0, ',', '.') }}</h4>
                                    @if ($card['note'])
                                        <span class="text-{{ $card['color'] }} small">{{ $card['note'] }}</span>
                                    @endif
                                </div>
                                <div class="card-icon">
                                    <span class="badge bg-label-{{ $card['color'] }} rounded p-2">
                                        <i class="icon-base bx {{ $card['icon'] }} icon-lg"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan
        @endforeach
    </div>

    @can('member-activations.view')
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Aktivasi perlu ditindak</h5>
                <a href="{{ route('admin.member-activations.index', ['status' => \App\Enums\MemberActivationStatus::PENDING->value]) }}"
                    class="btn btn-sm btn-text-primary">Lihat semua</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Perguruan Tinggi</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($pendingActivations as $activation)
                            <tr>
                                <td><span class="fw-medium">{{ $activation->full_name ?: '—' }}</span></td>
                                <td>{{ $activation->college?->name ?: ($activation->college_other ?: '—') }}</td>
                                <td>
                                    @if ($activation->currentStatus)
                                        {!! $activation->currentStatus->status_badge !!}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $activation->created_at?->translatedFormat('d M Y') ?? '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.member-activations.edit', $activation) }}"
                                        class="btn btn-sm btn-icon btn-text-secondary" title="Review">
                                        <i class="icon-base bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    Tidak ada aktivasi menunggu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endcan
@endsection

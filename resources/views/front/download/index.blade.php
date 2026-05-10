@php
    use App\Models\Document;
@endphp

@extends('front.layouts.app', ['bodyClass' => 'page-download'])

@section('title', 'Dokumen Resmi')

@section('content')
    <section class="container download-hero-section">
        <div class="hero-card" data-aos="fade-down" data-aos-duration="800">
            <div class="hero-content">
                <div class="hero-subtitle" data-aos="fade-right" data-aos-delay="200" data-aos-duration="600">Download</div>
                <h1 class="hero-title" data-aos="fade-right" data-aos-delay="400" data-aos-duration="600">Dokumen Resmi PMMBN</h1>
                <p class="hero-desc" data-aos="fade-right" data-aos-delay="600" data-aos-duration="600">
                    Unduh berbagai dokumen resmi, panduan kegiatan, dan materi organisasi Pergerakan
                    Mahasiswa Moderasi Beragama dan Bela Negara.
                </p>
            </div>
        </div>
    </section>

    <main class="container mb-5">
        <section class="download-grid-section">
            <div class="download-grid">
                @forelse ($documents as $document)
                    @php
                        $media = $document->getFirstMedia(Document::FILE_COLLECTION);
                        $ext = strtoupper($media->extension ?: pathinfo($media->file_name, PATHINFO_EXTENSION) ?: 'file');
                        $iconClass = strtolower((string) $media->extension) === 'pdf' ? 'bi-file-earmark-pdf' : 'bi-file-earmark-text';
                    @endphp
                    <article class="download-card" data-aos="fade-up" data-aos-duration="600" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="download-card-main">
                            <span class="download-file-icon"><i class="bi {{ $iconClass }}"></i></span>
                            <div class="download-meta">
                                <h3 class="download-title">{{ $document->title }}</h3>
                                <p class="download-detail">{{ $media->human_readable_size }} · {{ $ext }}</p>
                            </div>
                        </div>
                        <a href="{{ $media->getUrl() }}" class="btn-open" download="{{ $media->file_name }}">Download</a>
                    </article>
                @empty
                    <p class="text-center text-muted py-5 mb-0" style="grid-column: 1 / -1;">
                        Belum ada dokumen yang dapat diunduh.
                    </p>
                @endforelse
            </div>
        </section>
    </main>
@endsection

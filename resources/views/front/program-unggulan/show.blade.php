@extends('front.layouts.app')

@section('title', $program->title . ' - Program Unggulan')

@section('content')
@php
    $cover = $program->getFirstMediaUrl(\App\Models\Program::COVER_COLLECTION);
    $gallery = $program->getMedia(\App\Models\Program::GALLERY_COLLECTION);
@endphp
<div class="container py-4">
    <div class="hero position-relative rounded-4 overflow-hidden mb-4 mb-md-5"
        @if ($cover) style="background-image: url('{{ $cover }}');" @endif>
        <div class="hero-pattern position-absolute top-0 bottom-0 start-0 end-0 z-0"></div>
        <div class="hero-overlay position-absolute top-0 bottom-0 start-0 end-0 z-1"></div>

        <div class="position-relative z-2 p-4 p-md-5 w-100">
            <span class="hero-tag badge rounded-pill text-uppercase fw-semibold mb-2 px-3 py-2">Program Unggulan</span>
            <h1 class="fs-3 display-6-md fw-bold text-white mb-2">{{ $program->title }}</h1>
        </div>
    </div>

    <div class="row g-4 g-lg-5">
        <div class="col-lg-8">

            @if ($program->about_content)
                <section class="mb-5">
                    <p class="text-maroon-light fw-bold text-uppercase mb-1"
                        style="font-size: 12px; letter-spacing: 0.1em;">Tentang Program</p>
                    <h2 class="h4 fw-bold text-dark mb-3">{{ $program->about_heading ?: $program->title }}</h2>
                    <div class="text-secondary program-about" style="line-height: 1.8;">
                        {!! $program->about_content !!}
                    </div>
                </section>
            @endif

            @if ($program->goals->isNotEmpty())
                <hr class="text-black-50 my-4">

                <section class="mb-5">
                    <p class="text-maroon-light fw-bold text-uppercase mb-1"
                        style="font-size: 12px; letter-spacing: 0.1em;">Tujuan Program</p>
                    <h2 class="h4 fw-bold text-dark mb-4">Yang Akan Kamu Capai</h2>

                    <div class="d-flex flex-column gap-3">
                        @foreach ($program->goals as $goal)
                            <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-white border shadow-sm">
                                <div
                                    class="tujuan-num rounded-circle bg-maroon-main text-white fw-bold d-flex align-items-center justify-content-center flex-shrink-0">
                                    {{ $loop->iteration }}</div>
                                <div class="text-dark" style="font-size: 14.5px; line-height: 1.6;">{{ $goal->content }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($gallery->isNotEmpty())
                <hr class="text-black-50 my-4">

                <section class="mb-5">
                    <p class="text-maroon-light fw-bold text-uppercase mb-1"
                        style="font-size: 12px; letter-spacing: 0.1em;">Galeri</p>
                    <h2 class="h4 fw-bold text-dark mb-4">Dokumentasi Kegiatan</h2>

                    <div class="galeri-grid">
                        @foreach ($gallery as $image)
                            <div class="galeri-item {{ $loop->first ? 'galeri-main' : '' }} rounded-3 border position-relative overflow-hidden">
                                <img src="{{ $image->getUrl() }}" alt="" loading="lazy"
                                    style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;">
                                <button type="button" data-galeri-index="{{ $loop->index }}"
                                    class="galeri-overlay position-absolute top-0 bottom-0 start-0 end-0 d-flex align-items-center justify-content-center z-2 w-100 h-100 p-0"
                                    aria-label="Perbesar foto">
                                    <i class="bi bi-zoom-in text-white fs-2"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- Lightbox popup galeri --}}
                <div class="modal fade galeri-lightbox" id="galeriLightbox" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content bg-transparent border-0">
                            <button type="button" class="btn-close btn-close-white galeri-lightbox-close"
                                data-bs-dismiss="modal" aria-label="Tutup"></button>
                            <div class="modal-body d-flex align-items-center justify-content-center p-0">
                                <div class="swiper galeri-swiper w-100 h-100">
                                    <div class="swiper-wrapper">
                                        @foreach ($gallery as $image)
                                            <div class="swiper-slide d-flex align-items-center justify-content-center">
                                                <img src="{{ $image->getUrl() }}" alt="" class="galeri-slide-img">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="swiper-button-prev"></div>
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-pagination"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <div class="col-lg-4">
            <div class="sticky-top" style="top: 6rem;">
                <h3 class="h6 fw-bold text-dark border-bottom pb-2 mb-3">Program Lainnya</h3>

                <div class="d-flex flex-column gap-2">
                    @forelse ($otherPrograms as $other)
                        @php
                            $otherCover = $other->getFirstMediaUrl(\App\Models\Program::COVER_COLLECTION);
                        @endphp
                        <a href="{{ route('program-unggulan.show', $other->slug) }}"
                            class="prog-card d-flex align-items-start gap-3 p-3 rounded-3 border bg-white text-decoration-none">
                            <div class="prog-img-wrapper">
                                <img src="{{ $otherCover ?: 'https://placehold.co/300x180/e0e0e0/555555?text=Program' }}"
                                    alt="{{ $other->title }}" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="flex-grow-1">
                                <div class="prog-name fw-semibold text-dark mb-1" style="font-size: 14px;">
                                    {{ $other->title }}</div>
                                <div class="text-secondary mb-0" style="font-size: 12px; line-height: 1.4;">
                                    {{ \Illuminate\Support\Str::limit($other->excerpt, 80) }}</div>
                            </div>
                        </a>
                    @empty
                        <p class="text-muted small mb-0">Belum ada program lain.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/swiper/swiper.css') }}">
<style>
    /* CSS Kustom */
    :root {
        --maroon-dark: #7B1A1A;
        --maroon-main: #9B1C1C;
        --maroon-light: #C0392B;
        --maroon-subtle: #F9E0E0;
    }

    body {
        background-color: #fcfcfc;
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    /* Hero Section - Responsif Height */
    .hero {
        position: relative;
        width: 100%;
        min-height: 400px;
        background: url('https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80') no-repeat center center / cover;
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        align-items: flex-end;
        margin-bottom: 2rem;
    }

    @media (min-width: 768px) {
        .hero {
            height: 320px;
            /* Tinggi untuk Tablet/PC */
        }
    }

    @media (max-width: 575.98px) {
        .hero {
            min-height: 260px;
            /* Kecilkan hero di HP */
        }
    }

    .col-lg-4 .sticky-top {
    z-index: 1 !important;
    }

    .hero-overlay {
        background: linear-gradient(to top, #4a0d0d 0%, transparent 60%);
    }

    .hero-pattern {
        opacity: 0.06;
        background-image: repeating-linear-gradient(45deg, #fff 0, #fff 1px, transparent 0, transparent 50%);
        background-size: 12px 12px;
    }

    .hero-tag {
        font-size: 11px;
        letter-spacing: 0.1em;
        background: rgba(255, 255, 255, 0.12);
        color: #FFAAAA;
    }

    /* Teks Spesifik */
    .text-maroon-light {
        color: var(--maroon-light) !important;
    }

    .text-maroon-main {
        color: var(--maroon-main) !important;
    }

    .bg-maroon-main {
        background-color: var(--maroon-main) !important;
    }

    .bg-maroon-subtle-custom {
        background-color: var(--maroon-subtle) !important;
    }

    /* List Tujuan */
    .tujuan-num {
        min-width: 28px;
        height: 28px;
        font-size: 13px;
    }

    /* Galeri Grid Custom - Responsif */
    .galeri-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        /* 2 Kolom untuk HP */
        gap: 8px;
    }

    @media (min-width: 768px) {
        .galeri-grid {
            grid-template-columns: repeat(3, 1fr);
            /* 3 Kolom untuk Tablet/PC */
        }
    }

    .galeri-item {
        aspect-ratio: 4/3;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .galeri-item:hover .galeri-overlay {
        opacity: 1;
    }

    .galeri-overlay {
        background: rgba(123, 26, 26, 0.5);
        opacity: 0;
        transition: opacity 0.2s;
        border: 0;
        cursor: pointer;
    }

    .galeri-main {
        grid-column: span 2;
        grid-row: span 2;
    }

    /* Lightbox popup galeri */
    .galeri-lightbox .modal-body {
        background: rgba(0, 0, 0, 0.92);
    }

    .galeri-slide-img {
        max-width: 100%;
        max-height: 100vh;
        object-fit: contain;
    }

    .galeri-lightbox-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 10;
    }

    .galeri-swiper {
        --swiper-navigation-color: #fff;
        --swiper-pagination-color: #fff;
    }

    /* Sidebar Cards */
    .prog-card {
        transition: border-color 0.2s, transform 0.2s, background-color 0.2s;
        cursor: pointer;
    }

    .prog-card:hover {
        border-color: rgba(192, 57, 43, 0.4) !important;
        transform: translateX(4px);
    }

    /* Pengaturan gambar thumbnail untuk sidebar */
    .prog-img-wrapper {
        width: 72px;
        height: 54px;
        /* Rasio 4:3 */
        border-radius: 6px;
        overflow: hidden;
        flex-shrink: 0;
        background-color: var(--maroon-subtle);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
</style>

@endpush

@push('scripts')
<script src="{{ asset('assets/vendor/libs/swiper/swiper.js') }}"></script>
<script src="{{ asset('assets/js/front-program-gallery.js') }}"></script>
@endpush

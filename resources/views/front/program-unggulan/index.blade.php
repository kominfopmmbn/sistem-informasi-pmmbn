@extends('front.layouts.app')

@section('title', 'Program Unggulan')

@section('content')

<section class="container program-hero-section">
    <div class="hero-card" data-aos="fade-down" data-aos-duration="800">
        <div class="hero-content">
            <div class="hero-subtitle" data-aos="fade-right" data-aos-delay="200" data-aos-duration="600">Program</div>
            <h1 class="hero-title" data-aos="fade-right" data-aos-delay="400" data-aos-duration="600">Program Unggulan
                PMMBN</h1>
            <p class="hero-desc" data-aos="fade-right" data-aos-delay="600" data-aos-duration="600">
                Kumpulan program pilihan yang dirancang untuk memberikan dampak nyata, solusi berkelanjutan, dan
                pengalaman
                terbaik bagi sasaran program.
            </p>
        </div>
    </div>
</section>

<div class="container mt-5 mb-5">
    <h6 class="text-brand fw-bold mb-3" style="letter-spacing: 0.08em;">PROGRAM UTAMA</h6>
    <hr class="mb-4">
    <div class="row g-4">
        @forelse ($programs as $program)
            @php
                $cover = $program->getFirstMediaUrl(\App\Models\Program::COVER_COLLECTION);
            @endphp
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="{{ route('program-unggulan.show', $program->slug) }}" class="card custom-card text-decoration-none">
                    <div class="custom-card-thumb">
                        <img src="{{ $cover ?: 'https://placehold.co/300x180/f3e9e9/8b1515?text=Program' }}"
                            class="card-img-top" alt="{{ $program->title }}">
                    </div>
                    <div class="custom-card-body flex-grow-1">
                        <div>
                            <h5 class="custom-card-title fw-bold">{{ $program->title }}</h5>
                            <p class="card-text-custom">{{ \Illuminate\Support\Str::limit($program->excerpt, 140) }}</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="custom-card-cta">Selengkapnya</span>
                            <span class="btn-arrow"><i class="bi bi-arrow-up-right"></i></span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted text-center py-5 mb-0">Belum ada program unggulan.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection

@push('styles')
    <style>
    /* Styling untuk Banner Merah */
    .program-hero-section {
        padding: 0 1rem;
        margin-top: 1rem;
    }

    .hero-card {
        border-radius: 1.5rem;
        overflow: hidden;
        position: relative;
        min-height: 400px;
        display: flex;
        align-items: center;
        background: linear-gradient(to right, rgba(94, 13, 13, 0.92), rgba(139, 21, 21, 0.4)),
            url('https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
        color: white;
        padding: 3rem;
    }

    .hero-content {
        max-width: 600px;
        z-index: 2;
    }

    .hero-subtitle {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
    }

    .hero-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .hero-desc {
        font-size: 1rem;
        opacity: 0.9;
    }

    /* Styling untuk Kartu (Card) — selaras tema maroon PMMBN */
    .custom-card {
        background-color: #ffffff;
        border: 1px solid #efe2e2;
        border-radius: 16px;
        color: #212529;
        height: 100%;
        /* Agar tinggi kartu sama rata */
        overflow: hidden;
        box-shadow: 0 4px 18px rgba(139, 21, 21, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
    }

    .custom-card:hover {
        transform: translateY(-6px);
        border-color: rgba(139, 21, 21, 0.28);
        box-shadow: 0 16px 34px rgba(139, 21, 21, 0.16);
    }

    .custom-card-thumb {
        overflow: hidden;
    }

    .custom-card img {
        object-fit: cover;
        height: 180px;
        width: 100%;
        transition: transform 0.4s ease;
    }

    .custom-card:hover img {
        transform: scale(1.05);
    }

    .custom-card-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .custom-card-title {
        color: #2b2b2b;
        font-size: 1.05rem;
        line-height: 1.4;
        margin-bottom: 0.6rem;
        transition: color 0.3s ease;
    }

    .custom-card:hover .custom-card-title {
        color: #8b1515;
    }

    .card-text-custom {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .custom-card-cta {
        font-size: 0.85rem;
        font-weight: 600;
        color: #8b1515;
    }

    /* Styling untuk Tombol Panah */
    .btn-arrow {
        border: 1px solid #8b1515;
        color: #8b1515;
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        flex-shrink: 0;
        transition: 0.3s;
    }

    .custom-card:hover .btn-arrow {
        background-color: #8b1515;
        color: #ffffff;
    }

    /* Mobile (phone) overrides */
    @media (max-width: 575.98px) {
        .hero-card {
            min-height: 260px;
            padding: 1.75rem;
        }

        .hero-title {
            font-size: 1.6rem;
        }
    }

</style>
@endpush


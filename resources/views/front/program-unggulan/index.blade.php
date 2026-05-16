@extends('front.layouts.app')

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
    <h6 class="fw-bold mb-3">PROGRAM UTAMA</h6>
    <hr class="mb-4">
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card custom-card">
                <img src="https://via.placeholder.com/300x180/e0e0e0/555555?text=Gambar+Kelas" class="card-img-top"
                    alt="Sekolah Moderasi">
                <div class="custom-card-body flex-grow-1">
                    <div>
                        <h5 class="fw-bold">Sekolah Moderasi</h5>
                        <p class="card-text-custom">Program pendidikan dan kajian interaktif untuk memahami nilai
                            toleransi, inklusivitas, dan hubungan antarumat beragama melalui diskusi, studi kasus, dan
                            dialog lintas iman.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="badge-custom">Pendidikan</span>
                        <a href="#" class="btn-arrow"><i class="bi bi-arrow-up-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card custom-card">
                <img src="https://via.placeholder.com/300x180/2c3e50/ffffff?text=Branding+UI/UX" class="card-img-top"
                    alt="Kaderisasi">
                <div class="custom-card-body flex-grow-1">
                    <div>
                        <h5 class="fw-bold">Kaderisasi</h5>
                        <p class="card-text-custom">Program pembinaan karakter kebangsaan yang menanamkan nilai cinta
                            tanah air, kesadaran konstitusi, dan tanggung jawab sosial sebagai bagian dari bela negara.
                        </p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="badge-custom">Kaderisasi</span>
                        <a href="#" class="btn-arrow"><i class="bi bi-arrow-up-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card custom-card">
                <img src="https://via.placeholder.com/300x180/8e44ad/ffffff?text=Generative+AI" class="card-img-top"
                    alt="Digital Campaign">
                <div class="custom-card-body flex-grow-1">
                    <div>
                        <h5 class="fw-bold">Digital Campaign</h5>
                        <p class="card-text-custom">Program pengembangan intelektual melalui riset, penulisan, dan
                            publikasi gagasan mahasiswa terkait moderasi beragama dan wawasan kebangsaan.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="badge-custom">Publikasi</span>
                        <a href="#" class="btn-arrow"><i class="bi bi-arrow-up-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card custom-card">
                <img src="https://via.placeholder.com/300x180/000000/ffffff?text=Steve+Jobs" class="card-img-top"
                    alt="Forum Diskusi">
                <div class="custom-card-body flex-grow-1">
                    <div>
                        <h5 class="fw-bold">Forum Diskusi Lintas Iman</h5>
                        <p class="card-text-custom">Ruang pertemuan antar mahasiswa dari berbagai latar belakang,
                            mendiskusikan isu-isu keagamaan dan kebangsaan secara terbuka.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="badge-custom">Pendidikan</span>
                        <a href="#" class="btn-arrow"><i class="bi bi-arrow-up-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

</html>
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
        background: linear-gradient(to right, rgba(150, 0, 0, 0.8), rgba(0, 100, 150, 0.2)),
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

    /* Styling untuk Kartu (Card) */
    .custom-card {
        background-color: #383333;
        /* Warna gelap kecoklatan */
        border: none;
        border-radius: 16px;
        color: white;
        height: 100%;
        /* Agar tinggi kartu sama rata */
    }

    .custom-card img {
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        object-fit: cover;
        height: 180px;
    }

    .custom-card-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .card-text-custom {
        color: #d1d1d1;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    /* Styling untuk Label/Badge di bawah kartu */
    .badge-custom {
        background-color: #fcebeb;
        color: #8b0000;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.8rem;
        text-decoration: none;
    }

    /* Styling untuk Tombol Panah */
    .btn-arrow {
        border: 1px solid #777;
        color: #ccc;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: 0.3s;
    }

    .btn-arrow:hover {
        background-color: #555;
        color: white;
    }

</style>
@endpush


@extends('front.layouts.app')


@section('content')
<div class="container py-4">
    <div class="hero position-relative rounded-4 overflow-hidden mb-4 mb-md-5">
        <div class="hero-pattern position-absolute top-0 bottom-0 start-0 end-0 z-0"></div>
        <div class="hero-overlay position-absolute top-0 bottom-0 start-0 end-0 z-1"></div>

        <div class="position-relative z-2 p-4 p-md-5 w-100">
            <span class="hero-tag badge rounded-pill text-uppercase fw-semibold mb-2 px-3 py-2">Program Unggulan ·
                Pendidikan</span>
            <h1 class="fs-3 display-6-md fw-bold text-white mb-2">Sekolah Moderasi</h1>
        </div>
    </div>

    <div class="row g-4 g-lg-5">
        <div class="col-lg-8">

            <section class="mb-5">
                <p class="text-maroon-light fw-bold text-uppercase mb-1"
                    style="font-size: 12px; letter-spacing: 0.1em;">Tentang Program</p>
                <h2 class="h4 fw-bold text-dark mb-3">Apa itu Sekolah Moderasi?</h2>
                <p class="text-secondary" style="line-height: 1.8;">Sekolah Moderasi adalah program pendidikan dan
                    kajian intensif yang dirancang khusus bagi mahasiswa untuk memahami, menghayati, dan
                    mengimplementasikan nilai-nilai toleransi, inklusivitas, dan harmoni antarumat beragama dalam
                    kehidupan sehari-hari.</p>
                <p class="text-secondary" style="line-height: 1.8;">Melalui metode diskusi interaktif, studi kasus
                    nyata, dan dialog lintas iman, peserta diajak untuk membuka wawasan, mempererat relasi antar
                    komunitas, serta menjadi agen perubahan yang aktif dalam menjaga kerukunan bangsa di era
                    kontemporer.</p>
            </section>

            <hr class="text-black-50 my-4">

            <section class="mb-5">
                <p class="text-maroon-light fw-bold text-uppercase mb-1"
                    style="font-size: 12px; letter-spacing: 0.1em;">Tujuan Program</p>
                <h2 class="h4 fw-bold text-dark mb-4">Yang Akan Kamu Capai</h2>

                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-white border shadow-sm">
                        <div
                            class="tujuan-num rounded-circle bg-maroon-main text-white fw-bold d-flex align-items-center justify-content-center flex-shrink-0">
                            1</div>
                        <div class="text-dark" style="font-size: 14.5px; line-height: 1.6;">Memahami konsep moderasi
                            beragama secara mendalam melalui kajian akademis dan perspektif lintas tradisi keagamaan.
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-white border shadow-sm">
                        <div
                            class="tujuan-num rounded-circle bg-maroon-main text-white fw-bold d-flex align-items-center justify-content-center flex-shrink-0">
                            2</div>
                        <div class="text-dark" style="font-size: 14.5px; line-height: 1.6;">Mengembangkan kemampuan
                            berdialog secara konstruktif dengan individu dari latar belakang agama, budaya, dan
                            pandangan yang berbeda.</div>
                    </div>
                    <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-white border shadow-sm">
                        <div
                            class="tujuan-num rounded-circle bg-maroon-main text-white fw-bold d-flex align-items-center justify-content-center flex-shrink-0">
                            3</div>
                        <div class="text-dark" style="font-size: 14.5px; line-height: 1.6;">Melatih kepekaan terhadap
                            isu intoleransi, radikalisme, dan ujaran kebencian agar mampu merespons secara bijak di
                            ruang publik maupun digital.</div>
                    </div>
                    <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-white border shadow-sm">
                        <div
                            class="tujuan-num rounded-circle bg-maroon-main text-white fw-bold d-flex align-items-center justify-content-center flex-shrink-0">
                            4</div>
                        <div class="text-dark" style="font-size: 14.5px; line-height: 1.6;">Menjadi agen moderasi yang
                            mampu menginisiasi gerakan kerukunan di lingkungan kampus dan masyarakat sekitar.</div>
                    </div>
                </div>
            </section>

            <hr class="text-black-50 my-4">

            <section class="mb-5">
                <p class="text-maroon-light fw-bold text-uppercase mb-1"
                    style="font-size: 12px; letter-spacing: 0.1em;">Galeri</p>
                <h2 class="h4 fw-bold text-dark mb-4">Dokumentasi Kegiatan</h2>

                <div class="galeri-grid">
                    <div
                        class="galeri-item galeri-main rounded-3 bg-light border position-relative d-flex flex-column align-items-center justify-content-center overflow-hidden">
                        <i class="ti ti-photo fs-1 text-maroon-light opacity-50 mb-2"></i>
                        <span class="text-secondary small">Foto Utama</span>
                        <div
                            class="galeri-overlay position-absolute top-0 bottom-0 start-0 end-0 d-flex align-items-center justify-content-center z-2">
                            <i class="ti ti-zoom-in text-white fs-2"></i>
                        </div>
                    </div>
                    <div
                        class="galeri-item rounded-3 bg-light border position-relative d-flex flex-column align-items-center justify-content-center overflow-hidden">
                        <i class="ti ti-photo fs-3 text-maroon-light opacity-50 mb-1"></i>
                        <span class="text-secondary" style="font-size: 11px;">Sesi Diskusi</span>
                        <div
                            class="galeri-overlay position-absolute top-0 bottom-0 start-0 end-0 d-flex align-items-center justify-content-center z-2">
                            <i class="ti ti-zoom-in text-white fs-4"></i>
                        </div>
                    </div>
                    <div
                        class="galeri-item rounded-3 bg-light border position-relative d-flex flex-column align-items-center justify-content-center overflow-hidden">
                        <i class="ti ti-photo fs-3 text-maroon-light opacity-50 mb-1"></i>
                        <span class="text-secondary" style="font-size: 11px;">Dialog</span>
                        <div
                            class="galeri-overlay position-absolute top-0 bottom-0 start-0 end-0 d-flex align-items-center justify-content-center z-2">
                            <i class="ti ti-zoom-in text-white fs-4"></i>
                        </div>
                    </div>
                    <div
                        class="galeri-item rounded-3 bg-light border position-relative d-flex flex-column align-items-center justify-content-center overflow-hidden">
                        <i class="ti ti-photo fs-3 text-maroon-light opacity-50 mb-1"></i>
                        <span class="text-secondary" style="font-size: 11px;">Kasus</span>
                        <div
                            class="galeri-overlay position-absolute top-0 bottom-0 start-0 end-0 d-flex align-items-center justify-content-center z-2">
                            <i class="ti ti-zoom-in text-white fs-4"></i>
                        </div>
                    </div>
                    <div
                        class="galeri-item rounded-3 bg-light border position-relative d-flex flex-column align-items-center justify-content-center overflow-hidden">
                        <i class="ti ti-photo fs-3 text-maroon-light opacity-50 mb-1"></i>
                        <span class="text-secondary" style="font-size: 11px;">Penutupan</span>
                        <div
                            class="galeri-overlay position-absolute top-0 bottom-0 start-0 end-0 d-flex align-items-center justify-content-center z-2">
                            <i class="ti ti-zoom-in text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </section>

        </div>

        <div class="col-lg-4">
            <div class="sticky-top" style="top: 2rem;">
                <h3 class="h6 fw-bold text-dark border-bottom pb-2 mb-3">Program Lainnya</h3>

                <div class="d-flex flex-column gap-2">

                    <div class="prog-card d-flex align-items-start gap-3 p-3 rounded-3 border bg-white"
                        onclick="alert('Tampilkan detail program Kaderisasi')">
                        <div class="prog-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=300&q=80"
                                alt="Banner Kaderisasi" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div class="flex-grow-1">
                            <div class="prog-name fw-semibold text-dark mb-1" style="font-size: 14px;">Kaderisasi</div>
                            <div class="text-secondary mb-2" style="font-size: 12px; line-height: 1.4;">Pembinaan
                                karakter kebangsaan dan bela negara bagi mahasiswa.</div>
                            <span
                                class="prog-badge badge bg-maroon-subtle-custom text-maroon-main rounded-pill fw-medium"
                                style="font-size: 10px;">Kaderisasi</span>
                        </div>
                    </div>

                    <div class="prog-card d-flex align-items-start gap-3 p-3 rounded-3 border bg-white"
                        onclick="alert('Tampilkan detail program Digital Campaign')">
                        <div class="prog-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1432888498266-38ffec3eaf0a?w=300&q=80"
                                alt="Banner Digital Campaign" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div class="flex-grow-1">
                            <div class="prog-name fw-semibold text-dark mb-1" style="font-size: 14px;">Digital Campaign
                            </div>
                            <div class="text-secondary mb-2" style="font-size: 12px; line-height: 1.4;">Riset,
                                penulisan, dan publikasi gagasan kebangsaan secara digital.</div>
                            <span
                                class="prog-badge badge bg-maroon-subtle-custom text-maroon-main rounded-pill fw-medium"
                                style="font-size: 10px;">Publikasi</span>
                        </div>
                    </div>

                    <div class="prog-card d-flex align-items-start gap-3 p-3 rounded-3 border bg-white"
                        onclick="alert('Tampilkan detail program Forum Diskusi Lintas Iman')">
                        <div class="prog-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?w=300&q=80"
                                alt="Banner Forum Diskusi" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div class="flex-grow-1">
                            <div class="prog-name fw-semibold text-dark mb-1" style="font-size: 14px;">Forum Diskusi
                                Lintas Iman</div>
                            <div class="text-secondary mb-2" style="font-size: 12px; line-height: 1.4;">Ruang terbuka
                                antar mahasiswa berbagai latar belakang.</div>
                            <span
                                class="prog-badge badge bg-maroon-subtle-custom text-maroon-main rounded-pill fw-medium"
                                style="font-size: 10px;">Sosial</span>
                        </div>
                    </div>

                    <div class="prog-card d-flex align-items-start gap-3 p-3 rounded-3 border bg-white"
                        onclick="alert('Tampilkan detail program Aksi Sosial Kebangsaan')">
                        <div class="prog-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1593113589914-07553f1db85c?w=300&q=80"
                                alt="Banner Aksi Sosial" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div class="flex-grow-1">
                            <div class="prog-name fw-semibold text-dark mb-1" style="font-size: 14px;">Aksi Sosial
                                Kebangsaan</div>
                            <div class="text-secondary mb-2" style="font-size: 12px; line-height: 1.4;">Pengabdian
                                masyarakat sebagai wujud nyata bela negara.</div>
                            <span
                                class="prog-badge badge bg-maroon-subtle-custom text-maroon-main rounded-pill fw-medium"
                                style="font-size: 10px;">Sosial</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
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
        min-heightht: 400px;
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
    }

    .galeri-main {
        grid-column: span 2;
        grid-row: span 2;
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

@extends('front.layouts.app', ['bodyClass' => 'page-tentang'])

@section('title', 'Profil Organisasi')

@section('content')
    <div class="container" data-aos="fade-in" data-aos-duration="1000">
        <header class="tentang-hero-section">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Tentang</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profil</li>
                    </ol>
                </nav>
                <h1 class="display-4 fw-bold mb-3">Profil Organisasi</h1>
                <p class="lead" style="font-size: 1.1rem; max-width: 500px;">Organisasi pergerakan mahasiswa yang
                    berkomitmen menumbuhkan moderasi beragama dan memperkuat semangat bela negara di tengah keberagaman
                    Indonesia.</p>
            </div>
        </header>
    </div>

    <section class="container my-5 py-5">
        <div class="row align-items-center">
            <div class="col-lg-5 pe-lg-5" data-aos="fade-right" data-aos-duration="1000">
                <h2 class="fw-bold mb-4">Sejarah Singkat<br>PMMBN</h2>
                <p class="text-muted mb-4">PMMBN berawal dari sebuah gerakan Duta Moderasi Beragama yang lahir di Surabaya.
                    Inisiatif ini hadir sebagai ruang bagi generasi muda untuk menanamkan nilai moderasi, toleransi, dan
                    kebangsaan di tengah keberagaman Indonesia.
                    PMMBN berawal dari sebuah gerakan Duta Moderasi Beragama yang lahir di Surabaya. Inisiatif ini hadir
                    sebagai ruang bagi generasi muda untuk menanamkan nilai moderasi, toleransi, dan kebangsaan di tengah
                    keberagaman Indonesia.
                </p>

                <!-- <div class="history-card shadow-sm mt-5">
                    <h5 class="fw-bold">Duta Moderasi Beragama</h5>
                    <p class="text-muted small">merupakan peran pemuda sebagai jembatan harmoni, yang menumbuhkan sikap toleran, adil, dan seimbang dalam kehidupan beragama dan berbangsa.</p>
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-outline-secondary rounded-circle p-2" style="width:40px; height:40px;"><i class="fa-solid fa-arrow-left"></i></button>
                        <button class="btn btn-outline-secondary rounded-circle p-2" style="width:40px; height:40px;"><i class="fa-solid fa-arrow-right"></i></button>
                    </div>
                </div> -->
            </div>

            <div class="col-lg-7 mt-5 mt-lg-0 history-images" data-aos="fade-left" data-aos-duration="1000"
                data-aos-delay="200">
                <div class="row g-3">
                    <div class="col-12">
                        <img src="https://images.unsplash.com/photo-1544531586-fde5298cdd40?q=80&w=2070&auto=format&fit=crop"
                            class="img-main" alt="Sejarah Utama">
                    </div>
                    <div class="col-4">
                        <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=2084&auto=format&fit=crop"
                            class="img-thumb" alt="Thumb 1">
                    </div>
                    <div class="col-4">
                        <img src="https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?q=80&w=2070&auto=format&fit=crop"
                            class="img-thumb" alt="Thumb 2">
                    </div>
                    <div class="col-4">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=2070&auto=format&fit=crop"
                            class="img-thumb" alt="Thumb 3">
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5" data-aos="fade-up" data-aos-offset="50">
            <div class="col-12">
                <div class="timeline">
                    <div class="timeline-point">2021</div>
                    <div class="timeline-point">2022</div>
                    <div class="timeline-point">2023</div>
                    <div class="timeline-point">2024</div>
                    <div class="timeline-point">2025</div>
                </div>
            </div>
        </div>
    </section>

    <section class="container my-5 py-5">
        <div class="row align-items-center">
            <div class="col-lg-5" data-aos="zoom-in-right" data-aos-duration="1000">
                <img src="https://images.unsplash.com/photo-1556157382-97eda2d62296?q=80&w=2070&auto=format&fit=crop"
                    class="visi-misi-img shadow" alt="Ketua/Tokoh PMMBN">
            </div>

            <div class="col-lg-7 ps-lg-5 mt-5 mt-lg-0" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                <h2 class="fw-bold mb-3">Visi</h2>
                <p class="text-muted mb-5">Menjadi organisasi pemuda yang berperan aktif dalam menguatkan moderasi beragama,
                    persatuan bangsa, dan semangat bela negara di Indonesia.</p>

                <h2 class="fw-bold mb-3">Misi</h2>
                <ol class="text-muted" style="line-height: 1.8;">
                    <li>Menanamkan nilai moderasi beragama sebagai landasan berpikir dan bersikap generasi muda.</li>
                    <li>Mendorong peran aktif pemuda dalam menjaga toleransi, harmoni, dan keutuhan bangsa.</li>
                    <li>Mengembangkan kader pemuda yang berintegritas, berwawasan kebangsaan, dan berjiwa kepemimpinan.</li>
                    <li>Menjadi wadah kolaborasi dan edukasi lintas latar belakang dalam isu keagamaan, sosial, dan
                        kebangsaan.</li>
                    <li>Menginisiasi program dan gerakan yang berdampak bagi masyarakat, berlandaskan nilai persatuan dan
                        bela negara.</li>
                </ol>
            </div>
        </div>
    </section>

@endsection

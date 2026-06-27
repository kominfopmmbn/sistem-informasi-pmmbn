<!-- HERO SECTION -->
<section class="hero-section position-relative overflow-hidden">
    @if (! empty($heroSlides))
        <div id="heroCarousel"
            class="carousel slide carousel-fade hero-carousel"
            data-bs-ride="carousel"
            data-bs-interval="6000"
            data-bs-pause="hover"
            aria-label="Banner utama beranda">
            <div class="carousel-inner h-100">
                @foreach ($heroSlides as $slideSrc)
                    <div class="carousel-item h-100 {{ $loop->first ? 'active' : '' }}">
                        <div class="hero-slide-bg" style="background-image: url('{{ $slideSrc }}');"
                            aria-hidden="true"></div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="hero-section-inner container position-relative">
        <div class="row">
            <div class="col-lg-8" data-aos="fade-up" data-aos-duration="1000">
                <h1 class="display-4 fw-bold mb-3">Pemuda Berdaya. Indonesia Digdaya</h1>
                <p class="lead mb-4">Mari bergabung bersama kami dalam gerakan membangun bangsa melalui kolaborasi,
                    inovasi, dan aksi nyata untuk Indonesia yang lebih baik.</p>
                <a href="{{ route('about.profil-organisasi') }}" class="btn btn-outline-light rounded-pill px-4 py-2"
                    style="border: 2px solid white;">Pelajari Lebih Lanjut <i
                        class="bi bi-arrow-right ms-2"></i></a>
            </div>
        </div>
    </div>
</section>

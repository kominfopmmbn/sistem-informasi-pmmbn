<!-- BERITA -->
<section class="py-5 bg-light news-section">
    <div class="container">
        <div
            class="news-heading d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4 mb-4">
            <div class="news-heading-left">
                <p class="text-brand fw-bold mb-1">Berita dan Opini</p>
                <h2 class="fw-bold mb-2">Berita Terbaru</h2>
                <p class="text-muted mb-0 news-heading-subtitle">
                    Berita ini memuat informasi terkini seputar kegiatan, kajian, aksi sosial,
                    serta dinamika pergerakan mahasiswa di berbagai wilayah.
                </p>
            </div>
            <a href="{{ route('article.index', ['categorySlug' => 'berita']) }}"
                class="btn btn-outline-brand d-none d-md-inline-flex align-items-center news-heading-cta">
                Lihat Semua
                <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <ul class="nav nav-pills news-filter mb-4" role="tablist" aria-label="Filter berita dan opini">
            <li class="nav-item">
                <a id="tab-home-berita" class="nav-link active rounded-pill px-4" href="#" data-news-tab="berita"
                    role="button" aria-pressed="true">Berita</a>
            </li>
            <li class="nav-item ms-2">
                <a id="tab-home-opini" class="nav-link rounded-pill px-4" href="#" data-news-tab="opini"
                    role="button" aria-pressed="false">Opini</a>
            </li>
        </ul>

        <div id="newsCardGrids">
            <div class="row g-4 mb-5" id="newsCardGridBerita" role="tabpanel" aria-labelledby="tab-home-berita">
                @foreach ($news as $article)
                    @include('front.components.article-card', ['article' => $article])
                @endforeach
            </div>
            <div class="row g-4 mb-5 d-none" id="newsCardGridOpini" role="tabpanel" aria-labelledby="tab-home-opini">
                @foreach ($opinions as $article)
                    @include('front.components.article-card', ['article' => $article])
                @endforeach
            </div>
        </div>
        <div class="text-center">
            <button type="button" class="btn btn-brand px-5"
                onclick="window.location.href='{{ route('article.index', ['categorySlug' => 'berita']) }}'">Selengkapnya
                <i class="bi bi-arrow-right ms-1"></i></button>
        </div>
    </div>
</section>

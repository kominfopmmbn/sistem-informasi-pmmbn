@extends('front.layouts.app', ['bodyClass' => 'page-news'])

@section('title', 'Berita')

@section('content')
    <div class="container">
        <div class="news-hero-section" data-aos="fade-down" data-aos-duration="800">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div class="breadcrumb-custom" data-aos="fade-right" data-aos-delay="200" data-aos-duration="600">
                    Artikel <i class="fa-solid fa-chevron-right fa-xs mx-2"></i>
                    <span id="heroCrumbLabel">{{ $selectedCategory->title }}</span>
                </div>
                <h1 class="display-5 fw-bold mb-3" id="heroTitle" data-aos="fade-right" data-aos-delay="400" data-aos-duration="600">
                    {{ $selectedCategory->title }}
                </h1>
                <p class="lead" id="heroLead" style="max-width: 600px; font-size: 1.05rem;" data-aos="fade-right" data-aos-delay="600" data-aos-duration="600">
                    {{ $selectedCategory->description }}
                </p>
            </div>
        </div>
    </div>

    <div class="subnav-wrapper mt-4" data-aos="fade-up" data-aos-duration="600" data-aos-delay="100">
        <div class="container" aria-label="Kategori artikel">
            @foreach ($categories as $category)
                <a href="{{ route('article.index', $category->slug) }}"
                   class="subnav-link {{ $selectedCategory->slug === $category->slug ? 'active' : '' }}"
                   data-news-tab="{{ $category->slug }}"
                   id="tab-{{ $category->slug }}">
                    {{ $category->title }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="container my-5">

        <div class="row justify-content-center mb-5" data-aos="fade-up" data-aos-duration="600" data-aos-delay="200">
            <div class="col-md-8 d-flex gap-3 justify-content-center flex-wrap">
                <div class="search-wrapper" style="width: 250px;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" class="form-control filter-control" placeholder="Search">
                </div>
                <select class="form-select filter-control" style="width: 150px;">
                    <option selected>Bulan</option>
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                </select>
                <select class="form-select filter-control" style="width: 150px;">
                    <option selected>Tahun</option>
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                </select>
            </div>
        </div>

        <div id="articleCardsRegion" role="tabpanel" aria-labelledby="tab-berita">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="articleCardGridBerita">
                @foreach ($articles as $article)
                    <div data-aos="fade-up" data-aos-duration="600" data-aos-delay="{{ $loop->index * 100 }}">
                        @include('front.components.article-card', ['article' => $article, 'columnClass' => 'col'])
                    </div>
                @endforeach
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-5 pt-3"
             data-aos="fade-up" data-aos-duration="600">
            <div class="text-muted mb-3 mb-md-0" id="articleResultsSummary" style="font-size: 0.9rem;">
                @if ($articles->isEmpty())
                    Tidak ada hasil
                @else
                    Menampilkan {{ $articles->firstItem() }} – {{ $articles->lastItem() }} dari {{ $articles->total() }} hasil
                @endif
            </div>
            @if ($articles->hasPages())
                {{ $articles->onEachSide(1)->links('vendor.pagination.front-custom') }}
            @endif
        </div>

    </div>
@endsection

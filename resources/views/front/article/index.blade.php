@extends('front.layouts.app', ['bodyClass' => 'page-news'])

@section('title', 'Berita')

@section('content')
    <div class="container">
        <div class="news-hero-section" data-aos="fade-down" data-aos-duration="800">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div class="breadcrumb-custom" data-aos="fade-right" data-aos-delay="200" data-aos-duration="600">
                    Artikel <i class="bi bi-chevron-right mx-2 small"></i>
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
            <form method="GET" action="{{ route('article.index', $selectedCategory->slug) }}"
                  class="col-md-8 news-filter d-flex gap-3 justify-content-center flex-wrap align-items-center">
                <div class="search-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control filter-control" placeholder="Cari berita…">
                </div>
                @php($months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'])
                <select name="month" class="form-select filter-control" onchange="this.form.submit()">
                    <option value="">Bulan</option>
                    @foreach ($months as $i => $name)
                        <option value="{{ $i + 1 }}" @selected(request('month') == $i + 1)>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="year" class="form-select filter-control" onchange="this.form.submit()">
                    <option value="">Tahun</option>
                    @foreach ($years as $y)
                        <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                    @endforeach
                </select>
                @if (request()->hasAny(['q', 'month', 'year']))
                    <a href="{{ route('article.index', $selectedCategory->slug) }}" class="filter-reset">
                        <i class="bi bi-x-lg"></i>Reset
                    </a>
                @endif
            </form>
        </div>

        <div id="articleCardsRegion" role="tabpanel" aria-labelledby="tab-berita">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="articleCardGridBerita">
                @foreach ($articles as $article)
                    <div class="h-100" data-aos="fade-up" data-aos-duration="600" data-aos-delay="{{ $loop->index * 100 }}">
                        @include('front.components.article-card', ['article' => $article, 'columnClass' => 'col h-100'])
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

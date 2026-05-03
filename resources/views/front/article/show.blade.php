@extends('front.layouts.app', ['bodyClass' => 'page-news-detail'])

@section('title', $article->title)

@section('content')
    <main class="container my-5">
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <img src="{{ $article->getFirstMediaUrl(\App\Models\Article::COVER_COLLECTION) }}"
                    alt="Hero Banner" class="hero-img shadow-sm">
            </div>
        </div>

        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h1 class="article-title">
                    {{ $article->title }}
                </h1>
                <p class="article-date">{{ $article->published_at->format('d F Y') }}</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-md-2 col-lg-1 mb-4">
                        <div class="share-sidebar">
                            <span class="fw-bold d-block mb-3" style="font-size: 0.9rem;">Share</span>
                            <div class="share-icons">
                                <a href="#"><i class="bi bi-instagram"></i></a>
                                <a href="#"><i class="bi bi-twitter-x"></i></a>
                                <a href="#"><i class="bi bi-facebook"></i></a>
                                <a href="#"><i class="bi bi-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7 col-lg-8 article-content pe-lg-5">
                        {!! $article->content !!}
                    </div>

                    <div class="col-md-3 col-lg-3 mt-5 mt-md-0">
                        <span class="sidebar-title">{{ $article->category->title }} Lainnya</span>
                        @foreach ($relatedArticles as $relatedArticle)
                            <a href="{{ route('article.show', $relatedArticle->slug) }}" class="sidebar-item">
                                <img src="{{ $relatedArticle->getFirstMediaUrl(\App\Models\Article::COVER_COLLECTION) }}" alt="{{ $relatedArticle->title }}">
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

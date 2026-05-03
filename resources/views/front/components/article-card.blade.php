@props([
    'article',
    'columnClass' => 'col-12 col-sm-6 col-lg-3',
    'titleLimit' => 35,
    'excerptLimit' => 120,
])

@php
    use App\Models\Article;
    use Illuminate\Support\Str;

    $url = route('article.show', $article->slug);
    $coverUrl = $article->getFirstMediaUrl(Article::COVER_COLLECTION) ?: null;
    $img = $coverUrl ?: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&w=700&q=80';
    $dateLabel = $article->published_at
        ? $article->published_at->locale(app()->getLocale())->translatedFormat('d F Y')
        : '—';
    $titleDisplay = Str::limit((string) $article->title, (int) $titleLimit, '…');
    $excerptDisplay = filled($article->subtitle)
        ? Str::limit((string) $article->subtitle, (int) $excerptLimit, '…')
        : '-';
@endphp

<div @class([$columnClass])>
    <a href="{{ $url }}" class="news-card text-decoration-none text-reset d-block h-100">
        <div class="news-img-wrapper">
            <img src="{{ $img }}" alt="{{ $article->title }}">
        </div>
        <div class="news-meta">
            <p class="news-date mb-0">{{ $dateLabel }}</p>
            <span class="news-views"><i class="bi bi-eye"></i> {{ $article->views_count }}</span>
        </div>
        <h5>{{ $titleDisplay }}</h5>
        <p class="news-excerpt">{{ $excerptDisplay }}</p>
    </a>
</div>

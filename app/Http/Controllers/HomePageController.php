<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Support\Collection;

class HomePageController extends Controller
{
    public function index()
    {
        $newsMedia = fn ($mq) => $mq->where('collection_name', Article::COVER_COLLECTION);

        $news = $this->mapArticlesForNewsJson(
            Article::published()
                ->with(['media' => $newsMedia])
                ->berita()
                ->orderBy('published_at', 'desc')
                ->take(4)
                ->get()
        );

        $opinions = $this->mapArticlesForNewsJson(
            Article::published()
                ->with(['media' => $newsMedia])
                ->opini()
                ->orderBy('published_at', 'desc')
                ->take(4)
                ->get()
        );

        return view('front.home.index', compact('news', 'opinions'));
    }

    /**
     * @param  Collection<int, Article>  $articles
     * @return Collection<int, array<string, mixed>>
     */
    private function mapArticlesForNewsJson(Collection $articles): Collection
    {
        return $articles->map(fn (Article $article) => [
            'title' => $article->title,
            'subtitle' => $article->subtitle,
            'published_at' => $article->published_at,
            'views_count' => $article->views_count,
            // URL lengkap — key sama dengan field lama agar kartu beranda JS tetap konsisten.
            'cover_photo_path' => $article->getFirstMediaUrl(Article::COVER_COLLECTION) ?: null,
        ]);
    }
}

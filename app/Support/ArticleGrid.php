<?php

namespace App\Support;

use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;

/**
 * Query artikel terpublish untuk grid front (beranda & indeks artikel).
 */
class ArticleGrid
{
    public static function coverMediaConstraint(): callable
    {
        return fn ($mq) => $mq->where('collection_name', Article::COVER_COLLECTION);
    }

    /**
     * @return Collection<int, Article>
     */
    public static function latestBerita(int $limit): Collection
    {
        return Article::published()
            ->with(['media' => self::coverMediaConstraint()])
            ->berita()
            ->orderBy('published_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * @return Collection<int, Article>
     */
    public static function latestOpini(int $limit): Collection
    {
        return Article::published()
            ->with(['media' => self::coverMediaConstraint()])
            ->opini()
            ->orderBy('published_at', 'desc')
            ->take($limit)
            ->get();
    }
}

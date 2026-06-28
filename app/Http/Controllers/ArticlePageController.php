<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Support\ArticleGrid;
use Illuminate\Http\Request;

class ArticlePageController extends Controller
{
    private const int PER_TAB = 8;

    public function index(Request $request, string $categorySlug)
    {
        $selectedCategory = Category::query()->where('slug', $categorySlug)->firstOrFail();

        $articles = Article::published()
            ->where('category_id', $selectedCategory->id)
            ->with(['media' => ArticleGrid::coverMediaConstraint()]);

        if (($q = trim((string) $request->input('q'))) !== '') {
            $articles->where('title', 'like', '%'.addcslashes($q, '%_\\').'%');
        }

        $month = $request->input('month');
        if (is_numeric($month) && (int) $month >= 1 && (int) $month <= 12) {
            $articles->whereMonth('published_at', (int) $month);
        }

        $year = $request->input('year');
        if (is_numeric($year)) {
            $articles->whereYear('published_at', (int) $year);
        }

        $articles = $articles
            ->orderBy('published_at', 'desc')
            ->paginate($request->input('per_page', self::PER_TAB))
            ->withQueryString();

        $categories = Category::query()->orderBy('title', 'asc')->get();

        // Rentang tahun penuh dari published_at paling awal s/d paling akhir (terbaru → terlama).
        $bounds = Article::published()
            ->where('category_id', $selectedCategory->id)
            ->selectRaw('MIN(published_at) as first_at, MAX(published_at) as last_at')
            ->first();
        $years = $bounds->first_at
            ? range((int) substr($bounds->last_at, 0, 4), (int) substr($bounds->first_at, 0, 4))
            : [];

        return view('front.article.index', compact('selectedCategory', 'articles', 'categories', 'years'));
    }

    public function show(string $slug)
    {
        $article = Article::published()
            ->where('slug', $slug)
            ->with([
                'media' => ArticleGrid::coverMediaConstraint(),
                'category',
            ])
            ->firstOrFail();

        // Increment penghitung view secara atomik via base query agar event Eloquent /
        // Userstamps tidak terpicu (jangan ubah updated_at / updated_by saat dilihat publik).
        Article::whereKey($article->getKey())->toBase()->increment('views_count');

        $relatedArticles = Article::published()
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->with(['media' => ArticleGrid::coverMediaConstraint()])
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        return view('front.article.show', compact('article', 'relatedArticles'));
    }
}

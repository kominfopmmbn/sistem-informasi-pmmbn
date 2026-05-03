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
            ->with(['media' => ArticleGrid::coverMediaConstraint()])
            ->orderBy('published_at', 'desc')
            ->paginate($request->input('per_page', self::PER_TAB))
            ->withQueryString();

        $categories = Category::query()->orderBy('title', 'asc')->get();

        return view('front.article.index', compact('selectedCategory', 'articles', 'categories'));
    }

    public function show(string $slug)
    {
        $article = Article::published()->where('slug', $slug)->firstOrFail();

        return view('front.article.show', compact('article'));
    }
}

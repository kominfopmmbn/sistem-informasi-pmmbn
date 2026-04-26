<?php

namespace App\Http\Controllers;

use App\Models\Article;

class HomePageController extends Controller
{
    public function index()
    {
        $news = Article::published()
            ->select([
                'title',
                'subtitle',
                'cover_photo_path',
                'published_at',
                'views_count',
            ])
            ->berita()
            ->orderBy('published_at', 'desc')
            ->take(4)
            ->get();
        $opinions = Article::published()
            ->select([
                'title',
                'subtitle',
                'cover_photo_path',
                'published_at',
                'views_count',
            ])
            ->opini()
            ->orderBy('published_at', 'desc')
            ->take(4)
            ->get();
        return view('front.home.index', compact('news', 'opinions'));
    }
}

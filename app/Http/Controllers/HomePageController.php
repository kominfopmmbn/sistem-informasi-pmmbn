<?php

namespace App\Http\Controllers;

use App\Support\ArticleGrid;

class HomePageController extends Controller
{
    private const int HOME_TAB_LIMIT = 4;

    public function index()
    {
        $news = ArticleGrid::latestBerita(self::HOME_TAB_LIMIT);
        $opinions = ArticleGrid::latestOpini(self::HOME_TAB_LIMIT);

        $heroSlides = [
            asset('assets-front-pages/img/bg-hero-home.JPG'),
            asset('assets-front-pages/img/download-hero.png'),
            asset('assets-front-pages/img/kta-hero.png'),
            asset('assets-front-pages/img/ketum.png'),
        ];

        return view('front.home.index', compact('news', 'opinions', 'heroSlides'));
    }
}

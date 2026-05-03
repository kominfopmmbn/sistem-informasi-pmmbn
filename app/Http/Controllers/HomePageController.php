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

        return view('front.home.index', compact('news', 'opinions'));
    }
}

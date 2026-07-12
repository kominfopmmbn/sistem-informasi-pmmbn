<?php

namespace App\Http\Controllers;

use App\Enums\MemberActivationStatus;
use App\Models\Article;
use App\Models\College;
use App\Models\Member;
use App\Models\MemberActivation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $memberCount = Member::count();
        $collegeCount = College::count();

        $publishedArticleQuery = Article::published();
        if (! $request->user()->can('articles.other')) {
            $publishedArticleQuery->where('created_by', $request->user()->id);
        }
        $publishedArticleCount = $publishedArticleQuery->count();

        $pendingQuery = MemberActivation::query()
            ->currentStatusIs(MemberActivationStatus::PENDING);

        $pendingActivationCount = (clone $pendingQuery)->count();

        $pendingActivations = $pendingQuery
            ->with(['college', 'currentStatus'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'memberCount',
            'collegeCount',
            'publishedArticleCount',
            'pendingActivationCount',
            'pendingActivations',
        ));
    }
}

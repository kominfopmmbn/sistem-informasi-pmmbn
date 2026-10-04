<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Banner;
use App\Policies\ArticlePolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Gate::policy(Article::class, ArticlePolicy::class);

        // Peran Administrator melewati semua pengecekan permission (middleware Spatie & @can).
        Gate::before(function ($user, string $ability) {
            if ($user === null) {
                return null;
            }

            return $user->hasRole('Administrator') ? true : null;
        });

        // Popup banner di semua halaman publik; `?banner=<slug>` menjadikan banner itu slide pertama
        // dan memaksa popup tampil walau sudah ditutup di sesi ini.
        View::composer('front.layouts.partials.banner-popup', function ($view): void {
            $banners = Banner::query()->active()->with('media')->get();

            $slug = request()->query('banner');
            $forced = is_string($slug) ? $banners->firstWhere('slug', $slug) : null;

            if ($forced !== null) {
                $banners = $banners->reject(fn (Banner $banner) => $banner->is($forced))
                    ->prepend($forced)
                    ->values();
            }

            $view->with([
                'banners' => $banners,
                'bannerForced' => $forced !== null,
            ]);
        });
    }
}

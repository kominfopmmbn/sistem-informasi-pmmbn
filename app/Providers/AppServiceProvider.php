<?php

namespace App\Providers;

use App\Models\Article;
use App\Policies\ArticlePolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Di Vercel filesystem read-only kecuali /tmp. Arahkan storage path ke sana
        // agar compiled views, cache framework, dan berkas temporer MediaLibrary bisa ditulis.
        // (Vercel otomatis men-set env VERCEL pada runtime serverless.)
        if (env('VERCEL')) {
            $this->app->useStoragePath('/tmp/storage');
        }
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
    }
}

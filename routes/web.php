<?php

use App\Http\Controllers\ArticlePageController;
use App\Http\Controllers\DownloadPageController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\MemberActivationPageController;
use Illuminate\Support\Facades\Route;

Route::prefix('select')
    ->name('select.')
    ->middleware('throttle:120,1')
    ->group(function (): void {
        Route::get('cities', [LookupController::class, 'cities'])->name('cities');
        Route::get('districts', [LookupController::class, 'districts'])->name('districts');
        Route::get('villages', [LookupController::class, 'villages'])->name('villages');
    });

Route::get('/', [HomePageController::class, 'index'])->name('home.index');

Route::prefix('about')
    ->name('about.')
    ->group(function (): void {
        Route::get('/profil-organisasi', function () {
            return view('front.about.profil-organisasi');
        })->name('profil-organisasi');
        Route::get('/member-activation', [MemberActivationPageController::class, 'index'])
            ->name('member-activation');
        Route::post('/member-activation', [MemberActivationPageController::class, 'store'])
            ->name('member-activation.store');
    });

Route::get('/download', [DownloadPageController::class, 'index'])->name('download.index');

Route::get('/article', [ArticlePageController::class, 'index'])->name('article.index');

Route::prefix('article')
    ->name('article.')
    ->group(function (): void {
        Route::get('/detail/{slug}', [ArticlePageController::class, 'show'])->name('show');
        Route::get('/{categorySlug}', [ArticlePageController::class, 'index'])->name('index');
        Route::get('/', [ArticlePageController::class, 'index'])->name('all');
    });

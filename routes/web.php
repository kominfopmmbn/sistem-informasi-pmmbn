<?php

use App\Http\Controllers\HomePageController;
use App\Http\Controllers\LookupController;
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
Route::get('/about/profil-organisasi', function() {
    return view('front.about.profil-organisasi');
})->name('about.profil-organisasi');

Route::get('/about/member-activation', function() {
    return view('front.about.member-activation');
})->name('about.member-activation');

Route::get('/download', function() {
    return view('front.download.index');
})->name('download.index');

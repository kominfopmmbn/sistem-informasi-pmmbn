<?php

use App\Http\Controllers\HomePageController;
use App\Http\Controllers\IndonesiaLookupController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomePageController::class, 'index'])->name('home.index');

Route::prefix('indonesia')
    ->name('indonesia.')
    ->middleware('throttle:120,1')
    ->group(function (): void {
        Route::prefix('select')
            ->name('select.')
            ->group(function (): void {
                Route::get('cities', [IndonesiaLookupController::class, 'cities'])->name('cities');
            });
    });

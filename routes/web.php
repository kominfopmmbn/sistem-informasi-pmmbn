<?php

use App\Http\Controllers\HomePageController;
use App\Http\Controllers\LookupController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomePageController::class, 'index'])->name('home.index');

Route::prefix('select')
    ->name('select.')
    ->middleware('throttle:120,1')
    ->group(function (): void {
        Route::get('cities', [LookupController::class, 'cities'])->name('cities');
        Route::get('districts', [LookupController::class, 'districts'])->name('districts');
        Route::get('villages', [LookupController::class, 'villages'])->name('villages');
    });

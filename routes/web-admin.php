<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.layouts.app');
});

Route::group([
    'middleware' => 'auth',
    'prefix' => 'dashboard',
    'as' => 'dashboard.',
], function (): void {
    Route::get('/', function () {
        return view('admin.dashboard.index');
    })->name('index');
});

Route::group([
    'as' => 'auth.',
], function (): void {
    Route::get('/login', [AuthController::class, 'login'])
        ->name('login')
        ->middleware('guest');
    Route::post('/login', [AuthController::class, 'loginPost'])
        ->name('loginPost')
        ->middleware('guest');
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout')
        ->middleware('auth');
});


Route::middleware('auth')->prefix('users')->name('users.')->group(function (): void {
    Route::get('/', function () {
        return view('admin.users.index');
    })->name('index');
});

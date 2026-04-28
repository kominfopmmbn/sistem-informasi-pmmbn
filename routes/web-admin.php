<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth',
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

Route::middleware(['auth'])->group(function (): void {
    Route::resource('users', UserController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:users.view',
            'create' => 'permission:users.create',
            'store' => 'permission:users.create',
            'edit' => 'permission:users.update',
            'update' => 'permission:users.update',
            'destroy' => 'permission:users.delete',
        ]);

    Route::prefix('articles/{article}')
        ->middleware('permission:articles.update')
        ->as('articles.')
        ->group(function (): void {
            Route::patch('archive', [ArticleController::class, 'archive'])->name('archive');
            Route::patch('unarchive', [ArticleController::class, 'unarchive'])->name('unarchive');
        });

    Route::resource('articles', ArticleController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:articles.view',
            'create' => 'permission:articles.create',
            'store' => 'permission:articles.create',
            'edit' => 'permission:articles.update',
            'update' => 'permission:articles.update',
            'destroy' => 'permission:articles.delete',
        ]);

    Route::resource('roles', RoleController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:roles.view',
            'create' => 'permission:roles.create',
            'store' => 'permission:roles.create',
            'edit' => 'permission:roles.update',
            'update' => 'permission:roles.update',
            'destroy' => 'permission:roles.delete',
        ]);
});

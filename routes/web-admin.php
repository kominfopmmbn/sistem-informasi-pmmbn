<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\OrgRegionController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VillageController;
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

    Route::resource('org-regions', OrgRegionController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:org_regions.view',
            'create' => 'permission:org_regions.create',
            'store' => 'permission:org_regions.create',
            'edit' => 'permission:org_regions.update',
            'update' => 'permission:org_regions.update',
            'destroy' => 'permission:org_regions.delete',
        ]);

    Route::resource('colleges', CollegeController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:colleges.view',
            'create' => 'permission:colleges.create',
            'store' => 'permission:colleges.create',
            'edit' => 'permission:colleges.update',
            'update' => 'permission:colleges.update',
            'destroy' => 'permission:colleges.delete',
        ]);

    Route::resource('provinces', ProvinceController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:provinces.view',
            'create' => 'permission:provinces.create',
            'store' => 'permission:provinces.create',
            'edit' => 'permission:provinces.update',
            'update' => 'permission:provinces.update',
            'destroy' => 'permission:provinces.delete',
        ]);

    Route::resource('cities', CityController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:cities.view',
            'create' => 'permission:cities.create',
            'store' => 'permission:cities.create',
            'edit' => 'permission:cities.update',
            'update' => 'permission:cities.update',
            'destroy' => 'permission:cities.delete',
        ]);

    Route::resource('districts', DistrictController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:districts.view',
            'create' => 'permission:districts.create',
            'store' => 'permission:districts.create',
            'edit' => 'permission:districts.update',
            'update' => 'permission:districts.update',
            'destroy' => 'permission:districts.delete',
        ]);

    Route::resource('villages', VillageController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:villages.view',
            'create' => 'permission:villages.create',
            'store' => 'permission:villages.create',
            'edit' => 'permission:villages.update',
            'update' => 'permission:villages.update',
            'destroy' => 'permission:villages.delete',
        ]);
});

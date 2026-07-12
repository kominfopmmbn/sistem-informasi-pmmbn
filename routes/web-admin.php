<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\MemberActivationController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RegionalLeaderController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VillageController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth',
    'as' => 'dashboard.',
], function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
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
        ->middlewareFor('index', 'permission:users.view')
        ->middlewareFor(['create', 'store'], 'permission:users.create')
        ->middlewareFor(['edit', 'update'], 'permission:users.update')
        ->middlewareFor('destroy', 'permission:users.delete');

    Route::prefix('articles/{article}')
        ->middleware('permission:articles.update')
        ->as('articles.')
        ->group(function (): void {
            Route::patch('archive', [ArticleController::class, 'archive'])->name('archive');
            Route::patch('unarchive', [ArticleController::class, 'unarchive'])->name('unarchive');
        });

    Route::resource('articles', ArticleController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:articles.view')
        ->middlewareFor(['create', 'store'], 'permission:articles.create')
        ->middlewareFor(['edit', 'update'], 'permission:articles.update')
        ->middlewareFor('destroy', 'permission:articles.delete');

    Route::resource('documents', DocumentController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:documents.view')
        ->middlewareFor(['create', 'store'], 'permission:documents.create')
        ->middlewareFor(['edit', 'update'], 'permission:documents.update')
        ->middlewareFor('destroy', 'permission:documents.delete');

    Route::resource('roles', RoleController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:roles.view')
        ->middlewareFor(['create', 'store'], 'permission:roles.create')
        ->middlewareFor(['edit', 'update'], 'permission:roles.update')
        ->middlewareFor('destroy', 'permission:roles.delete');

    Route::resource('members', MemberController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:members.view')
        ->middlewareFor(['create', 'store'], 'permission:members.create')
        ->middlewareFor(['edit', 'update'], 'permission:members.update')
        ->middlewareFor('destroy', 'permission:members.delete');
    Route::prefix('member-activations/{member_activation}')
        ->middleware('permission:member-activations.update')
        ->as('member-activations.')
        ->group(function (): void {
            Route::get('suggestion-member', [MemberActivationController::class, 'getSuggestionMember'])->name('suggestion-member');
            Route::patch('accept', [MemberActivationController::class, 'accept'])->name('accept');
            Route::patch('reject', [MemberActivationController::class, 'reject'])->name('reject');
        });
    Route::resource('member-activations', MemberActivationController::class)
        ->except([
            'show',
            'create',
            'store',
        ])
        ->middlewareFor('index', 'permission:member-activations.view')
        ->middlewareFor(['edit', 'update'], 'permission:member-activations.update')
        ->middlewareFor('destroy', 'permission:member-activations.delete');

    Route::delete('member-activations/{member_activation}/media/{media}', [MemberActivationController::class, 'destroySupportingMedia'])
        ->middleware('permission:member-activations.update')
        ->name('member-activations.supporting-media.destroy');

    Route::delete('members/{member}/media/{media}', [MemberController::class, 'destroySupportingMedia'])
        ->middleware('permission:members.update')
        ->name('members.supporting-media.destroy');

    Route::resource('colleges', CollegeController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:colleges.view')
        ->middlewareFor(['create', 'store'], 'permission:colleges.create')
        ->middlewareFor(['edit', 'update'], 'permission:colleges.update')
        ->middlewareFor('destroy', 'permission:colleges.delete');

    Route::resource('regional-leaders', RegionalLeaderController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:regional-leaders.view')
        ->middlewareFor(['create', 'store'], 'permission:regional-leaders.create')
        ->middlewareFor(['edit', 'update'], 'permission:regional-leaders.update')
        ->middlewareFor('destroy', 'permission:regional-leaders.delete');

    Route::resource('programs', ProgramController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:programs.view')
        ->middlewareFor(['create', 'store'], 'permission:programs.create')
        ->middlewareFor(['edit', 'update'], 'permission:programs.update')
        ->middlewareFor('destroy', 'permission:programs.delete');

    Route::delete('programs/{program}/media/{media}', [ProgramController::class, 'destroyGalleryMedia'])
        ->middleware('permission:programs.update')
        ->name('programs.media.destroy');

    Route::resource('provinces', ProvinceController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:provinces.view')
        ->middlewareFor(['create', 'store'], 'permission:provinces.create')
        ->middlewareFor(['edit', 'update'], 'permission:provinces.update')
        ->middlewareFor('destroy', 'permission:provinces.delete');

    Route::resource('cities', CityController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:cities.view')
        ->middlewareFor(['create', 'store'], 'permission:cities.create')
        ->middlewareFor(['edit', 'update'], 'permission:cities.update')
        ->middlewareFor('destroy', 'permission:cities.delete');

    Route::resource('districts', DistrictController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:districts.view')
        ->middlewareFor(['create', 'store'], 'permission:districts.create')
        ->middlewareFor(['edit', 'update'], 'permission:districts.update')
        ->middlewareFor('destroy', 'permission:districts.delete');

    Route::resource('villages', VillageController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:villages.view')
        ->middlewareFor(['create', 'store'], 'permission:villages.create')
        ->middlewareFor(['edit', 'update'], 'permission:villages.update')
        ->middlewareFor('destroy', 'permission:villages.delete');
});

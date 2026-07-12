<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Member;
use App\Models\Program;
use App\Support\ArticleGrid;
use Closure;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HomePageController extends Controller
{
    private const int HOME_TAB_LIMIT = 4;

    public function index()
    {
        $news = ArticleGrid::latestBerita(self::HOME_TAB_LIMIT);
        $opinions = ArticleGrid::latestOpini(self::HOME_TAB_LIMIT);

        $programs = Program::active()
            ->with(['media' => fn ($q) => $q->where('collection_name', Program::COVER_COLLECTION)])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $heroSlides = [
            asset('assets-front-pages/img/bg-hero-home.JPG'),
            asset('assets-front-pages/img/download-hero.png'),
            asset('assets-front-pages/img/kta-hero.png'),
            asset('assets-front-pages/img/fotbar-hero.JPG'),
        ];

        // Cache murni array skalar (bukan Collection/model apa pun) — driver cache `database`
        // men-serialize isinya, dan objek (Eloquent maupun Support\Collection) bisa gagal
        // di-unserialize jadi __PHP_Incomplete_Class. Array biasa selalu aman.
        [
            'campusData' => $campusData,
            'provinceCount' => $provinceCount,
            'collegeCount' => $collegeCount,
            'cityCount' => $cityCount,
        ] = $this->rememberHomeData(
            College::HOME_COLLEGES_CACHE_KEY,
            function () {
                $colleges = College::query()
                    ->with(['province', 'city'])
                    ->whereNotNull('lat')
                    ->whereNotNull('long')
                    ->orderBy('name')
                    ->get();

                $campusData = $colleges
                    ->groupBy(fn (College $college) => Str::title($college->province?->name ?? 'Lainnya'))
                    ->map(fn ($group) => [
                        // center = rata-rata koordinat kampus di provinsi itu (untuk posisi pill + fitBounds)
                        'center' => [$group->avg('lat'), $group->avg('long')],
                        'campuses' => $group->map(fn (College $college) => [
                            'name' => $college->name,
                            'lat' => $college->lat,
                            'lng' => $college->long,
                            'kota' => Str::title($college->city?->name ?? ''),
                        ])->values()->all(),
                    ]);

                return [
                    'campusData' => $campusData->toArray(),
                    'provinceCount' => $campusData->count(),
                    'collegeCount' => $colleges->count(),
                    'cityCount' => $colleges->pluck('city_code')->filter()->unique()->count(),
                ];
            },
        );

        $memberCount = $this->rememberHomeData(
            Member::HOME_MEMBER_COUNT_CACHE_KEY,
            fn () => Member::count(),
        );

        return view('front.home.index', compact(
            'news',
            'opinions',
            'programs',
            'heroSlides',
            'campusData',
            'provinceCount',
            'collegeCount',
            'cityCount',
            'memberCount',
        ));
    }

    /**
     * Ambil data home dari cache; saat miss, satu request saja yang menghitung ulang
     * (dilindungi lock) — sisanya menunggu, mencegah dogpile di halaman terpanas ini.
     *
     * Invalidasi bersifat event-based (model event College/Member + forget post-commit di
     * MemberActivationController@accept). Tulisan yang mem-bypass Eloquent (seeder, SQL mentah,
     * bulk ->update()/->delete()) TIDAK memicu invalidasi — jalankan `php artisan cache:clear`
     * atau `Cache::forget` key terkait setelahnya; TTL 1 hari adalah backstop.
     */
    private function rememberHomeData(string $key, Closure $callback): mixed
    {
        if (($cached = Cache::get($key)) !== null) {
            return $cached;
        }

        try {
            return Cache::lock($key.':lock', 10)->block(
                5,
                fn () => Cache::remember($key, now()->addDay(), $callback),
            );
        } catch (LockTimeoutException) {
            return $callback(); // fallback: hitung langsung daripada gagal
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;

#[Fillable(['name', 'province_code', 'city_code', 'lat', 'long'])]
class College extends Model
{
    /**
     * Cache data kampus di halaman home. Di-invalidate oleh `booted()` di bawah untuk mutasi
     * lewat Eloquent; tulisan yang mem-bypass Eloquent (seeder, SQL mentah, bulk update/delete)
     * TIDAK memicu invalidasi — jalankan `php artisan cache:clear` setelahnya (TTL 1 hari = backstop).
     */
    public const string HOME_COLLEGES_CACHE_KEY = 'home.colleges';

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::HOME_COLLEGES_CACHE_KEY));   // created + updated
        static::deleted(fn () => Cache::forget(self::HOME_COLLEGES_CACHE_KEY)); // hard delete
    }

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'long' => 'float',
        ];
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_code', 'code');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function memberActivations(): HasMany
    {
        return $this->hasMany(MemberActivation::class);
    }
}

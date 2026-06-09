<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable([
    'title',
    'slug',
    'excerpt',
    'about_heading',
    'about_content',
    'is_active',
    'sort_order',
])]
class Program extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;
    use Userstamps;

    /** Koleksi sampul single-file (gambar kartu daftar + latar hero detail). */
    public const COVER_COLLECTION = 'cover';

    /** Koleksi galeri multi-file (Dokumentasi Kegiatan). */
    public const GALLERY_COLLECTION = 'gallery';

    /** Maksimal gambar galeri dalam satu submit. */
    public const GALLERY_MAX_PER_SUBMIT = 12;

    public static function imageMimeList(): string
    {
        return 'jpg,jpeg,png,webp';
    }

    /** Nilai accepted-files Dropzone (ekstensi dengan titik), turunan dari imageMimeList(). */
    public static function imageDropzoneAcceptedFiles(): string
    {
        return collect(explode(',', self::imageMimeList()))
            ->map(fn (string $ext) => '.'.trim($ext))
            ->implode(',');
    }

    /** Aturan validasi per item `gallery.*` (selain nullable di level array). */
    public static function galleryItemRules(): array
    {
        $maxKb = (int) floor(config('media-library.max_file_size') / 1024);

        return [
            'image',
            'max:'.$maxKb,
            'mimes:'.self::imageMimeList(),
        ];
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::COVER_COLLECTION)->singleFile();
        $this->addMediaCollection(self::GALLERY_COLLECTION);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(ProgramGoal::class)->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}

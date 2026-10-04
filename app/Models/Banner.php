<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable([
    'title',
    'slug',
    'link_url',
    'is_active',
    'sort_order',
])]
class Banner extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;
    use Userstamps;

    /** Gambar banner (single-file) yang tampil di popup. */
    public const IMAGE_COLLECTION = 'image';

    public static function imageMimeList(): string
    {
        return 'jpg,jpeg,png,webp';
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::IMAGE_COLLECTION)->singleFile();
    }

    /** Link yang dibagikan admin (mis. di IG): membuka home dengan banner ini sebagai slide pertama. */
    public function shareUrl(): string
    {
        return route('home.index', ['banner' => $this->slug]);
    }

    /** Banner aktif dalam urutan tampil popup. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id');
    }
}

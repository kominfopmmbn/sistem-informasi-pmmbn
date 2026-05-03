<?php

namespace App\Models;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable([
    'nim',
    'full_name',
    'nickname',
    'email',
    'place_of_birth_code',
    'date_of_birth',
    'gender_id',
    'org_region_id',
    'phone_number',
])]
class Member extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;
    use Userstamps;

    /** Koleksi lampiran multi-file di admin anggota. */
    public const SUPPORTING_DOCUMENTS_COLLECTION = 'supporting_documents';

    /** Maksimal berkas dalam satu submit. */
    public const SUPPORTING_DOCUMENTS_MAX_PER_SUBMIT = 10;

    /** Maksimal total lampiran per anggota (termasuk yang sudah ada). */
    public const SUPPORTING_DOCUMENTS_MAX_TOTAL = 20;

    public static function supportingDocumentMimeList(): string
    {
        return 'pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,jpg,jpeg,png,gif,webp';
    }

    /** Aturan validasi per item `supporting_documents.*` (selain nullable di level array). */
    public static function supportingDocumentItemRules(): array
    {
        $maxKb = (int) floor(config('media-library.max_file_size') / 1024);

        return [
            'file',
            'max:'.$maxKb,
            'mimes:'.self::supportingDocumentMimeList(),
        ];
    }

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'gender_id' => Gender::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::SUPPORTING_DOCUMENTS_COLLECTION);
    }

    public function placeOfBirthCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'place_of_birth_code', 'code');
    }

    public function orgRegion(): BelongsTo
    {
        return $this->belongsTo(OrgRegion::class);
    }
}

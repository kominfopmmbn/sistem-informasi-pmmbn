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

    /** Nilai `accepted-files` Dropzone: ekstensi dengan titik, dipisahkan koma (turunan dari `supportingDocumentMimeList()`). */
    public static function supportingDocumentDropzoneAcceptedFiles(): string
    {
        return collect(explode(',', self::supportingDocumentMimeList()))
            ->map(fn (string $ext) => '.'.trim($ext))
            ->implode(',');
    }

    /** Gabungan ekstensi + MIME untuk atribut `accept` pada input file (turunan dari `supportingDocumentMimeList()`). */
    public static function supportingDocumentFileInputAccept(): string
    {
        $mimes = collect(explode(',', self::supportingDocumentMimeList()))
            ->map(fn (string $ext) => self::mimeTypeForSupportingDocumentExtension(trim($ext)))
            ->filter()
            ->unique()
            ->values()
            ->implode(',');

        return self::supportingDocumentDropzoneAcceptedFiles().','.$mimes;
    }

    private static function mimeTypeForSupportingDocumentExtension(string $ext): ?string
    {
        return match ($ext) {
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt' => 'text/plain',
            'zip' => 'application/zip',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => null,
        };
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

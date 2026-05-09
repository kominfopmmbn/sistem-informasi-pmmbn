<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\MemberActivationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Notifications\Notifiable;

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
class MemberActivation extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;
    use Userstamps;
    use Notifiable;

    /**
     * Nama koleksi sama seperti Member agar aturan ukuran/mime (validasi & Dropzone) tetap satu referensi.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(Member::SUPPORTING_DOCUMENTS_COLLECTION);
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

    public function placeOfBirthCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'place_of_birth_code', 'code');
    }

    public function orgRegion(): BelongsTo
    {
        return $this->belongsTo(OrgRegion::class);
    }

    public function memberActivationStatusLogs(): HasMany
    {
        return $this->hasMany(MemberActivationStatusLog::class);
    }

    public function currentStatus(): HasOne
    {
        return $this->hasOne(MemberActivationStatusLog::class)->orderBy('id', 'desc');
    }

    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }
}

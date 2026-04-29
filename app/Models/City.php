<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravolt\Indonesia\Models\City as BaseModel;

class City extends BaseModel
{
    /** Relasi harus memakai App\Models\Province agar accessor seperti name_with_code ikut dipakai. */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    public function getNameWithCodeAttribute(): string
    {
        return $this->name . ' (code: ' . $this->code . ')';
    }
}

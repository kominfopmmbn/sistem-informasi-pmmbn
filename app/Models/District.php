<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravolt\Indonesia\Models\District as BaseModel;

class District extends BaseModel
{
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_code', 'code');
    }

    public function getNameWithCodeAttribute(): string
    {
        return $this->name . ' (code: ' . $this->code . ')';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravolt\Indonesia\Models\Province as BaseModel;

class Province extends BaseModel
{
    public function getNameWithCodeAttribute(): string
    {
        return $this->name . ' (code: ' . $this->code . ')';
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'province_code', 'code');
    }
}

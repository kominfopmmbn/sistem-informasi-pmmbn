<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravolt\Indonesia\Models\Village as BaseModel;

class Village extends BaseModel
{
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_code', 'code');
    }

    public function getNameWithCodeAttribute(): string
    {
        return $this->name . ' (code: ' . $this->code . ')';
    }
}

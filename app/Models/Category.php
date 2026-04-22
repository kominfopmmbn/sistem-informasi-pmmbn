<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;

#[Fillable(['title', 'slug'])]
class Category extends Model
{
    use SoftDeletes, Userstamps;

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}

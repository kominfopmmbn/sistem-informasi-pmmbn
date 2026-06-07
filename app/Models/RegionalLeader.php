<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;

#[Fillable(['code', 'name'])]
class RegionalLeader extends Model
{
    use SoftDeletes, Userstamps;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mattiverse\Userstamps\Traits\Userstamps;

#[Fillable(['program_id', 'content', 'sort_order'])]
class ProgramGoal extends Model
{
    use Userstamps;

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}

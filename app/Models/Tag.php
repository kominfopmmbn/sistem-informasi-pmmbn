<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Mattiverse\Userstamps\Traits\Userstamps;

#[Fillable(['title', 'slug'])]
class Tag extends Model
{
    use SoftDeletes, Userstamps;

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    public static function findOrCreateFromTitle(string $title): self
    {
        $title = trim($title);
        if ($title === '') {
            throw new \InvalidArgumentException('Tag title cannot be empty.');
        }

        $base = Str::slug($title);
        if ($base === '') {
            $base = 'tag';
        }

        $slug = $base;
        $suffix = 2;

        while (true) {
            $existing = static::query()->where('slug', $slug)->first();

            if (! $existing) {
                return static::create([
                    'title' => $title,
                    'slug' => $slug,
                ]);
            }

            if (strcasecmp($existing->title, $title) === 0) {
                return $existing;
            }

            $slug = $base.'-'.$suffix;
            $suffix++;
        }
    }
}

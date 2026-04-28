<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;

#[Fillable([
    'category_id',
    'title',
    'slug',
    'subtitle',
    'content',
    'cover_photo_path',
    'published_at',
    'is_draft',
    'archived_at',
    'archived_by',
])]
class Article extends Model
{
    use SoftDeletes, Userstamps;

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'archived_at' => 'datetime',
            'is_draft' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_draft', false)
            ->where('published_at', '<=', now())
            ->whereNull('archived_at');
    }

    public function scopeOpini(Builder $query): Builder
    {
        return $query->where('category_id', Category::OPINI);
    }

    public function scopeBerita(Builder $query): Builder
    {
        return $query->where('category_id', Category::BERITA);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function archivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by');
    }
}

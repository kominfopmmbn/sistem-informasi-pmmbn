<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    /** Boleh edit jika punya articles.update dan (articles.other atau artikel sendiri). */
    public function update(User $user, Article $article): bool
    {
        if (! $user->can('articles.update')) {
            return false;
        }

        if ($user->can('articles.other')) {
            return true;
        }

        return $article->created_by !== null
            && (int) $article->created_by === (int) $user->id;
    }

    /** Boleh hapus jika punya articles.delete dan (articles.other atau artikel sendiri). */
    public function delete(User $user, Article $article): bool
    {
        if (! $user->can('articles.delete')) {
            return false;
        }

        if ($user->can('articles.other')) {
            return true;
        }

        return $article->created_by !== null
            && (int) $article->created_by === (int) $user->id;
    }
}

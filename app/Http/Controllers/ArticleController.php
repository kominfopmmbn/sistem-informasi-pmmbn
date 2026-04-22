<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $q = isset($filters['q']) ? trim($filters['q']) : '';

        $articles = Article::query()
            ->with(['category', 'tags']);

        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $articles->where('title', 'like', $like);
        }

        if (! empty($filters['category_id'])) {
            $articles->where('category_id', (int) $filters['category_id']);
        }

        $articles = $articles->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::query()->orderBy('title')->get();

        $filterState = [
            'q' => $q,
            'category_id' => $filters['category_id'] ?? null,
        ];

        $hasActiveFilters = $q !== '' || ! empty($filters['category_id']);

        return view('admin.articles.index', compact(
            'articles',
            'categories',
            'filterState',
            'hasActiveFilters'
        ));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('title')->get();
        $tags = Tag::query()->orderBy('title')->get();

        return view('admin.articles.create', compact('categories', 'tags'));
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['tags', 'cover_photo', 'save_action']);
        $data['is_draft'] = $request->string('save_action')->toString() === 'draft';
        $data['slug'] = $this->uniqueSlugFromTitle($request->string('title')->toString());

        if ($request->hasFile('cover_photo')) {
            $data['cover_photo_path'] = $request->file('cover_photo')->store('articles', 'public');
        }

        $article = Article::create($data);

        $article->tags()->sync($this->resolveTagIdsFromRequest($request));

        $success = $data['is_draft']
            ? 'Artikel disimpan sebagai draf.'
            : 'Artikel berhasil diterbitkan.';

        return redirect()
            ->route('admin.articles.index')
            ->with('success', $success);
    }

    public function edit(Article $article): View
    {
        $article->load(['tags']);
        $categories = Category::query()->orderBy('title')->get();
        $tags = Tag::query()->orderBy('title')->get();

        return view('admin.articles.edit', compact('article', 'categories', 'tags'));
    }

    public function update(UpdateArticleRequest $request, Article $article): RedirectResponse
    {
        $data = $request->safe()->except(['tags', 'cover_photo', 'remove_cover', 'save_action']);
        $data['is_draft'] = $request->string('save_action')->toString() === 'draft';
        $data['slug'] = $this->uniqueSlugFromTitle($request->string('title')->toString(), $article->getKey());

        if ($request->boolean('remove_cover') && $article->cover_photo_path) {
            Storage::disk('public')->delete($article->cover_photo_path);
            $data['cover_photo_path'] = null;
        }

        if ($request->hasFile('cover_photo')) {
            if ($article->cover_photo_path) {
                Storage::disk('public')->delete($article->cover_photo_path);
            }
            $data['cover_photo_path'] = $request->file('cover_photo')->store('articles', 'public');
        }

        $article->update($data);

        $article->tags()->sync($this->resolveTagIdsFromRequest($request));

        $success = $data['is_draft']
            ? 'Draf tersimpan.'
            : 'Artikel berhasil diperbarui dan diterbitkan.';

        return redirect()
            ->route('admin.articles.index')
            ->with('success', $success);
    }

    public function destroy(Article $article): RedirectResponse
    {
        if ($article->cover_photo_path) {
            Storage::disk('public')->delete($article->cover_photo_path);
        }

        $article->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    /**
     * @return list<int>
     */
    private function resolveTagIdsFromRequest(Request $request): array
    {
        $raw = $request->input('tags', []);
        if (! is_array($raw)) {
            return [];
        }

        $ids = [];

        foreach ($raw as $item) {
            if ($item === null) {
                continue;
            }

            $str = trim((string) $item);

            if ($str === '') {
                continue;
            }

            if (ctype_digit($str)) {
                $id = (int) $str;
                if (Tag::query()->whereKey($id)->exists()) {
                    $ids[] = $id;
                }

                continue;
            }

            $ids[] = Tag::findOrCreateFromTitle($str)->getKey();
        }

        return array_values(array_unique($ids));
    }

    private function uniqueSlugFromTitle(string $title, ?int $ignoreArticleId = null): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'artikel';
        }

        $slug = $base;
        $suffix = 2;

        while (Article::query()
            ->when($ignoreArticleId !== null, fn ($q) => $q->where('id', '!=', $ignoreArticleId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}

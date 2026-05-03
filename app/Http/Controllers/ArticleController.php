<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'archive_status' => ['nullable', 'in:active,archived'],
        ]);

        $q = isset($filters['q']) ? trim($filters['q']) : '';
        $archiveStatus = $filters['archive_status'] ?? 'active';

        $articles = Article::query()
            ->with(['category', 'tags']);

        if (! $request->user()->can('articles.other')) {
            $articles->where('created_by', $request->user()->id);
        }

        if ($archiveStatus === 'archived') {
            $articles->whereNotNull('archived_at');
        } else {
            $articles->whereNull('archived_at');
        }

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

        $categories = Category::query()->orderBy('title', 'asc')->get();

        $filterState = [
            'q' => $q,
            'category_id' => $filters['category_id'] ?? null,
            'archive_status' => $archiveStatus,
        ];

        $hasActiveFilters = $q !== '' || ! empty($filters['category_id']) || $archiveStatus === 'archived';

        return view('admin.articles.index', compact(
            'articles',
            'categories',
            'filterState',
            'hasActiveFilters'
        ));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('title', 'asc')->get();
        $tags = Tag::query()->orderBy('title', 'asc')->get();

        return view('admin.articles.create', compact('categories', 'tags'));
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['tags', 'cover_photo', 'save_action']);
        $data['is_draft'] = $request->string('save_action')->toString() === 'draft';
        $data['slug'] = $this->uniqueSlugFromTitle($request->string('title')->toString());

        $article = Article::create($data);

        if ($request->hasFile('cover_photo')) {
            $article->addMediaFromRequest('cover_photo')->toMediaCollection(Article::COVER_COLLECTION);
        }

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
        $this->authorize('update', $article);

        $article->load([
            'tags',
            'media' => fn ($mq) => $mq->where('collection_name', Article::COVER_COLLECTION),
        ]);
        $categories = Category::query()->orderBy('title', 'asc')->get();
        $tags = Tag::query()->orderBy('title', 'asc')->get();

        return view('admin.articles.edit', compact('article', 'categories', 'tags'));
    }

    public function update(UpdateArticleRequest $request, Article $article): RedirectResponse
    {
        $this->authorize('update', $article);

        $data = $request->safe()->except(['tags', 'cover_photo', 'remove_cover', 'save_action']);
        $data['is_draft'] = $request->string('save_action')->toString() === 'draft';
        $data['slug'] = $this->uniqueSlugFromTitle($request->string('title')->toString(), $article->getKey());

        $article->update($data);

        if ($request->hasFile('cover_photo')) {
            $article->addMediaFromRequest('cover_photo')->toMediaCollection(Article::COVER_COLLECTION);
        } elseif ($request->boolean('remove_cover')) {
            $article->clearMediaCollection(Article::COVER_COLLECTION);
        }

        $article->tags()->sync($this->resolveTagIdsFromRequest($request));

        $success = $data['is_draft']
            ? 'Draf tersimpan.'
            : 'Artikel berhasil diperbarui dan diterbitkan.';

        return redirect()
            ->route('admin.articles.index')
            ->with('success', $success);
    }

    /**
     * @param Model $article
     * @return RedirectResponse
     */
    public function destroy(Article $article): RedirectResponse
    {
        $this->authorize('delete', $article);

        $article->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    public function archive(Request $request, Article $article): RedirectResponse
    {
        $this->authorize('update', $article);

        $article->update([
            'archived_at' => now(),
            'archived_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.articles.index', $this->indexQueryForRedirect($request))
            ->with('success', 'Artikel diarsipkan.');
    }

    public function unarchive(Request $request, Article $article): RedirectResponse
    {
        $this->authorize('update', $article);

        $article->update([
            'archived_at' => null,
            'archived_by' => null,
        ]);

        return redirect()
            ->route('admin.articles.index', $this->indexQueryForRedirect($request))
            ->with('success', 'Artikel dikembalikan dari arsip.');
    }

    /**
     * Query string untuk redirect ke index setelah arsip/unarchive (pertahankan filter).
     *
     * @return array<string, string>
     */
    private function indexQueryForRedirect(Request $request): array
    {
        $out = [];

        $q = trim((string) $request->input('q', ''));
        if ($q !== '') {
            $out['q'] = $q;
        }

        $categoryId = $request->input('category_id');
        if ($categoryId !== null && $categoryId !== '') {
            $out['category_id'] = (string) $categoryId;
        }

        $status = $request->input('archive_status', 'active');
        if ($status === 'archived') {
            $out['archive_status'] = 'archived';
        }

        return $out;
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

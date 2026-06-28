<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleIndexFilterTest extends TestCase
{
    use RefreshDatabase;

    private function category(): Category
    {
        return Category::query()->create(['title' => 'Berita', 'slug' => 'berita']);
    }

    private function article(Category $category, string $title, string $publishedAt): Article
    {
        return Article::query()->create([
            'category_id' => $category->id,
            'title' => $title,
            'slug' => \Str::slug($title),
            'content' => '<p>konten</p>',
            'published_at' => $publishedAt,
            'is_draft' => false,
        ]);
    }

    public function test_lists_all_articles_without_filter(): void
    {
        $category = $this->category();
        $this->article($category, 'Banjir Bandang', '2026-01-10');
        $this->article($category, 'Panen Raya', '2025-06-10');

        $this->get(route('article.index', $category->slug))
            ->assertOk()
            ->assertSee('Banjir Bandang')
            ->assertSee('Panen Raya');
    }

    public function test_search_filters_by_title(): void
    {
        $category = $this->category();
        $this->article($category, 'Banjir Bandang', '2026-01-10');
        $this->article($category, 'Panen Raya', '2026-01-10');

        $this->get(route('article.index', [$category->slug, 'q' => 'banjir']))
            ->assertOk()
            ->assertSee('Banjir Bandang')
            ->assertDontSee('Panen Raya');
    }

    public function test_filters_by_month_and_year(): void
    {
        $category = $this->category();
        $this->article($category, 'Banjir Bandang', '2026-01-10');
        $this->article($category, 'Panen Raya', '2025-06-10');

        $this->get(route('article.index', [$category->slug, 'month' => 1, 'year' => 2026]))
            ->assertOk()
            ->assertSee('Banjir Bandang')
            ->assertDontSee('Panen Raya');
    }
}

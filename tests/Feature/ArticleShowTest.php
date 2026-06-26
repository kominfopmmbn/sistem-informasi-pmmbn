<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleShowTest extends TestCase
{
    use RefreshDatabase;

    private function publishedArticle(): Article
    {
        $category = Category::query()->create(['title' => 'Berita', 'slug' => 'berita']);

        return Article::query()->create([
            'category_id' => $category->id,
            'title' => 'judul-uji',
            'slug' => 'judul-uji',
            'content' => '<p>konten</p>',
            'published_at' => now(),
            'is_draft' => false,
        ]);
    }

    public function test_show_increments_views_count_on_each_visit(): void
    {
        $article = $this->publishedArticle();

        $this->get(route('article.show', $article->slug))->assertOk();
        $this->assertSame(1, $article->fresh()->views_count);

        $this->get(route('article.show', $article->slug))->assertOk();
        $this->assertSame(2, $article->fresh()->views_count);
    }

    public function test_show_does_not_touch_updated_at_or_updated_by(): void
    {
        $editor = User::factory()->create();
        $article = $this->publishedArticle();

        // Set audit fields to known values, bypassing Userstamps.
        Article::whereKey($article->getKey())->toBase()->update([
            'updated_by' => $editor->id,
            'updated_at' => '2020-01-01 00:00:00',
        ]);

        $this->get(route('article.show', $article->slug))->assertOk();

        $fresh = $article->fresh();
        $this->assertSame($editor->id, $fresh->updated_by);
        $this->assertSame('2020-01-01 00:00:00', $fresh->updated_at->format('Y-m-d H:i:s'));
    }
}

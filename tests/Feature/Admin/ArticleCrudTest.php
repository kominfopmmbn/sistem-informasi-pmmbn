<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    private function actingAsUser(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_guest_is_redirected_from_articles_index_to_admin_login(): void
    {
        $this->get(route('admin.articles.index'))
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_guest_cannot_post_to_store_articles(): void
    {
        $this->post(route('admin.articles.store'), [])
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_index_shows_articles_and_respects_query_filters(): void
    {
        $this->actingAsUser();

        $catA = Category::query()->create(['title' => 'Berita', 'slug' => 'berita']);
        $catB = Category::query()->create(['title' => 'Opini', 'slug' => 'opini']);

        Article::query()->create([
            'category_id' => $catA->id,
            'title' => 'alpha-headline-unique',
            'slug' => 'alpha-headline-unique',
            'content' => '<p>konten</p>',
            'published_at' => now(),
            'is_draft' => false,
        ]);
        Article::query()->create([
            'category_id' => $catB->id,
            'title' => 'other-story',
            'slug' => 'other-story',
            'content' => '<p>lain</p>',
            'published_at' => now(),
            'is_draft' => false,
        ]);

        $this->get(route('admin.articles.index'))->assertOk()
            ->assertViewHas('articles', fn ($paginator) => $paginator->total() === 2);

        $r = $this->get(route('admin.articles.index', ['q' => 'alpha-headline']))->assertOk();
        $this->assertSame(1, $r->viewData('articles')->total());

        $r = $this->get(route('admin.articles.index', ['category_id' => (string) $catB->id]))->assertOk();
        $this->assertSame(1, $r->viewData('articles')->total());
    }

    public function test_create_renders(): void
    {
        $this->actingAsUser();
        Category::query()->create(['title' => 'C', 'slug' => 'c']);

        $this->get(route('admin.articles.create'))->assertOk();
    }

    public function test_store_publish_persists_article_and_flashes_success(): void
    {
        Storage::fake('public');
        $this->actingAsUser();
        $cat = Category::query()->create(['title' => 'Kategori', 'slug' => 'kategori']);
        $tag = Tag::query()->create(['title' => 'Satu', 'slug' => 'satu']);

        $file = UploadedFile::fake()->image('cover.jpg', 800, 600);

        $this->post(route('admin.articles.store'), [
            'save_action' => 'publish',
            'title' => 'Judul Publikasi',
            'subtitle' => 'Sub',
            'content' => '<p>Isi teks cukup</p>',
            'category_id' => (string) $cat->id,
            'published_at' => '2025-01-10 10:00',
            'tags' => [(string) $tag->id, 'Tag Baru Dari Teks'],
            'cover_photo' => $file,
        ])->assertRedirect(route('admin.articles.index'))
            ->assertSessionHas('success', 'Artikel berhasil diterbitkan.');

        $this->assertDatabaseHas('articles', [
            'title' => 'Judul Publikasi',
            'is_draft' => false,
            'category_id' => $cat->id,
            'slug' => 'judul-publikasi',
        ]);

        $article = Article::query()->where('slug', 'judul-publikasi')->firstOrFail();
        $this->assertNotNull($article->cover_photo_path);
        $this->assertTrue(Storage::disk('public')->exists($article->cover_photo_path));
        $this->assertCount(2, $article->tags);
        $this->assertTrue($article->tags->contains('title', 'Satu'));
        $this->assertTrue($article->tags->contains('title', 'Tag Baru Dari Teks'));
    }

    public function test_store_draft_allows_empty_content(): void
    {
        $this->actingAsUser();
        $cat = Category::query()->create(['title' => 'C', 'slug' => 'c-2']);

        $this->post(route('admin.articles.store'), [
            'save_action' => 'draft',
            'title' => 'Draf Saja',
            'category_id' => (string) $cat->id,
            'content' => null,
        ])->assertRedirect(route('admin.articles.index'))
            ->assertSessionHas('success', 'Artikel disimpan sebagai draf.');

        $this->assertDatabaseHas('articles', [
            'title' => 'Draf Saja',
            'is_draft' => true,
            'slug' => 'draf-saja',
        ]);
    }

    public function test_store_validates_on_publish_with_invalid_payload(): void
    {
        $this->actingAsUser();
        $cat = Category::query()->create(['title' => 'C', 'slug' => 'c-3']);

        $this->from(route('admin.articles.create'))
            ->post(route('admin.articles.store'), [
                'save_action' => 'publish',
                'title' => '',
                'content' => '<p>teks cukup</p>',
                'category_id' => (string) $cat->id,
                'published_at' => '2025-01-10 10:00',
            ])
            ->assertSessionHasErrors('title');

        $this->from(route('admin.articles.create'))
            ->post(route('admin.articles.store'), [
                'save_action' => 'publish',
                'title' => 'Judul Oke',
                'content' => '<p></p>',
                'category_id' => (string) $cat->id,
                'published_at' => '2025-01-10 10:00',
            ])
            ->assertSessionHasErrors('content');
    }

    public function test_edit_renders(): void
    {
        $this->actingAsUser();
        $cat = Category::query()->create(['title' => 'C', 'slug' => 'c-4']);
        $article = Article::query()->create([
            'category_id' => $cat->id,
            'title' => 'E',
            'slug' => 'e',
            'content' => '<p>x</p>',
            'published_at' => now(),
            'is_draft' => false,
        ]);

        $this->get(route('admin.articles.edit', $article))->assertOk();
    }

    public function test_update_publish_changes_title_slug_and_flashes(): void
    {
        $this->actingAsUser();
        $cat = Category::query()->create(['title' => 'C', 'slug' => 'c-5']);
        $article = Article::query()->create([
            'category_id' => $cat->id,
            'title' => 'Lama',
            'slug' => 'lama',
            'content' => '<p>lama</p>',
            'published_at' => now(),
            'is_draft' => false,
        ]);

        $this->put(route('admin.articles.update', $article), [
            'save_action' => 'publish',
            'title' => 'Baru Diterbitkan',
            'content' => '<p>pembaruan</p>',
            'category_id' => (string) $cat->id,
            'published_at' => '2025-02-01 12:00',
        ])->assertRedirect(route('admin.articles.index'))
            ->assertSessionHas('success', 'Artikel berhasil diperbarui dan diterbitkan.');

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Baru Diterbitkan',
            'slug' => 'baru-diterbitkan',
            'is_draft' => false,
        ]);
    }

    public function test_update_draft_flashes_teks_draf_tersimpan(): void
    {
        $this->actingAsUser();
        $cat = Category::query()->create(['title' => 'C', 'slug' => 'c-6']);
        $article = Article::query()->create([
            'category_id' => $cat->id,
            'title' => 'A',
            'slug' => 'a',
            'content' => null,
            'published_at' => null,
            'is_draft' => true,
        ]);

        $this->put(route('admin.articles.update', $article), [
            'save_action' => 'draft',
            'title' => 'A',
            'category_id' => (string) $cat->id,
        ])->assertSessionHas('success', 'Draf tersimpan.');
    }

    public function test_update_replaces_cover_and_removes_old_file(): void
    {
        Storage::fake('public');
        $this->actingAsUser();
        $cat = Category::query()->create(['title' => 'C', 'slug' => 'c-7']);
        $old = UploadedFile::fake()->image('old.jpg', 10, 10);
        $oldPath = $old->store('articles', 'public');
        $article = Article::query()->create([
            'category_id' => $cat->id,
            'title' => 'Dengan Sampul',
            'slug' => 'dengan-sampul',
            'content' => '<p>x</p>',
            'cover_photo_path' => $oldPath,
            'published_at' => now(),
            'is_draft' => false,
        ]);
        $this->assertTrue(Storage::disk('public')->exists($oldPath));

        $new = UploadedFile::fake()->image('new.png', 20, 20);
        $this->put(route('admin.articles.update', $article), [
            'save_action' => 'publish',
            'title' => 'Dengan Sampul',
            'content' => '<p>isi</p>',
            'category_id' => (string) $cat->id,
            'published_at' => '2025-03-01 10:00',
            'cover_photo' => $new,
        ])->assertRedirect(route('admin.articles.index'));

        $article->refresh();
        $this->assertNotSame($oldPath, $article->cover_photo_path);
        $this->assertFalse(Storage::disk('public')->exists($oldPath));
        $this->assertTrue(Storage::disk('public')->exists($article->cover_photo_path));
    }

    public function test_update_with_remove_cover_clears_path_and_deletes_file(): void
    {
        Storage::fake('public');
        $this->actingAsUser();
        $cat = Category::query()->create(['title' => 'C', 'slug' => 'c-8']);
        $file = UploadedFile::fake()->image('c.jpg', 5, 5);
        $path = $file->store('articles', 'public');
        $article = Article::query()->create([
            'category_id' => $cat->id,
            'title' => 'Hapus Sampul',
            'slug' => 'hapus-sampul',
            'content' => '<p>k</p>',
            'cover_photo_path' => $path,
            'published_at' => now(),
            'is_draft' => false,
        ]);
        $this->assertTrue(Storage::disk('public')->exists($path));

        $this->put(route('admin.articles.update', $article), [
            'save_action' => 'publish',
            'title' => 'Hapus Sampul',
            'content' => '<p>isi</p>',
            'category_id' => (string) $cat->id,
            'published_at' => '2025-04-01 08:00',
            'remove_cover' => '1',
        ])->assertRedirect(route('admin.articles.index'));

        $article->refresh();
        $this->assertNull($article->cover_photo_path);
        $this->assertFalse(Storage::disk('public')->exists($path));
    }

    public function test_destroy_soft_deletes_article(): void
    {
        $this->actingAsUser();
        $cat = Category::query()->create(['title' => 'C', 'slug' => 'c-9']);
        $article = Article::query()->create([
            'category_id' => $cat->id,
            'title' => 'Hapus',
            'slug' => 'hapus',
            'content' => '<p>x</p>',
            'published_at' => now(),
            'is_draft' => false,
        ]);

        $this->delete(route('admin.articles.destroy', $article))
            ->assertRedirect(route('admin.articles.index'))
            ->assertSessionHas('success', 'Artikel berhasil dihapus.');

        $this->assertSoftDeleted('articles', ['id' => $article->id]);
    }

    public function test_destroy_deletes_cover_from_public_disk(): void
    {
        Storage::fake('public');
        $this->actingAsUser();
        $cat = Category::query()->create(['title' => 'C', 'slug' => 'c-10']);
        $file = UploadedFile::fake()->image('d.jpg', 4, 4);
        $path = $file->store('articles', 'public');
        $article = Article::query()->create([
            'category_id' => $cat->id,
            'title' => 'X',
            'slug' => 'x-del',
            'content' => '<p>x</p>',
            'cover_photo_path' => $path,
            'published_at' => now(),
            'is_draft' => false,
        ]);

        $this->delete(route('admin.articles.destroy', $article))->assertRedirect();

        $this->assertFalse(Storage::disk('public')->exists($path));
        $this->assertSoftDeleted('articles', ['id' => $article->id]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::query()->pluck('id', 'slug');
        $tagIds = Tag::query()->pluck('id', 'slug');

        if ($categories->isEmpty()) {
            throw new \InvalidArgumentException('Jalankan CategorySeeder dulu.');
        }
        if ($tagIds->isEmpty()) {
            throw new \InvalidArgumentException('Jalankan TagSeeder dulu.');
        }

        $categorySlugs = $categories->keys()->all();
        $allTagSlugs = $tagIds->keys()->all();
        $maxTags = min(3, count($allTagSlugs));

        for ($i = 0; $i < 50; $i++) {
            $title = rtrim(fake()->sentence(fake()->numberBetween(4, 8)), '.');
            $slug = Str::slug($title).'-'.$i;
            $categoryId = (int) $categories[fake()->randomElement($categorySlugs)];
            $isDraft = fake()->boolean(30);
            $publishedAt = $isDraft
                ? null
                : fake()->dateTimeBetween('-90 days', 'now');
            $subtitle = fake()->boolean(70) ? rtrim(fake()->sentence(fake()->numberBetween(5, 12)), '.') : null;
            $author = fake()->boolean(80) ? fake()->name() : null;
            $content = collect(range(1, fake()->numberBetween(1, 3)))
                ->map(function () {
                    return '<p>'.e(fake()->paragraph()).'</p>';
                })
                ->implode('');
            $tagCount = fake()->numberBetween(1, $maxTags);
            $selectedTagSlugs = fake()->randomElements($allTagSlugs, $tagCount);
            $syncTagIds = collect($selectedTagSlugs)
                ->map(fn (string $tagSlug) => (int) $tagIds[$tagSlug])
                ->all();

            $archivedAt = fake()->boolean(40) ? fake()->dateTimeBetween('-90 days', 'now') : null;
            $createdBy = User::query()->inRandomOrder('')->first()->id;
            $archivedBy = $archivedAt ? $createdBy : null;
            $article = Article::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categoryId,
                    'title' => $title,
                    'subtitle' => $subtitle,
                    'author' => $author,
                    'content' => $content,
                    'is_draft' => $isDraft,
                    'published_at' => $publishedAt,
                    'archived_at' => $archivedAt,
                    'archived_by' => $archivedBy,
                    'created_by' => $createdBy,
                ]
            );

            if(!$isDraft && !$archivedAt) {
                $this->attachRemoteImage(
                    $article,
                    "https://picsum.photos/seed/{$slug}/1200/800",
                    'cover-'.$i.'.jpg',
                );
            }

            $article->tags()->sync($syncTagIds);
        }
    }

    /**
     * Lampirkan sampul dari URL remote; fallback ke gambar palsu bila gagal.
     * Meniru ProgramSeeder::attachRemoteImage(). Cover bersifat singleFile →
     * lewati bila sudah ada agar tak mengunduh ulang tiap run (idempoten).
     */
    private function attachRemoteImage(Article $article, string $url, string $name): void
    {
        if ($article->getFirstMedia(Article::COVER_COLLECTION) !== null) {
            return;
        }

        try {
            $article->addMediaFromUrl($url)
                ->usingFileName($name)
                ->toMediaCollection(Article::COVER_COLLECTION);
        } catch (\Throwable $e) {
            $this->command?->warn("Gagal mengunduh {$url} ({$e->getMessage()}). Memakai gambar placeholder.");
            $article->addMedia(UploadedFile::fake()->image($name, 1200, 800))
                ->toMediaCollection(Article::COVER_COLLECTION);
        }
    }
}

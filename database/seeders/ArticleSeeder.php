<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;
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

        for ($i = 0; $i < 8; $i++) {
            $title = rtrim(fake()->sentence(fake()->numberBetween(4, 8)), '.');
            $slug = Str::slug($title).'-'.$i;
            $categoryId = (int) $categories[fake()->randomElement($categorySlugs)];
            $isDraft = fake()->boolean(30);
            $publishedAt = $isDraft
                ? null
                : fake()->dateTimeBetween('-90 days', 'now');
            $subtitle = fake()->boolean(70) ? rtrim(fake()->sentence(fake()->numberBetween(5, 12)), '.') : null;
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

            $article = Article::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categoryId,
                    'title' => $title,
                    'subtitle' => $subtitle,
                    'content' => $content,
                    'is_draft' => $isDraft,
                    'published_at' => $publishedAt,
                ]
            );
            $article->tags()->sync($syncTagIds);
        }
    }
}

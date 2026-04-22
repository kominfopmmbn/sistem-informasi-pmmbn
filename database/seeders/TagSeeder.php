<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Politik',
            'Hukum',
            'Lingkungan',
            'Pembangunan',
            'Opini',
        ];

        foreach ($tags as $title) {
            $slug = Str::slug($title);
            Tag::updateOrCreate(
                ['slug' => $slug],
                ['title' => $title]
            );
        }
    }
}

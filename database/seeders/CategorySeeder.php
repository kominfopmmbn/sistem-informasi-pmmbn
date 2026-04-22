<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['title' => 'Berita', 'slug' => Str::slug('Berita')],
            ['title' => 'Opini', 'slug' => Str::slug('Opini')],
        ];

        foreach ($categories as $row) {
            Category::updateOrCreate(
                ['slug' => $row['slug']],
                ['title' => $row['title']]
            );
        }
    }
}

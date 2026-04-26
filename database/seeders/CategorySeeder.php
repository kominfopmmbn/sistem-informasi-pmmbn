<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => Category::BERITA,
                'title' => 'Berita',
                'slug' => Str::slug('Berita')
            ],
            [
                'id' => Category::OPINI,
                'title' => 'Opini',
                'slug' => Str::slug('Opini')
            ],
        ];

        foreach ($categories as $row) {
            DB::table('categories')->updateOrInsert(
                ['id' => $row['id']],
                [
                    'title' => $row['title'],
                    'slug' => $row['slug'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

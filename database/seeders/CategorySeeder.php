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
                'slug' => Str::slug('Berita'),
                'description' => 'Menyajikan berita, pembaruan, dan perkembangan terbaru seputar dunia digital, desain, dan teknologi yang relevan untuk dibaca hari ini.',
            ],
            [
                'id' => Category::OPINI,
                'title' => 'Opini',
                'slug' => Str::slug('Opini'),
                'description' => 'Kolom pandangan dan analisis reflektif tentang isu moderasi beragama, kebangsaan, serta peran mahasiswa dalam pembangunan bermartabat.',
            ],
        ];

        foreach ($categories as $row) {
            DB::table('categories')->updateOrInsert(
                ['id' => $row['id']],
                [
                    'title' => $row['title'],
                    'slug' => $row['slug'],
                    'description' => $row['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Jumlah dokument admin yang diisi untuk demo / pengembangan lokal.
     */
    private const COUNT = 15;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Document::factory()->count(self::COUNT)->create();
    }
}

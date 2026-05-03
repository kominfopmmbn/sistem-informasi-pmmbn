<?php

namespace Tests\Feature;

use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DownloadPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_download_page_lists_documents_with_files(): void
    {
        $document = Document::factory()->create([
            'title' => 'Pedoman Resmi PMMBN',
        ]);

        $response = $this->get(route('download.index'));

        $response->assertOk();
        $response->assertSee('Pedoman Resmi PMMBN');
        $response->assertSee('Download');
    }

    public function test_download_page_shows_empty_message_when_no_documents(): void
    {
        $response = $this->get(route('download.index'));

        $response->assertOk();
        $response->assertSee('Belum ada dokumen yang dapat diunduh.');
    }
}

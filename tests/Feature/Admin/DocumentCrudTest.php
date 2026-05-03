<?php

namespace Tests\Feature\Admin;

use App\Models\Document;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DocumentCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    private function actingAsAdministrator(): User
    {
        Role::firstOrCreate(
            ['name' => 'Administrator', 'guard_name' => 'web'],
        );

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('Administrator');
        $this->actingAs($user);

        return $user;
    }

    private function fakePdf(): UploadedFile
    {
        return UploadedFile::fake()->create('dokumen.pdf', 200, 'application/pdf');
    }

    public function test_guest_is_redirected_from_documents_index_to_admin_login(): void
    {
        $this->get(route('admin.documents.index'))
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_guest_cannot_post_to_store_documents(): void
    {
        $this->post(route('admin.documents.store'), [])
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_index_forbidden_without_documents_view_permission(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('admin.documents.index'))->assertForbidden();
    }

    public function test_index_shows_documents(): void
    {
        $this->actingAsAdministrator();
        Document::query()->create(['title' => 'Dokumen Tes']);

        $this->get(route('admin.documents.index'))->assertOk()
            ->assertSee('Dokumen Tes', false);
    }

    public function test_index_filters_by_q(): void
    {
        $this->actingAsAdministrator();
        Document::query()->create(['title' => 'Alpha Satu']);
        Document::query()->create(['title' => 'Beta Dua']);

        $this->get(route('admin.documents.index', ['q' => 'Alpha']))
            ->assertOk()
            ->assertSee('Alpha Satu', false)
            ->assertDontSee('Beta Dua', false);
    }

    public function test_store_persists_document_and_media(): void
    {
        Storage::fake('public');
        $this->actingAsAdministrator();

        $this->post(route('admin.documents.store'), [
            'title' => 'PDF Penting',
            'file' => $this->fakePdf(),
        ])->assertRedirect(route('admin.documents.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('documents', ['title' => 'PDF Penting']);
        $document = Document::query()->where('title', 'PDF Penting')->firstOrFail();
        $this->assertCount(1, $document->getMedia(Document::FILE_COLLECTION));
    }

    public function test_store_validates_without_file(): void
    {
        $this->actingAsAdministrator();

        $this->post(route('admin.documents.store'), [
            'title' => 'Tanpa File',
        ])->assertSessionHasErrors(['file']);
    }

    public function test_update_changes_title_without_new_file(): void
    {
        Storage::fake('public');
        $this->actingAsAdministrator();

        $document = Document::query()->create(['title' => 'Lama']);
        $document->addMedia($this->fakePdf())->toMediaCollection(Document::FILE_COLLECTION);

        $this->put(route('admin.documents.update', $document), [
            'title' => 'Baru',
        ])->assertRedirect(route('admin.documents.index'));

        $document->refresh();
        $this->assertSame('Baru', $document->title);
        $this->assertCount(1, $document->getMedia(Document::FILE_COLLECTION));
    }

    public function test_update_replaces_file_in_single_file_collection(): void
    {
        Storage::fake('public');
        $this->actingAsAdministrator();

        $document = Document::query()->create(['title' => 'Ganti Berkas']);
        $document->addMedia(
            UploadedFile::fake()->create('old.pdf', 100, 'application/pdf'),
        )->toMediaCollection(Document::FILE_COLLECTION);
        $oldId = $document->getFirstMedia(Document::FILE_COLLECTION)?->id;
        $this->assertNotNull($oldId);

        $new = UploadedFile::fake()->create('new.pdf', 150, 'application/pdf');
        $this->put(route('admin.documents.update', $document), [
            'title' => 'Ganti Berkas',
            'file' => $new,
        ])->assertRedirect(route('admin.documents.index'));

        $document->refresh();
        $this->assertCount(1, $document->getMedia(Document::FILE_COLLECTION));
        $this->assertNotSame(
            $oldId,
            $document->getFirstMedia(Document::FILE_COLLECTION)?->id,
        );
    }

    public function test_destroy_soft_deletes_document(): void
    {
        $this->actingAsAdministrator();

        $document = Document::query()->create(['title' => 'Untuk Dihapus']);

        $this->delete(route('admin.documents.destroy', $document))
            ->assertRedirect(route('admin.documents.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('documents', ['id' => $document->id]);
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Models\Program;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProgramCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    private function actingAsUser(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('Administrator');
        $this->actingAs($user);
    }

    private function makeProgram(array $overrides = []): Program
    {
        return Program::query()->create(array_merge([
            'title' => 'Program Awal',
            'slug' => 'program-awal',
            'excerpt' => 'Ringkasan',
            'is_active' => true,
            'sort_order' => 0,
        ], $overrides));
    }

    public function test_guest_is_redirected_from_programs_index_to_admin_login(): void
    {
        $this->get(route('admin.programs.index'))
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_guest_cannot_post_to_store_programs(): void
    {
        $this->post(route('admin.programs.store'), [])
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_index_forbidden_without_programs_view_permission(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('admin.programs.index'))->assertForbidden();
    }

    public function test_index_shows_programs_and_filters_by_title(): void
    {
        $this->actingAsUser();

        $this->makeProgram(['title' => 'alpha-unik', 'slug' => 'alpha-unik']);
        $this->makeProgram(['title' => 'lainnya', 'slug' => 'lainnya']);

        $this->get(route('admin.programs.index'))->assertOk()
            ->assertViewHas('programs', fn ($paginator) => $paginator->total() === 2);

        $r = $this->get(route('admin.programs.index', ['q' => 'alpha']))->assertOk();
        $this->assertSame(1, $r->viewData('programs')->total());
    }

    public function test_store_creates_program_with_goals_and_images(): void
    {
        Storage::fake('public');
        $this->actingAsUser();

        $this->post(route('admin.programs.store'), [
            'title' => 'Sekolah Moderasi',
            'excerpt' => 'Program pendidikan dan kajian interaktif.',
            'about_heading' => 'Apa itu Sekolah Moderasi?',
            'about_content' => '<p>Penjelasan program.</p>',
            'is_active' => '1',
            'sort_order' => '3',
            'goals' => ['Memahami moderasi', 'Berdialog konstruktif', ''],
            'cover_photo' => UploadedFile::fake()->image('cover.jpg', 800, 600),
            'gallery' => [
                UploadedFile::fake()->image('g1.jpg'),
                UploadedFile::fake()->image('g2.jpg'),
            ],
        ])->assertRedirect(route('admin.programs.index'))
            ->assertSessionHas('success', 'Program berhasil ditambahkan.');

        $this->assertDatabaseHas('programs', [
            'title' => 'Sekolah Moderasi',
            'slug' => 'sekolah-moderasi',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $program = Program::query()->where('slug', 'sekolah-moderasi')->firstOrFail();

        // Tujuan kosong di-skip, urutan dari posisi array.
        $this->assertCount(2, $program->goals);
        $this->assertDatabaseHas('program_goals', [
            'program_id' => $program->id,
            'content' => 'Memahami moderasi',
            'sort_order' => 0,
        ]);
        $this->assertDatabaseHas('program_goals', [
            'program_id' => $program->id,
            'content' => 'Berdialog konstruktif',
            'sort_order' => 1,
        ]);

        $this->assertTrue($program->hasMedia(Program::COVER_COLLECTION));
        $this->assertCount(2, $program->getMedia(Program::GALLERY_COLLECTION));
    }

    public function test_store_validation_requires_title_and_excerpt(): void
    {
        $this->actingAsUser();

        $this->from(route('admin.programs.create'))
            ->post(route('admin.programs.store'), [
                'title' => '',
                'excerpt' => '',
            ])
            ->assertSessionHasErrors(['title', 'excerpt']);
    }

    public function test_update_syncs_goals_replacing_old(): void
    {
        $this->actingAsUser();

        $program = $this->makeProgram(['title' => 'Lama', 'slug' => 'lama']);
        $program->goals()->createMany([
            ['content' => 'Tujuan Satu', 'sort_order' => 0],
            ['content' => 'Tujuan Dua', 'sort_order' => 1],
        ]);

        $this->put(route('admin.programs.update', $program), [
            'title' => 'Lama',
            'excerpt' => 'Ringkasan baru',
            'is_active' => '1',
            'goals' => ['Hanya Satu'],
        ])->assertRedirect(route('admin.programs.index'))
            ->assertSessionHas('success', 'Program berhasil diperbarui.');

        $program->refresh();
        $this->assertCount(1, $program->goals);
        $this->assertDatabaseHas('program_goals', [
            'program_id' => $program->id,
            'content' => 'Hanya Satu',
            'sort_order' => 0,
        ]);
        $this->assertDatabaseMissing('program_goals', [
            'program_id' => $program->id,
            'content' => 'Tujuan Satu',
        ]);
    }

    public function test_update_toggles_is_active_off_when_unchecked(): void
    {
        $this->actingAsUser();

        $program = $this->makeProgram(['is_active' => true]);

        $this->put(route('admin.programs.update', $program), [
            'title' => $program->title,
            'excerpt' => $program->excerpt,
            // is_active sengaja tidak dikirim (checkbox tidak dicentang)
        ])->assertRedirect(route('admin.programs.index'));

        $this->assertDatabaseHas('programs', [
            'id' => $program->id,
            'is_active' => false,
        ]);
    }

    public function test_destroy_gallery_media_only_deletes_owned(): void
    {
        Storage::fake('public');
        $this->actingAsUser();

        $programA = $this->makeProgram(['title' => 'A', 'slug' => 'a']);
        $programB = $this->makeProgram(['title' => 'B', 'slug' => 'b']);

        $programA->addMedia(UploadedFile::fake()->image('a.jpg'))->toMediaCollection(Program::GALLERY_COLLECTION);
        $mediaB = $programB->addMedia(UploadedFile::fake()->image('b.jpg'))->toMediaCollection(Program::GALLERY_COLLECTION);

        // Media milik B tidak boleh dihapus lewat program A.
        $this->delete(route('admin.programs.media.destroy', [$programA, $mediaB]))->assertNotFound();
        $this->assertDatabaseHas('media', ['id' => $mediaB->id]);

        // Hapus media milik sendiri berhasil.
        $this->delete(route('admin.programs.media.destroy', [$programB, $mediaB]))
            ->assertRedirect(route('admin.programs.edit', $programB))
            ->assertSessionHas('success', 'Gambar galeri berhasil dihapus.');
        $this->assertDatabaseMissing('media', ['id' => $mediaB->id]);
    }

    public function test_destroy_soft_deletes_program_and_clears_media(): void
    {
        Storage::fake('public');
        $this->actingAsUser();

        $program = $this->makeProgram();
        $program->addMedia(UploadedFile::fake()->image('cover.jpg'))->toMediaCollection(Program::COVER_COLLECTION);
        $gallery = $program->addMedia(UploadedFile::fake()->image('g.jpg'))->toMediaCollection(Program::GALLERY_COLLECTION);
        $relative = $gallery->getPathRelativeToRoot();

        $this->delete(route('admin.programs.destroy', $program))
            ->assertRedirect(route('admin.programs.index'))
            ->assertSessionHas('success', 'Program berhasil dihapus.');

        $this->assertSoftDeleted('programs', ['id' => $program->id]);
        $this->assertFalse(Storage::disk('public')->exists($relative));
        $this->assertDatabaseMissing('media', ['id' => $gallery->id]);
    }

    public function test_front_show_displays_active_program_and_hides_inactive(): void
    {
        $active = $this->makeProgram(['title' => 'Tampil', 'slug' => 'tampil', 'is_active' => true]);
        $hidden = $this->makeProgram(['title' => 'Sembunyi', 'slug' => 'sembunyi', 'is_active' => false]);

        $this->get(route('program-unggulan.show', $active->slug))->assertOk()
            ->assertSee('Tampil');

        $this->get(route('program-unggulan.show', $hidden->slug))->assertNotFound();
    }
}

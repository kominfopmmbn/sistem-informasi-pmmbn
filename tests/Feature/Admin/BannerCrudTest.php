<?php

namespace Tests\Feature\Admin;

use App\Models\Banner;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BannerCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
        Storage::fake('public');
    }

    private function actingAsUser(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('Administrator');
        $this->actingAs($user);
    }

    private function makeBanner(array $overrides = []): Banner
    {
        return Banner::query()->create(array_merge([
            'title' => 'Banner Awal',
            'slug' => 'banner-awal',
            'link_url' => 'https://forms.gle/abc',
            'is_active' => true,
            'sort_order' => 0,
        ], $overrides));
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Pendaftaran Anggota 2026',
            'slug' => '',
            'link_url' => 'https://forms.gle/xyz',
            'is_active' => '1',
            'sort_order' => '2',
            'image' => UploadedFile::fake()->image('banner.jpg', 800, 1000),
        ], $overrides);
    }

    public function test_guest_is_redirected_from_banners_index_to_admin_login(): void
    {
        $this->get(route('admin.banners.index'))
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_index_forbidden_without_banners_view_permission(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('admin.banners.index'))->assertForbidden();
    }

    public function test_index_lists_banners_with_share_url(): void
    {
        $this->actingAsUser();
        $banner = $this->makeBanner();

        $this->get(route('admin.banners.index'))
            ->assertOk()
            ->assertViewHas('banners', fn ($paginator) => $paginator->total() === 1)
            ->assertSee($banner->shareUrl(), false);
    }

    public function test_create_and_edit_forms_render(): void
    {
        $this->actingAsUser();
        $banner = $this->makeBanner();
        $banner->addMedia(UploadedFile::fake()->image('a.jpg'))->toMediaCollection(Banner::IMAGE_COLLECTION);

        $this->get(route('admin.banners.create'))->assertOk()
            ->assertSee('id="banner-image-dropzone"', false)
            ->assertSee('name="image"', false)
            ->assertSee('form="banner-form"', false)
            ->assertSee('data-share-base="'.route('home.index').'"', false)
            ->assertDontSee('Lihat di situs');

        // Edit: gambar tersimpan dipasang ke dropzone, link share siap salin, tombol buka situs.
        $this->get(route('admin.banners.edit', $banner))->assertOk()
            ->assertSee('data-existing-url="'.$banner->getFirstMediaUrl(Banner::IMAGE_COLLECTION).'"', false)
            ->assertSee('data-copy-target="banner-share-url"', false)
            ->assertSee($banner->shareUrl(), false)
            ->assertSee('Lihat di situs');
    }

    public function test_store_creates_banner_with_image_and_slug_from_title(): void
    {
        $this->actingAsUser();

        $this->post(route('admin.banners.store'), $this->validPayload())
            ->assertRedirect(route('admin.banners.index'))
            ->assertSessionHas('success', 'Banner berhasil ditambahkan.');

        $banner = Banner::query()->where('slug', 'pendaftaran-anggota-2026')->firstOrFail();
        $this->assertTrue($banner->is_active);
        $this->assertSame(2, $banner->sort_order);
        $this->assertSame('https://forms.gle/xyz', $banner->link_url);
        $this->assertTrue($banner->hasMedia(Banner::IMAGE_COLLECTION));
    }

    public function test_store_normalizes_custom_slug(): void
    {
        $this->actingAsUser();

        $this->post(route('admin.banners.store'), $this->validPayload(['slug' => 'Daftar 2026']))
            ->assertRedirect(route('admin.banners.index'));

        $this->assertDatabaseHas('banners', ['slug' => 'daftar-2026']);
    }

    public function test_store_auto_slug_gets_suffix_when_taken(): void
    {
        $this->actingAsUser();
        $this->makeBanner(['title' => 'Pendaftaran Anggota 2026', 'slug' => 'pendaftaran-anggota-2026']);

        $this->post(route('admin.banners.store'), $this->validPayload())
            ->assertRedirect(route('admin.banners.index'));

        $this->assertDatabaseHas('banners', ['slug' => 'pendaftaran-anggota-2026-2']);
    }

    public function test_store_validation_requires_title_and_image(): void
    {
        $this->actingAsUser();

        $this->from(route('admin.banners.create'))
            ->post(route('admin.banners.store'), $this->validPayload(['title' => '', 'image' => null]))
            ->assertSessionHasErrors(['title', 'image']);
    }

    public function test_store_rejects_non_http_link_url(): void
    {
        $this->actingAsUser();

        $this->from(route('admin.banners.create'))
            ->post(route('admin.banners.store'), $this->validPayload(['link_url' => 'javascript:alert(1)']))
            ->assertSessionHasErrors(['link_url']);

        $this->from(route('admin.banners.create'))
            ->post(route('admin.banners.store'), $this->validPayload(['link_url' => 'bukan url']))
            ->assertSessionHasErrors(['link_url']);
    }

    public function test_store_rejects_slug_used_by_soft_deleted_banner(): void
    {
        $this->actingAsUser();
        $this->makeBanner(['slug' => 'promo'])->delete();

        $this->from(route('admin.banners.create'))
            ->post(route('admin.banners.store'), $this->validPayload(['slug' => 'promo']))
            ->assertSessionHasErrors(['slug']);
    }

    public function test_update_without_new_image_keeps_image_and_allows_own_slug(): void
    {
        $this->actingAsUser();
        $banner = $this->makeBanner();
        $banner->addMedia(UploadedFile::fake()->image('a.jpg'))->toMediaCollection(Banner::IMAGE_COLLECTION);

        $this->put(route('admin.banners.update', $banner), [
            'title' => 'Judul Baru',
            'slug' => 'banner-awal',
            'link_url' => '',
            'sort_order' => '5',
        ])->assertRedirect(route('admin.banners.index'))
            ->assertSessionHas('success', 'Banner berhasil diperbarui.');

        $banner->refresh();
        $this->assertSame('Judul Baru', $banner->title);
        $this->assertSame('banner-awal', $banner->slug);
        $this->assertNull($banner->link_url);
        $this->assertFalse($banner->is_active);
        $this->assertSame(5, $banner->sort_order);
        $this->assertTrue($banner->hasMedia(Banner::IMAGE_COLLECTION));
    }

    public function test_destroy_soft_deletes_banner(): void
    {
        $this->actingAsUser();
        $banner = $this->makeBanner();

        $this->delete(route('admin.banners.destroy', $banner))
            ->assertRedirect(route('admin.banners.index'))
            ->assertSessionHas('success', 'Banner berhasil dihapus.');

        $this->assertSoftDeleted('banners', ['id' => $banner->id]);
    }
}

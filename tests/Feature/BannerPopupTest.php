<?php

namespace Tests\Feature;

use App\Models\Banner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerPopupTest extends TestCase
{
    use RefreshDatabase;

    private function makeBanner(array $overrides = []): Banner
    {
        return Banner::query()->create(array_merge([
            'title' => 'Banner',
            'slug' => 'banner',
            'link_url' => 'https://forms.gle/abc',
            'is_active' => true,
            'sort_order' => 0,
        ], $overrides));
    }

    public function test_no_popup_without_active_banner(): void
    {
        $this->makeBanner(['is_active' => false]);
        $this->makeBanner(['slug' => 'terhapus'])->delete();

        $this->get(route('home.index'))
            ->assertOk()
            ->assertDontSee('id="bannerPopup"', false);
    }

    public function test_active_banner_shows_in_popup_with_link(): void
    {
        $this->makeBanner(['title' => 'Pendaftaran Dibuka']);

        $this->get(route('home.index'))
            ->assertOk()
            ->assertSee('id="bannerPopup"', false)
            ->assertSee('data-forced="0"', false)
            ->assertSee('alt="Pendaftaran Dibuka"', false)
            ->assertSee('href="https://forms.gle/abc"', false)
            ->assertSee('Daftar Sekarang');
    }

    public function test_popup_button_uses_global_brand_style(): void
    {
        // `.btn-custom` hanya ber-style di dalam `.page-card-member`; popup tampil di semua halaman.
        $this->makeBanner();

        $html = $this->get(route('home.index'))->assertOk()->getContent();
        $popup = substr($html, strpos($html, 'id="bannerPopup"'));

        $this->assertStringContainsString('class="btn btn-brand"', $popup);
        $this->assertStringNotContainsString('btn-custom', $popup);
    }

    public function test_seen_flag_is_set_when_popup_opens_not_on_close(): void
    {
        // Klik link internal memicu navigasi tanpa menutup modal; flag harus sudah tersimpan saat tampil.
        $this->makeBanner();

        $this->get(route('home.index'))
            ->assertSee("shown.bs.modal", false)
            ->assertDontSee("hidden.bs.modal", false);
    }

    public function test_banner_without_link_has_no_button(): void
    {
        $this->makeBanner(['link_url' => null]);

        $this->get(route('home.index'))
            ->assertOk()
            ->assertSee('id="bannerPopup"', false)
            ->assertDontSee('Daftar Sekarang');
    }

    public function test_popup_renders_on_other_public_pages(): void
    {
        $this->makeBanner();

        $this->get(route('download.index'))
            ->assertOk()
            ->assertSee('id="bannerPopup"', false);
    }

    public function test_slides_follow_sort_order(): void
    {
        $this->makeBanner(['slug' => 'kedua', 'sort_order' => 2]);
        $this->makeBanner(['slug' => 'pertama', 'sort_order' => 1]);

        $this->get(route('home.index'))
            ->assertSeeInOrder(['data-banner-slug="pertama"', 'data-banner-slug="kedua"'], false);
    }

    public function test_multiple_banners_render_navigation_below_slides(): void
    {
        // Panah & indikator bawaan Bootstrap berupa overlay absolut yang menutupi gambar lebar dan tombol
        // "Daftar Sekarang"; harus mengalir dalam satu baris di bawah slide.
        $this->makeBanner(['slug' => 'pertama']);
        $this->makeBanner(['slug' => 'kedua']);

        $this->get(route('home.index'))
            ->assertSeeInOrder([
                'class="carousel-inner"',
                'class="carousel-control-prev position-static',
                'class="carousel-indicators position-static',
                'class="carousel-control-next position-static',
            ], false);
    }

    public function test_banner_param_moves_banner_first_and_forces_popup(): void
    {
        $this->makeBanner(['slug' => 'pertama', 'sort_order' => 1]);
        $this->makeBanner(['slug' => 'promo-ig', 'sort_order' => 9]);

        $this->get(route('home.index', ['banner' => 'promo-ig']))
            ->assertOk()
            ->assertSee('data-forced="1"', false)
            ->assertSeeInOrder(['data-banner-slug="promo-ig"', 'data-banner-slug="pertama"'], false);
    }

    public function test_unknown_or_inactive_banner_param_is_ignored(): void
    {
        $this->makeBanner(['slug' => 'pertama', 'sort_order' => 1]);
        $this->makeBanner(['slug' => 'nonaktif', 'is_active' => false]);

        $this->get(route('home.index', ['banner' => 'nonaktif']))
            ->assertOk()
            ->assertSee('data-forced="0"', false)
            ->assertDontSee('data-banner-slug="nonaktif"', false);

        $this->get(route('home.index', ['banner' => 'tidak-ada']))
            ->assertOk()
            ->assertSee('data-forced="0"', false);
    }

    public function test_array_banner_param_does_not_break_page(): void
    {
        $this->makeBanner(['slug' => 'pertama']);

        $this->get('/?banner[]=pertama')
            ->assertOk()
            ->assertSee('data-forced="0"', false);
    }
}

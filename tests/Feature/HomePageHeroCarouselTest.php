<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageHeroCarouselTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_hero_carousel(): void
    {
        $response = $this->get(route('home.index'));

        $response->assertOk();
        $response->assertSee('id="heroCarousel"', false);
        $response->assertSee('hero-slide-bg', false);
    }
}

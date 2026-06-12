<?php

namespace Tests\Feature;

use App\Models\Program;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeProgramSectionTest extends TestCase
{
    use RefreshDatabase;

    private function makeProgram(array $overrides = []): Program
    {
        return Program::query()->create(array_merge([
            'title' => 'Program Awal',
            'slug' => 'program-awal',
            'excerpt' => 'Ringkasan program.',
            'is_active' => true,
            'sort_order' => 0,
        ], $overrides));
    }

    public function test_home_page_shows_active_program(): void
    {
        $this->makeProgram(['title' => 'Sekolah Moderasi DB', 'slug' => 'sekolah-moderasi-db']);

        $this->get(route('home.index'))
            ->assertOk()
            ->assertSeeText('Sekolah Moderasi DB');
    }

    public function test_home_page_hides_inactive_program(): void
    {
        $this->makeProgram([
            'title' => 'Program Nonaktif DB',
            'slug' => 'program-nonaktif-db',
            'is_active' => false,
        ]);

        $this->get(route('home.index'))
            ->assertOk()
            ->assertDontSeeText('Program Nonaktif DB');
    }
}

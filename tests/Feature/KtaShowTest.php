<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\RegionalLeader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KtaShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_renders_member_name_number_and_regional_leader(): void
    {
        $regionalLeader = RegionalLeader::query()->create(['code' => '35', 'name' => 'Jawa Timur']);
        $member = Member::query()->create([
            'full_name' => 'Derida Achmad Bil Haq',
            'regional_leader_id' => $regionalLeader->id,
        ]);
        $kta = $member->kta()->create(['member_id' => $member->id]);

        $response = $this->get(route('kta.show', ['ktaNumber' => $kta->number]).'?type=view');

        $response->assertOk();
        $response->assertSee('Derida Achmad Bil Haq');
        $response->assertSee('Jawa Timur');
        $response->assertSee($kta->number);
    }

    public function test_view_omits_region_line_when_member_has_no_regional_leader_or_village(): void
    {
        $member = Member::query()->create(['full_name' => 'Tanpa Wilayah']);
        $kta = $member->kta()->create(['member_id' => $member->id]);

        $response = $this->get(route('kta.show', ['ktaNumber' => $kta->number]).'?type=view');

        $response->assertOk();
        $response->assertSee('Tanpa Wilayah');
        $response->assertDontSee('<div class="member-region">', false);
    }

    public function test_web_view_normalizes_line_height_for_browser(): void
    {
        $member = Member::query()->create(['full_name' => 'Tampilan Web']);
        $kta = $member->kta()->create(['member_id' => $member->id]);

        $this->get(route('kta.show', ['ktaNumber' => $kta->number]).'?type=view')
            ->assertOk()
            ->assertViewHas('isWebView', true)
            ->assertSee('.member-name, .member-region { line-height: 1.1; }', false);
    }

    public function test_unknown_number_returns_404(): void
    {
        $this->get(route('kta.show', ['ktaNumber' => '9999999999']))->assertNotFound();
    }
}

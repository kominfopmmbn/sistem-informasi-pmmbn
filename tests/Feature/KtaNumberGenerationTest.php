<?php

namespace Tests\Feature;

use App\Models\Kta;
use App\Models\Member;
use App\Models\RegionalLeader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KtaNumberGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_number_uses_regional_leader_code_as_prefix(): void
    {
        $regionalLeader = RegionalLeader::query()->create(['code' => '01', 'name' => 'PW Satu']);
        $member = Member::query()->create([
            'full_name' => 'Anggota Satu',
            'regional_leader_id' => $regionalLeader->id,
        ]);

        $kta = $member->kta()->create(['member_id' => $member->id]);

        $this->assertSame('01' . date('y') . '0001', $kta->number);
    }

    public function test_number_falls_back_to_00_when_member_has_no_regional_leader(): void
    {
        $member = Member::query()->create(['full_name' => 'Tanpa Wilayah']);

        $kta = $member->kta()->create(['member_id' => $member->id]);

        $this->assertSame('00' . date('y') . '0001', $kta->number);
    }
}

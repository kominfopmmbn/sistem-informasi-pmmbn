<?php

namespace Tests\Feature\Admin;

use App\Enums\MemberActivationStatus;
use App\Models\College;
use App\Models\District;
use App\Models\Member;
use App\Models\MemberActivation;
use App\Models\User;
use App\Models\Village;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;
use Tests\TestCase;

class MemberActivationAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
        $this->seed(ProvincesSeeder::class);
        $this->seed(CitiesSeeder::class);
    }

    /** Kecamatan + desa minimal (kode pos di kolom `meta` JSON) untuk memenuhi `exists:villages,code`. */
    private function sampleVillage(City $city): Village
    {
        $district = District::query()->firstOrCreate(
            ['code' => $city->code.'001'],
            ['city_code' => $city->code, 'name' => 'KECAMATAN TES'],
        );

        return Village::query()->firstOrCreate(
            ['code' => $district->code.'001'],
            [
                'district_code' => $district->code,
                'name' => 'DESA TES',
                'meta' => ['lat' => '-6.2', 'long' => '106.8', 'pos' => '40123'],
            ],
        );
    }

    public function test_edit_page_renders_for_admin(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('Administrator');
        $this->actingAs($user);

        $province = Province::query()->orderBy('id')->firstOrFail();
        $city = City::query()
            ->where('province_code', $province->code)
            ->orderBy('id')
            ->firstOrFail();

        $college = College::query()->create([
            'name' => 'Universitas Aktivasi Tes',
            'province_code' => $province->code,
            'city_code' => $city->code,
            'lat' => -6.3612,
            'long' => 106.8268,
        ]);

        $activation = MemberActivation::withoutEvents(
            fn () => MemberActivation::query()->create([
                'email' => 'member-activation-test@example.test',
                'college_id' => $college->id,
            ])
        );

        $this->get(route('admin.member-activations.edit', ['member_activation' => $activation]))
            ->assertOk()
            ->assertSee('Universitas Aktivasi Tes', false);
    }

    public function test_accept_copies_address_to_new_member(): void
    {
        Notification::fake();

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('Administrator');
        $this->actingAs($user);

        $province = Province::query()->orderBy('id')->firstOrFail();
        $city = City::query()
            ->where('province_code', $province->code)
            ->orderBy('id')
            ->firstOrFail();

        $college = College::query()->create([
            'name' => 'Universitas Aktivasi Tes',
            'province_code' => $province->code,
            'city_code' => $city->code,
            'lat' => -6.3612,
            'long' => 106.8268,
        ]);

        $activation = MemberActivation::withoutEvents(
            fn () => MemberActivation::query()->create([
                'full_name' => 'Calon Anggota',
                'email' => 'calon@example.test',
                'address' => 'Jl. Kenanga No. 3, Surabaya',
                'college_id' => $college->id,
            ])
        );

        $this->patch(route('admin.member-activations.accept', ['member_activation' => $activation]))
            ->assertRedirect(route('admin.member-activations.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('members', [
            'member_activation_id' => $activation->id,
            'email' => 'calon@example.test',
            'address' => 'Jl. Kenanga No. 3, Surabaya',
        ]);
    }

    public function test_accept_copies_village_code_to_new_member(): void
    {
        Notification::fake();

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('Administrator');
        $this->actingAs($user);

        $province = Province::query()->orderBy('id')->firstOrFail();
        $city = City::query()
            ->where('province_code', $province->code)
            ->orderBy('id')
            ->firstOrFail();
        $village = $this->sampleVillage($city);

        $activation = MemberActivation::withoutEvents(
            fn () => MemberActivation::query()->create([
                'full_name' => 'Calon Desa',
                'email' => 'calon-desa@example.test',
                'village_code' => $village->code,
            ])
        );

        $this->patch(route('admin.member-activations.accept', ['member_activation' => $activation]))
            ->assertRedirect(route('admin.member-activations.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('members', [
            'member_activation_id' => $activation->id,
            'email' => 'calon-desa@example.test',
            'village_code' => $village->code,
        ]);
    }

    public function test_index_filters_by_current_status(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('Administrator');
        $this->actingAs($user);

        // A: status terakhir PENDING.
        $pending = MemberActivation::withoutEvents(
            fn () => MemberActivation::query()->create([
                'full_name' => 'Pemohon Pending Satu',
                'email' => 'pending-satu@example.test',
            ])
        );
        $pending->memberActivationStatusLogs()->create([
            'status_id' => MemberActivationStatus::PENDING->value,
        ]);

        // B: pernah PENDING lalu DITOLAK → status terakhir REJECTED.
        $rejected = MemberActivation::withoutEvents(
            fn () => MemberActivation::query()->create([
                'full_name' => 'Pemohon Tolak Dua',
                'email' => 'tolak-dua@example.test',
            ])
        );
        $rejected->memberActivationStatusLogs()->create([
            'status_id' => MemberActivationStatus::PENDING->value,
        ]);
        $rejected->memberActivationStatusLogs()->create([
            'status_id' => MemberActivationStatus::REJECTED->value,
        ]);

        // Filter PENDING: hanya A (B punya riwayat PENDING tapi status terakhirnya REJECTED).
        $this->get(route('admin.member-activations.index', ['status' => MemberActivationStatus::PENDING->value]))
            ->assertOk()
            ->assertSee('Pemohon Pending Satu', false)
            ->assertDontSee('Pemohon Tolak Dua', false);

        // Filter REJECTED: hanya B.
        $this->get(route('admin.member-activations.index', ['status' => MemberActivationStatus::REJECTED->value]))
            ->assertOk()
            ->assertSee('Pemohon Tolak Dua', false)
            ->assertDontSee('Pemohon Pending Satu', false);
    }
}

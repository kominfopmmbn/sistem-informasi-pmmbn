<?php

namespace Tests\Feature\Admin;

use App\Models\College;
use App\Models\Member;
use App\Models\MemberActivation;
use App\Models\User;
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
}

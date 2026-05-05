<?php

namespace Tests\Feature\Admin;

use App\Models\MemberActivation;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    }

    public function test_edit_page_renders_for_admin(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('Administrator');
        $this->actingAs($user);

        $activation = MemberActivation::withoutEvents(
            fn () => MemberActivation::query()->create([
                'email' => 'member-activation-test@example.test',
            ])
        );

        $this->get(route('admin.member-activations.edit', ['member_activation' => $activation]))
            ->assertOk();
    }
}

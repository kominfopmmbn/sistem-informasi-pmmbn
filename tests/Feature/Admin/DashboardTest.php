<?php

namespace Tests\Feature\Admin;

use App\Enums\MemberActivationStatus;
use App\Models\Article;
use App\Models\Category;
use App\Models\Member;
use App\Models\MemberActivation;
use App\Models\MemberActivationStatusLog;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    private function actingAsAdministrator(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('Administrator');
        $this->actingAs($user);
    }

    private function makeActivation(MemberActivationStatus ...$statusFlow): MemberActivation
    {
        $activation = MemberActivation::create(['full_name' => 'Uji '.uniqid()]);

        foreach ($statusFlow as $status) {
            MemberActivationStatusLog::create([
                'member_activation_id' => $activation->id,
                'status_id' => $status->value,
            ]);
        }

        return $activation;
    }

    public function test_guest_is_redirected_from_dashboard_to_admin_login(): void
    {
        $this->get(route('admin.dashboard.index'))
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_administrator_sees_dashboard_with_correct_counts(): void
    {
        $this->actingAsAdministrator();

        Member::create(['full_name' => 'Anggota A']);
        Member::create(['full_name' => 'Anggota B']);

        // Dua aktivasi berhenti di PENDING → dihitung.
        $this->makeActivation(MemberActivationStatus::PENDING);
        $this->makeActivation(MemberActivationStatus::PENDING);
        // Satu aktivasi sudah lanjut ke VERIFIED → TIDAK dihitung sebagai pending.
        $this->makeActivation(MemberActivationStatus::PENDING, MemberActivationStatus::VERIFIED);

        $this->get(route('admin.dashboard.index'))
            ->assertOk()
            ->assertViewIs('admin.dashboard.index')
            ->assertViewHas('memberCount', 2)
            ->assertViewHas('pendingActivationCount', 2);
    }

    public function test_user_without_articles_other_only_counts_own_published_articles(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->givePermissionTo('articles.view');
        $this->actingAs($user);

        // Milik user login → dihitung (created_by di-set otomatis oleh userstamps).
        $this->makePublishedArticle();

        // Milik user lain → TIDAK dihitung.
        $other = User::factory()->create();
        $this->makePublishedArticle()->forceFill(['created_by' => $other->id])->save();

        $this->get(route('admin.dashboard.index'))
            ->assertOk()
            ->assertViewHas('publishedArticleCount', 1);
    }

    private function makePublishedArticle(): Article
    {
        $suffix = uniqid();
        $category = Category::firstOrCreate(['slug' => 'berita'], ['title' => 'Berita']);

        return Article::create([
            'category_id' => $category->id,
            'title' => 'Artikel '.$suffix,
            'slug' => 'artikel-'.$suffix,
            'content' => '<p>Isi</p>',
            'is_draft' => false,
            'published_at' => now()->subDay(),
        ]);
    }
}

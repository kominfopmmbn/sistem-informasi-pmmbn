<?php

namespace Tests\Feature\Admin;

use App\Enums\Gender;
use App\Models\Member;
use App\Models\OrgRegion;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MemberCrudTest extends TestCase
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

    private function actingAsAdministrator(): User
    {
        Role::firstOrCreate(
            ['name' => 'Administrator', 'guard_name' => 'web'],
        );

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('Administrator');
        $this->actingAs($user);

        return $user;
    }

    /** @return array{province: Province, city: City} */
    private function sampleProvinceAndCity(): array
    {
        $province = Province::query()->orderBy('id')->firstOrFail();
        $city = City::query()
            ->where('province_code', $province->code)
            ->orderBy('id')
            ->firstOrFail();

        return ['province' => $province, 'city' => $city];
    }

    public function test_guest_is_redirected_from_members_index_to_admin_login(): void
    {
        $this->get(route('admin.members.index'))
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_guest_cannot_post_to_store_members(): void
    {
        $this->post(route('admin.members.store'), [])
            ->assertRedirect(route('admin.auth.login'));
    }

    public function test_index_forbidden_without_members_view_permission(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('admin.members.index'))->assertForbidden();
    }

    public function test_index_shows_members(): void
    {
        $this->actingAsAdministrator();
        Member::query()->create([
            'nim' => '12345',
            'full_name' => 'Anggota Tes',
            'email' => 'anggota@example.test',
        ]);

        $this->get(route('admin.members.index'))->assertOk()
            ->assertSee('Anggota Tes', false)
            ->assertSee('12345', false);
    }

    public function test_create_renders(): void
    {
        $this->actingAsAdministrator();

        $this->get(route('admin.members.create'))->assertOk();
    }

    public function test_store_persists_member(): void
    {
        $this->actingAsAdministrator();
        ['province' => $province, 'city' => $city] = $this->sampleProvinceAndCity();
        $region = OrgRegion::query()->create([
            'name' => 'Wilayah Member',
            'code' => 'WM',
        ]);

        $this->post(route('admin.members.store'), [
            'nim' => 'NIM-001',
            'full_name' => 'Budi Tester',
            'nickname' => 'Budi',
            'email' => 'budi@example.test',
            'province_code' => $province->code,
            'place_of_birth_code' => $city->code,
            'date_of_birth' => '1999-05-03',
            'gender_id' => Gender::MALE->value,
            'org_region_id' => $region->id,
            'phone_number' => '081234567890',
        ])->assertRedirect(route('admin.members.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('members', [
            'nim' => 'NIM-001',
            'full_name' => 'Budi Tester',
            'email' => 'budi@example.test',
            'place_of_birth_code' => $city->code,
            'gender_id' => Gender::MALE->value,
            'org_region_id' => $region->id,
            'phone_number' => '081234567890',
        ]);
    }

    public function test_store_validates_invalid_payload(): void
    {
        $this->actingAsAdministrator();

        $this->post(route('admin.members.store'), [
            'email' => 'bukan-email',
        ])->assertSessionHasErrors(['email']);
    }

    public function test_store_requires_city_when_province_given(): void
    {
        $this->actingAsAdministrator();
        $province = Province::query()->orderBy('id')->firstOrFail();

        $this->post(route('admin.members.store'), [
            'full_name' => 'Tanpa Kota',
            'province_code' => $province->code,
        ])->assertSessionHasErrors(['place_of_birth_code']);
    }

    public function test_destroy_soft_deletes_member(): void
    {
        $this->actingAsAdministrator();
        $member = Member::query()->create([
            'full_name' => 'Dihapus',
        ]);

        $this->delete(route('admin.members.destroy', $member))
            ->assertRedirect(route('admin.members.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('members', ['id' => $member->id]);
    }

    public function test_store_attaches_multiple_supporting_documents(): void
    {
        Storage::fake(config('media-library.disk_name'));
        $this->actingAsAdministrator();

        $pdfA = UploadedFile::fake()->create('laporan.pdf', 120, 'application/pdf');
        $pdfB = UploadedFile::fake()->create('scan.pdf', 80, 'application/pdf');

        $this->post(route('admin.members.store'), [
            'full_name' => 'Pak Lampiran',
            'supporting_documents' => [$pdfA, $pdfB],
        ])->assertRedirect(route('admin.members.index'))
            ->assertSessionHas('success');

        $member = Member::query()->where('full_name', 'Pak Lampiran')->firstOrFail();
        $this->assertCount(2, $member->getMedia(Member::SUPPORTING_DOCUMENTS_COLLECTION));
    }

    public function test_store_rejects_invalid_supporting_document_type(): void
    {
        Storage::fake(config('media-library.disk_name'));
        $this->actingAsAdministrator();

        $bad = UploadedFile::fake()->create('virus.exe', 10);

        $this->post(route('admin.members.store'), [
            'full_name' => 'Tes Mime',
            'supporting_documents' => [$bad],
        ])->assertSessionHasErrors(['supporting_documents.0']);
    }

    public function test_destroy_supporting_media_removes_attachment(): void
    {
        Storage::fake(config('media-library.disk_name'));
        $this->actingAsAdministrator();

        $member = Member::query()->create([
            'full_name' => 'Ada Berkas',
        ]);
        $member->addMedia(UploadedFile::fake()->create('x.pdf', 50, 'application/pdf'))
            ->toMediaCollection(Member::SUPPORTING_DOCUMENTS_COLLECTION);
        $media = $member->getMedia(Member::SUPPORTING_DOCUMENTS_COLLECTION)->first();

        $this->delete(route('admin.members.supporting-media.destroy', [$member, $media]))
            ->assertRedirect(route('admin.members.edit', $member))
            ->assertSessionHas('success');

        $this->assertCount(0, $member->fresh()->getMedia(Member::SUPPORTING_DOCUMENTS_COLLECTION));
    }

    public function test_update_rejects_when_total_supporting_documents_exceeds_cap(): void
    {
        Storage::fake(config('media-library.disk_name'));
        $this->actingAsAdministrator();

        $member = Member::query()->create([
            'full_name' => 'Sudah Penuh',
        ]);

        for ($i = 0; $i < Member::SUPPORTING_DOCUMENTS_MAX_TOTAL; $i++) {
            $member->addMedia(UploadedFile::fake()->create("doc{$i}.pdf", 15, 'application/pdf'))
                ->toMediaCollection(Member::SUPPORTING_DOCUMENTS_COLLECTION);
        }

        $extra = UploadedFile::fake()->create('extra.pdf', 15, 'application/pdf');

        $this->from(route('admin.members.edit', $member))
            ->put(route('admin.members.update', $member), [
                'full_name' => 'Sudah Penuh',
                'supporting_documents' => [$extra],
            ])
            ->assertSessionHasErrors(['supporting_documents']);
    }
}

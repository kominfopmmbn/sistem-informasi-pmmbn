<?php

namespace Tests\Feature\Admin;

use App\Enums\Gender;
use App\Models\College;
use App\Models\District;
use App\Models\Kta;
use App\Models\Member;
use App\Models\RegionalLeader;
use App\Models\User;
use App\Models\Village;
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

    private function sampleCollege(): College
    {
        ['province' => $province, 'city' => $city] = $this->sampleProvinceAndCity();

        return College::query()->create([
            'name' => 'Universitas Tes Member',
            'province_code' => $province->code,
            'city_code' => $city->code,
            'lat' => -6.3612,
            'long' => 106.8268,
        ]);
    }

    /** Kecamatan + desa minimal (kode pos di kolom `meta` JSON) untuk memenuhi `exists:villages,code`. */
    private function sampleVillage(): Village
    {
        ['city' => $city] = $this->sampleProvinceAndCity();

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

    public function test_index_filters_by_verification_status(): void
    {
        $this->actingAsAdministrator();

        $withKta = Member::query()->create([
            'full_name' => 'Anggota Ber KTA',
            'email' => 'ber-kta@example.test',
        ]);
        Kta::query()->create(['member_id' => $withKta->id]);

        Member::query()->create([
            'full_name' => 'Anggota Tanpa KTA',
            'email' => 'tanpa-kta@example.test',
        ]);

        $this->get(route('admin.members.index', ['verification' => 'verified']))
            ->assertOk()
            ->assertSee('Anggota Ber KTA', false)
            ->assertDontSee('Anggota Tanpa KTA', false);

        $this->get(route('admin.members.index', ['verification' => 'unverified']))
            ->assertOk()
            ->assertSee('Anggota Tanpa KTA', false)
            ->assertDontSee('Anggota Ber KTA', false);
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
        $college = $this->sampleCollege();

        $this->post(route('admin.members.store'), [
            'nim' => 'NIM-001',
            'full_name' => 'Budi Tester',
            'email' => 'budi@example.test',
            'province_code' => $province->code,
            'place_of_birth_code' => $city->code,
            'date_of_birth' => '1999-05-03',
            'gender_id' => Gender::MALE->value,
            'phone_number' => '081234567890',
            'address' => 'Jl. Melati No. 2, Bandung',
            'college_id' => $college->id,
        ])->assertRedirect(route('admin.members.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('members', [
            'nim' => 'NIM-001',
            'full_name' => 'Budi Tester',
            'email' => 'budi@example.test',
            'place_of_birth_code' => $city->code,
            'gender_id' => Gender::MALE->value,
            'phone_number' => '081234567890',
            'address' => 'Jl. Melati No. 2, Bandung',
            'college_id' => $college->id,
        ]);
    }

    public function test_store_validates_invalid_payload(): void
    {
        $this->actingAsAdministrator();

        $this->post(route('admin.members.store'), [
            'email' => 'bukan-email',
        ])->assertSessionHasErrors(['email']);
    }

    public function test_store_persists_place_of_birth_without_province(): void
    {
        $this->actingAsAdministrator();
        ['city' => $city] = $this->sampleProvinceAndCity();

        $this->post(route('admin.members.store'), [
            'full_name' => 'Tanpa Provinsi',
            'place_of_birth_code' => $city->code,
        ])->assertRedirect(route('admin.members.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('members', [
            'full_name' => 'Tanpa Provinsi',
            'place_of_birth_code' => $city->code,
        ]);
    }

    public function test_store_rejects_unknown_place_of_birth_code(): void
    {
        $this->actingAsAdministrator();

        $this->post(route('admin.members.store'), [
            'full_name' => 'Kota Tak Dikenal',
            'place_of_birth_code' => '9999',
        ])->assertSessionHasErrors(['place_of_birth_code']);
    }

    public function test_store_persists_village_code(): void
    {
        $this->actingAsAdministrator();
        $village = $this->sampleVillage();

        $this->post(route('admin.members.store'), [
            'full_name' => 'Punya Desa',
            'village_code' => $village->code,
        ])->assertRedirect(route('admin.members.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('members', [
            'full_name' => 'Punya Desa',
            'village_code' => $village->code,
        ]);
    }

    public function test_store_rejects_unknown_village_code(): void
    {
        $this->actingAsAdministrator();

        $this->post(route('admin.members.store'), [
            'full_name' => 'Desa Tak Dikenal',
            'village_code' => '9999999999',
        ])->assertSessionHasErrors(['village_code']);
    }

    public function test_store_persists_regional_leader_id(): void
    {
        $this->actingAsAdministrator();
        $regionalLeader = RegionalLeader::query()->create([
            'code' => 'PW-01',
            'name' => 'Pimpinan Wilayah Tes',
        ]);

        $this->post(route('admin.members.store'), [
            'full_name' => 'Punya Pimpinan',
            'regional_leader_id' => $regionalLeader->id,
        ])->assertRedirect(route('admin.members.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('members', [
            'full_name' => 'Punya Pimpinan',
            'regional_leader_id' => $regionalLeader->id,
        ]);
    }

    public function test_store_rejects_unknown_regional_leader_id(): void
    {
        $this->actingAsAdministrator();

        $this->post(route('admin.members.store'), [
            'full_name' => 'Pimpinan Tak Dikenal',
            'regional_leader_id' => 999999,
        ])->assertSessionHasErrors(['regional_leader_id']);
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

    public function test_store_with_manual_kta_number_creates_verified_special_member(): void
    {
        $this->actingAsAdministrator();

        $this->post(route('admin.members.store'), [
            'full_name' => 'Anggota Khusus',
            'is_special' => 1,
            'kta_number' => 'KHUSUS-001',
        ])->assertRedirect(route('admin.members.index'));

        $member = Member::query()->where('full_name', 'Anggota Khusus')->firstOrFail();
        $this->assertSame('KHUSUS-001', $member->kta->number);
        $this->assertTrue($member->kta->is_manual);
        $this->assertSame(0, $member->kta->order_number);

        $this->get(route('admin.members.index', ['verification' => 'verified']))
            ->assertOk()
            ->assertSee('Anggota Khusus', false)
            ->assertSee('Khusus</span>', false);
    }

    public function test_store_ignores_kta_number_when_special_checkbox_unchecked(): void
    {
        $this->actingAsAdministrator();

        $this->post(route('admin.members.store'), [
            'full_name' => 'Anggota Biasa',
            'kta_number' => 'TIDAK-DIPAKAI',
        ])->assertRedirect(route('admin.members.index'));

        $this->assertNull(Member::query()->where('full_name', 'Anggota Biasa')->firstOrFail()->kta);
    }

    public function test_store_requires_kta_number_when_special_checkbox_checked(): void
    {
        $this->actingAsAdministrator();

        $this->post(route('admin.members.store'), [
            'full_name' => 'Anggota Khusus',
            'is_special' => 1,
            'kta_number' => '',
        ])->assertSessionHasErrors(['kta_number']);
    }

    public function test_store_rejects_duplicate_kta_number(): void
    {
        $this->actingAsAdministrator();
        $existing = Member::query()->create(['full_name' => 'Pemilik KTA']);
        Kta::query()->create(['member_id' => $existing->id, 'number' => 'DUP-1', 'is_manual' => true]);

        $this->post(route('admin.members.store'), [
            'full_name' => 'Peniru',
            'is_special' => 1,
            'kta_number' => 'DUP-1',
        ])->assertSessionHasErrors(['kta_number']);
    }

    public function test_update_member_without_kta_with_kta_number_creates_manual_kta(): void
    {
        $this->actingAsAdministrator();
        $member = Member::query()->create(['full_name' => 'Belum Ber KTA']);

        $this->put(route('admin.members.update', $member), [
            'full_name' => 'Belum Ber KTA',
            'is_special' => 1,
            'kta_number' => 'KHUSUS-002',
        ])->assertRedirect(route('admin.members.index'));

        $kta = $member->fresh()->kta;
        $this->assertSame('KHUSUS-002', $kta->number);
        $this->assertTrue($kta->is_manual);
    }

    public function test_update_special_member_can_change_kta_number(): void
    {
        $this->actingAsAdministrator();
        $member = Member::query()->create(['full_name' => 'Anggota Khusus']);
        Kta::query()->create(['member_id' => $member->id, 'number' => 'KHUSUS-003', 'is_manual' => true]);

        $this->put(route('admin.members.update', $member), [
            'full_name' => 'Anggota Khusus',
            'is_special' => 1,
            'kta_number' => 'KHUSUS-004',
        ])->assertRedirect(route('admin.members.index'));

        $this->assertSame('KHUSUS-004', $member->fresh()->kta->number);
    }

    public function test_update_cannot_change_auto_generated_kta_number(): void
    {
        $this->actingAsAdministrator();
        $member = Member::query()->create(['full_name' => 'Anggota Reguler']);
        $kta = Kta::query()->create(['member_id' => $member->id]);

        $this->get(route('admin.members.edit', $member))
            ->assertOk()
            ->assertSee('Nomor KTA otomatis tidak dapat diubah.', false);

        $this->put(route('admin.members.update', $member), [
            'full_name' => 'Anggota Reguler',
            'is_special' => 1,
            'kta_number' => 'COBA-UBAH',
        ])->assertRedirect(route('admin.members.index'));

        $fresh = $kta->fresh();
        $this->assertSame($kta->number, $fresh->number);
        $this->assertFalse($fresh->is_manual);
    }
}

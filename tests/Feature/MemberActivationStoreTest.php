<?php

namespace Tests\Feature;

use App\Enums\Gender;
use App\Enums\MemberActivationStatus;
use App\Models\College;
use App\Models\MemberActivation;
use App\Models\MemberActivationEmailOtpVerification;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;
use Tests\TestCase;

class MemberActivationStoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
        $this->seed(ProvincesSeeder::class);
        $this->seed(CitiesSeeder::class);
    }

    private function sampleCity(): City
    {
        $province = Province::query()->orderBy('id')->firstOrFail();

        return City::query()
            ->where('province_code', $province->code)
            ->orderBy('id')
            ->firstOrFail();
    }

    private function verifyEmail(string $email): void
    {
        MemberActivationEmailOtpVerification::query()->create([
            'email' => $email,
            'otp' => '123456',
            'verified_at' => now(),
        ]);
    }

    /** Payload publik yang valid; province_code sengaja tidak disertakan. */
    private function validPayload(City $city, array $overrides = []): array
    {
        $college = College::query()->create([
            'name' => 'Universitas Pendaftaran Tes',
            'province_code' => $city->province_code,
            'city_code' => $city->code,
            'lat' => -6.3612,
            'long' => 106.8268,
        ]);

        return array_merge([
            'nim' => 'NIM-PUB-1',
            'full_name' => 'Pendaftar Publik',
            'email' => 'pendaftar@example.test',
            'place_of_birth_code' => $city->code,
            'date_of_birth' => '2000-01-01',
            'gender_id' => Gender::MALE->value,
            'phone_number' => '081234567890',
            'address' => 'Jl. Mawar No. 1, Jakarta',
            'college_id' => $college->id,
        ], $overrides);
    }

    public function test_store_creates_pending_activation_without_province(): void
    {
        $city = $this->sampleCity();
        $this->verifyEmail('pendaftar@example.test');

        $this->post(route('about.member-activation.store'), $this->validPayload($city))
            ->assertRedirect(route('about.member-activation.index'))
            ->assertSessionHas('success');

        $activation = MemberActivation::query()
            ->where('email', 'pendaftar@example.test')
            ->firstOrFail();

        $this->assertSame($city->code, $activation->place_of_birth_code);
        $this->assertSame('Jl. Mawar No. 1, Jakarta', $activation->address);
        $this->assertDatabaseHas('member_activation_status_logs', [
            'member_activation_id' => $activation->id,
            'status_id' => MemberActivationStatus::PENDING->value,
        ]);
    }

    public function test_store_requires_address(): void
    {
        $city = $this->sampleCity();
        $this->verifyEmail('pendaftar@example.test');

        $payload = $this->validPayload($city);
        unset($payload['address']);

        $this->post(route('about.member-activation.store'), $payload)
            ->assertSessionHasErrors(['address']);
    }

    public function test_store_requires_place_of_birth_code(): void
    {
        $city = $this->sampleCity();
        $this->verifyEmail('pendaftar@example.test');

        $payload = $this->validPayload($city);
        unset($payload['place_of_birth_code']);

        $this->post(route('about.member-activation.store'), $payload)
            ->assertSessionHasErrors(['place_of_birth_code']);
    }

    public function test_store_requires_verified_email(): void
    {
        $city = $this->sampleCity();

        // Tanpa memverifikasi email terlebih dahulu.
        $this->post(route('about.member-activation.store'), $this->validPayload($city))
            ->assertSessionHasErrors(['email']);
    }
}

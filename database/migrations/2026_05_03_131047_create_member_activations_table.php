<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('member_activations', function (Blueprint $table) {
            $table->id();
            $table->string('nim')->nullable()->unique();
            $table->string('full_name')->nullable();
            $table->string('nickname')->nullable();
            $table->string('email')->unique()->nullable();
            $table->char('place_of_birth_code', 4)->nullable(); // relasi ke table cities
            $table->date('date_of_birth')->nullable();
            $table->smallInteger('gender_id')->nullable();
            $table->unsignedBigInteger('org_region_id')->nullable(); // relasi ke table org_regions
            $table->string('phone_number')->nullable();
            $table->unsignedBigInteger('member_activation_email_otp_verification_id')->nullable(); // relasi ke table member_activation_email_otp_verifications
            $table->timestamps();
            $table->softDeletes();
            $table->userstamps();
            $table->userstampSoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_activations');
    }
};

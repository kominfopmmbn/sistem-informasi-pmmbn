<?php

use App\Enums\MemberActivationStatus;
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
        Schema::create('member_activation_status_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_activation_id'); // relasi ke table member_activations
            $table->smallInteger('status_id')->default(MemberActivationStatus::PENDING->value);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->userstamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_activation_status_logs');
    }
};

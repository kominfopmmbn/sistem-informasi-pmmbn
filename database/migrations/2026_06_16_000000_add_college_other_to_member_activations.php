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
        Schema::table('member_activations', function (Blueprint $table) {
            // Nama perguruan tinggi yang diisi manual bila pendaftar memilih "Lainnya".
            $table->string('college_other')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_activations', function (Blueprint $table) {
            $table->dropColumn('college_other');
        });
    }
};

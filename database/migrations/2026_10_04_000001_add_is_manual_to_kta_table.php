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
        Schema::table('kta', function (Blueprint $table) {
            // true = nomor KTA diinput manual oleh admin (anggota khusus)
            $table->boolean('is_manual')->default(false)->after('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kta', function (Blueprint $table) {
            $table->dropColumn('is_manual');
        });
    }
};

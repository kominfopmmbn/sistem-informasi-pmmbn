<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id'); // relasi ke table programs
            $table->text('content');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->userstamps();

            $table->index(['program_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_goals');
    }
};

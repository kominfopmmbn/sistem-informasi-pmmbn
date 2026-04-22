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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->longText('content')->nullable();
            $table->string('cover_photo_path')->nullable();
            $table->unsignedBigInteger('views_count')->default(0);

            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('archived_at')->nullable();
            $table->userstamps();
            $table->userstampSoftDeletes();
            $table->foreignId('archived_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};

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
        Schema::create('comic_genre', function (Blueprint $table) {
            // Relasi Cascade: Jika komik dihapus, data pivot ikut terhapus
            $table->foreignUuid('comic_id')->constrained('comics')->cascadeOnDelete();
            $table->foreignUuid('genre_id')->constrained('genres')->cascadeOnDelete();
            $table->primary(['comic_id', 'genre_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comic_genre');
    }
};

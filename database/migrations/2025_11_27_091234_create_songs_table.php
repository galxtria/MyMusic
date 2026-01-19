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
        Schema::create('songs', function (Blueprint $table) {
            $table->id(); // ID Unik (1, 2, 3...)
            
            // --- TAMBAHKAN KOLOM INI ---
            $table->string('title');           // Judul Lagu
            $table->string('artist');          // Nama Artis
            $table->string('album_art');       // Path Gambar (/images/...)
            $table->string('file_path');       // Path MP3 (/music/...)
            $table->string('duration')->nullable(); // Durasi (Boleh kosong/nullable)
            // ---------------------------

            $table->timestamps(); // Created_at & Updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};

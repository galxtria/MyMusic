<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stub lagu online (YouTube) belum punya file lokal, jadi kolom
     * file/cover harus boleh NULL agar addToLibrary tidak 500.
     */
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->string('album_art')->nullable()->change();
            $table->string('file_path')->nullable()->change();
            $table->string('artist_image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->string('album_art')->nullable(false)->change();
            $table->string('file_path')->nullable(false)->change();
            $table->string('artist_image')->nullable(false)->change();
        });
    }
};

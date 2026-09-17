<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom untuk integrasi iTunes API.
     * Kolom lama (file_path, album_art) tetap dipertahankan untuk backward-compatibility.
     */
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->unsignedBigInteger('itunes_track_id')->nullable()->unique()->after('id');
            $table->string('preview_url')->nullable()->after('file_path');
            $table->string('artwork_url')->nullable()->after('album_art');
            $table->unsignedBigInteger('itunes_artist_id')->nullable()->after('artist');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn(['itunes_track_id', 'preview_url', 'artwork_url', 'itunes_artist_id']);
        });
    }
};

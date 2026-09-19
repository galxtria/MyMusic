<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Playlist sharing: deskripsi, publik/privat, token share
        Schema::table('playlists', function (Blueprint $table) {
            if (!Schema::hasColumn('playlists', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('playlists', 'is_public')) {
                $table->boolean('is_public')->default(false)->after('cover_path');
            }
            if (!Schema::hasColumn('playlists', 'share_token')) {
                $table->string('share_token', 32)->nullable()->unique()->after('is_public');
            }
        });

        // Profile user: avatar + bio
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'avatar_path')) {
                $table->string('avatar_path')->nullable()->after('email');
            }
        });

        // Follow artist (nama artis bebas, karena tidak ada tabel artists)
        if (!Schema::hasTable('artist_follows')) {
            Schema::create('artist_follows', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('artist_name', 255);
                $table->timestamps();
                $table->unique(['user_id', 'artist_name']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('artist_follows');
        Schema::table('playlists', function (Blueprint $table) {
            $table->dropColumn(['description', 'is_public', 'share_token']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_path']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Index untuk agregasi lagu populer per periode (harian/mingguan).
     */
    public function up(): void
    {
        Schema::table('song_histories', function (Blueprint $table) {
            $table->index(['song_id', 'played_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('song_histories', function (Blueprint $table) {
            $table->dropIndex(['song_id', 'played_at']);
        });
    }
};

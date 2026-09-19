<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'cover_path',
        'is_public',
        'share_token',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Relasi Balik ke User (Pemilik Playlist)
     * Satu playlist dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi Many-to-Many ke Song (Lagu-lagu dalam Playlist)
     * Satu playlist bisa memiliki banyak lagu, dan satu lagu bisa ada di banyak playlist.
     * Menggunakan tabel pivot: playlist_song
     */
    public function songs()
    {
        return $this->belongsToMany(Song::class, 'playlist_song', 'playlist_id', 'song_id');
    }
}
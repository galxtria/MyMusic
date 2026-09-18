<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'artist', 
        'genre', 
        'duration', 
        'album_art', 
        'file_path', 
        'artist_image', 
        'lyrics',
        'itunes_track_id',
        'preview_url',
        'artwork_url',
        'itunes_artist_id',
        'youtube_id',
        'play_count',
        'last_played_at',
    ];

    protected $casts = [
        'last_played_at' => 'datetime',
    ];

    /**
     * URL gambar cover.
     * Prioritas: artwork_url (iTunes) → album_art (lokal)
     */
    public function getCoverUrlAttribute(): string
    {
        return $this->artwork_url ?? $this->album_art ?? '';
    }

    public function isLikedBy($user)
    {
        if (!$user) return false;
        return \DB::table('likes')->where('user_id', $user->id)->where('song_id', $this->id)->exists();
    }
}
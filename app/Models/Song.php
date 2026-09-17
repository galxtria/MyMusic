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
    ];

    /**
     * URL audio yang bisa diputar.
     * Prioritas: youtube streaming (fetch API on demand) -> preview_url -> file_path
     * Note: Untuk youtube, stream URL akan digenerate dinamis via Controller. 
     * Di sini kita kembalikan route helper khusus.
     */
    public function getPlayableUrlAttribute(): string
    {
        if ($this->youtube_id) {
            return route('youtube.stream', ['id' => $this->youtube_id]);
        }
        return $this->preview_url ?? $this->file_path ?? '';
    }

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
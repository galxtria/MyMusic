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
        'lyrics'
    ];

    public function isLikedBy($user)
    {
        if (!$user) return false;
        return \DB::table('likes')->where('user_id', $user->id)->where('song_id', $this->id)->exists();
    }
}
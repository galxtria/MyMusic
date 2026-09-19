<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtistFollow extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'artist_name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

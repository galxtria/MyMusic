<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        // Top lagu user dari riwayat (30 hari terakhir, maks 10)
        $topSongIds = DB::table('song_histories')
            ->select('song_id', DB::raw('COUNT(*) as plays'))
            ->where('user_id', $user->id)
            ->where('played_at', '>=', now()->subDays(30))
            ->groupBy('song_id')
            ->orderByDesc('plays')
            ->limit(10)
            ->get();
        $topSongs = $topSongIds->isNotEmpty()
            ? Song::whereIn('id', $topSongIds->pluck('song_id'))->get()
                ->sortBy(fn($s) => array_search($s->id, $topSongIds->pluck('song_id')->all()))->values()
            : collect();
        $topSongPlays = $topSongIds->pluck('plays', 'song_id');

        // Top artist user dari riwayat
        $topArtists = DB::table('song_histories')
            ->join('songs', 'songs.id', '=', 'song_histories.song_id')
            ->select('songs.artist', DB::raw('COUNT(*) as plays'))
            ->where('song_histories.user_id', $user->id)
            ->where('song_histories.played_at', '>=', now()->subDays(30))
            ->groupBy('songs.artist')
            ->orderByDesc('plays')
            ->limit(5)
            ->get();

        // Riwayat terakhir (20)
        $recentRows = DB::table('song_histories')
            ->where('user_id', $user->id)
            ->orderByDesc('played_at')
            ->limit(20)
            ->get();
        $recentSongs = $recentRows->isNotEmpty()
            ? Song::whereIn('id', $recentRows->pluck('song_id')->unique())->get()->keyBy('id')
            : collect();

        $totalPlays = DB::table('song_histories')->where('user_id', $user->id)->count();
        // Estimasi menit: 3.5 menit per play bila durasi tak tersedia
        $minutes = (int) round($totalPlays * 3.5);
        $likesCount = $user->favoriteSongs()->count();
        $playlistCount = $user->playlists()->count();
        $followedArtists = $user->followedArtists()->pluck('artist_name')->values();

        return view('profile', compact(
            'topSongs', 'topSongPlays', 'topArtists', 'recentRows', 'recentSongs',
            'totalPlays', 'minutes', 'likesCount', 'playlistCount', 'followedArtists'
        ));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'remove_avatar' => 'nullable|boolean',
        ]);

        $user->name = $request->name;

        if ($request->boolean('remove_avatar') && $user->avatar_path) {
            Storage::disk('public')->delete(str_replace('storage/', '', $user->avatar_path));
            $user->avatar_path = null;
        } elseif ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete(str_replace('storage/', '', $user->avatar_path));
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_path = 'storage/' . $path;
        }

        $user->save();

        if ($request->expectsJson()) {
            return response()->json(['status' => 'ok', 'message' => 'Profile updated', 'name' => $user->name, 'avatar_url' => $user->avatar_url]);
        }
        return redirect()->route('profile')->with('success', 'Profile updated!');
    }

    public function clearHistory()
    {
        DB::table('song_histories')->where('user_id', auth()->id())->delete();
        return back()->with('success', 'Listening history cleared.');
    }
}

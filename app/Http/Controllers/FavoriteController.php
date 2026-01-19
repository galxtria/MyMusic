<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FavoriteController extends Controller
{
    public function index()
    {
        $songs = Auth::user()->favoriteSongs()->latest()->get();
        return view('favorites', compact('songs'));
    }

    public function toggle(Request $request, $songId)
    {
        $result = auth()->user()->favoriteSongs()->toggle($songId);
        $status = count($result['attached']) > 0 ? 'liked' : 'unliked';
        return response()->json(['status' => $status, 'message' => $status == 'liked' ? 'Added to favorites' : 'Removed from favorites']);
    }

    public function check($songId)
    {
        $isLiked = DB::table('likes')->where('user_id', auth()->id())->where('song_id', $songId)->exists();
        return response()->json(['is_favorite' => $isLiked]);
    }

    public function storePlaylist(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'cover' => 'nullable|image|max:2048']);
        $path = $request->hasFile('cover') ? $request->file('cover')->store('playlist_covers', 'public') : null;

        Playlist::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'cover_path' => $path ? 'storage/' . $path : 'images/default_playlist.jpg',
        ]);
        return redirect()->route('library')->with('success', 'Playlist created!');
    }

    public function editPlaylist(Playlist $playlist)
    {
        if ($playlist->user_id !== auth()->id()) return redirect()->route('library');
        return view('playlists.edit', compact('playlist'));
    }

    public function updatePlaylist(Request $request, Playlist $playlist)
    {
        if ($playlist->user_id !== auth()->id()) return abort(403);
        $request->validate(['name' => 'required|string|max:255', 'cover' => 'nullable|image|max:2048']);

        $playlist->name = $request->name;
        if ($request->hasFile('cover')) {
            if ($playlist->cover_path && !str_contains($playlist->cover_path, 'default')) {
                Storage::disk('public')->delete(str_replace('storage/', '', $playlist->cover_path));
            }
            $path = $request->file('cover')->store('playlist_covers', 'public');
            $playlist->cover_path = 'storage/' . $path;
        }
        $playlist->save();
        return redirect()->route('library')->with('success', 'Playlist updated!');
    }

    public function destroyPlaylist($id)
    {
        $playlist = Playlist::where('user_id', auth()->id())->findOrFail($id);
        if ($playlist->cover_path && !str_contains($playlist->cover_path, 'default')) {
            Storage::disk('public')->delete(str_replace('storage/', '', $playlist->cover_path));
        }
        $playlist->delete();
        return redirect()->route('library')->with('success', 'Playlist deleted!');
    }

    public function addSongToPlaylist(Request $request)
    {
        $playlist = Playlist::where('user_id', auth()->id())->findOrFail($request->playlist_id);
        $playlist->songs()->syncWithoutDetaching([$request->song_id]);
        return response()->json(['status' => 'success', 'message' => 'Added to ' . $playlist->name]);
    }

    public function showPlaylist($id)
    {
        $playlist = Playlist::with('songs')->where('user_id', auth()->id())->findOrFail($id);
        $favoriteIds = auth()->user()->favoriteSongs->pluck('id')->toArray();
        return view('playlists.show', compact('playlist', 'favoriteIds'));
    }

    public function removeSongFromPlaylist($playlistId, $songId)
    {
        $playlist = Playlist::where('user_id', auth()->id())->findOrFail($playlistId);
        $playlist->songs()->detach($songId);
        return back()->with('success', 'Song removed');
    }
}
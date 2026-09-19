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
        $request->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string|max:500', 'cover' => 'nullable|image|max:2048', 'is_public' => 'nullable|boolean']);
        $path = $request->hasFile('cover') ? $request->file('cover')->store('playlist_covers', 'public') : null;

        Playlist::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'cover_path' => $path ? 'storage/' . $path : 'images/default_playlist.jpg',
            'is_public' => $request->boolean('is_public'),
            'share_token' => \Illuminate\Support\Str::random(16),
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
        $request->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string|max:500', 'cover' => 'nullable|image|max:2048', 'is_public' => 'nullable|boolean']);

        $playlist->name = $request->name;
        $playlist->description = $request->description;
        $playlist->is_public = $request->boolean('is_public');
        if (!$playlist->share_token) $playlist->share_token = \Illuminate\Support\Str::random(16);
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
        $playlist = Playlist::with('songs')->findOrFail($id);
        // Private: hanya owner. Public: siapa saja yang login.
        if (!$playlist->is_public && $playlist->user_id !== auth()->id()) abort(403);
        $favoriteIds = auth()->user()->favoriteSongs->pluck('id')->toArray();
        $isOwner = $playlist->user_id === auth()->id();
        $ownerName = $playlist->user ? $playlist->user->name : '-';
        return view('playlists.show', compact('playlist', 'favoriteIds', 'isOwner', 'ownerName'));
    }

    public function removeSongFromPlaylist($playlistId, $songId)
    {
        $playlist = Playlist::where('user_id', auth()->id())->findOrFail($playlistId);
        $playlist->songs()->detach($songId);
        return back()->with('success', 'Song removed');
    }

    /**
     * Pin / unpin lagu ke hero (spotlight) dashboard user. Maksimal 8 pin.
     */
    public function toggleHeroPin($songId)
    {
        $song = Song::find($songId);
        if (!$song) {
            return response()->json(['message' => 'Song not found'], 404);
        }
        $user = auth()->user();
        $already = $user->heroPins()->where('songs.id', $songId)->exists();
        if ($already) {
            $user->heroPins()->detach($songId);
            $status = 'unpinned';
        } else {
            if ($user->heroPins()->count() >= 8) {
                return response()->json(['message' => 'Maksimal 8 lagu di-pin ke hero. Lepas satu dulu.'], 422);
            }
            $max = (int) DB::table('hero_pins')->where('user_id', $user->id)->max('position');
            $user->heroPins()->attach($songId, ['position' => $max + 1]);
            $status = 'pinned';
        }
        $pinnedIds = $user->heroPins()->pluck('songs.id')->values();
        return response()->json([
            'status' => $status,
            'message' => $status === 'pinned' ? 'Ditambahkan ke hero' : 'Dilepas dari hero',
            'pinnedIds' => $pinnedIds,
        ]);
    }

    /** Toggle publik/privat playlist milik sendiri. */
    public function togglePublic($id)
    {
        $playlist = Playlist::where('user_id', auth()->id())->findOrFail($id);
        $playlist->is_public = !$playlist->is_public;
        if ($playlist->is_public && !$playlist->share_token) {
            $playlist->share_token = \Illuminate\Support\Str::random(16);
        }
        $playlist->save();
        return response()->json([
            'status' => 'ok',
            'is_public' => $playlist->is_public,
            'share_url' => $playlist->share_token ? url('/p/' . $playlist->share_token) : null,
            'message' => $playlist->is_public ? 'Playlist is now public' : 'Playlist is now private',
        ]);
    }

    /** Halaman share publik via token (bisa dibuka user lain yang login). */
    public function publicShow($token)
    {
        $playlist = Playlist::with('songs', 'user')->where('share_token', $token)->firstOrFail();
        if (!$playlist->is_public && (!$playlist->user || $playlist->user_id !== auth()->id())) abort(403);
        $favoriteIds = auth()->user()->favoriteSongs->pluck('id')->toArray();
        $isOwner = $playlist->user_id === auth()->id();
        $ownerName = $playlist->user ? $playlist->user->name : '-';
        return view('playlists.show', compact('playlist', 'favoriteIds', 'isOwner', 'ownerName'));
    }

    /** Fork/duplikat playlist publik ke library sendiri. */
    public function fork($id)
    {
        $source = Playlist::with('songs')->findOrFail($id);
        if (!$source->is_public && $source->user_id !== auth()->id()) abort(403);
        $copy = Playlist::create([
            'user_id' => auth()->id(),
            'name' => $source->name . ' (copy)',
            'description' => $source->description,
            'cover_path' => $source->cover_path,
            'is_public' => false,
            'share_token' => \Illuminate\Support\Str::random(16),
        ]);
        $copy->songs()->sync($source->songs->pluck('id')->toArray());
        return redirect()->route('playlist.show', $copy->id)->with('success', 'Playlist forked to your library!');
    }
}
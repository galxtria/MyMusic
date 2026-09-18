<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Models\Song; 
use Symfony\Component\HttpFoundation\BinaryFileResponse; // Tambahkan ini

class SongController extends Controller
{
    /**
     * CONSTRUCTOR
     */
    public function __construct()
    {
        $defaultSong = Song::first();
        
        if (!$defaultSong) {
            $defaultSong = (object)[
                'title' => 'No Song', 
                'artist' => '-', 
                'genre' => '-',
                'album_art' => '', 
                'file_path' => '',
                'lyrics' => ''
            ];
        }

        View::share('currentSong', $defaultSong);
    }

    // --- FUNGSI BARU UNTUK FIX SCROLL/SEEKING ---
    public function stream($filename)
    {
        // Jangan gunakan basename() jika path di database mengandung sub-folder
        // $filename di sini akan berisi 'music/aespa_dirtywork/aespa_dirtywork.mp3'
        
        $path = public_path($filename); 

        if (!file_exists($path)) {
            // Cek alternatif jika path dikirim tanpa kata 'music'
            $path = public_path('music/' . $filename);
        }

        if (!file_exists($path)) {
            return response()->json([
                'error' => 'File tidak ditemukan',
                'path_dicari' => $path
            ], 404);
        }

        // MIME type mengikuti ekstensi file (mp3, m4a hasil import, wav, ogg).
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'm4a', 'aac' => 'audio/mp4',
            'wav' => 'audio/wav',
            'ogg', 'oga' => 'audio/ogg',
            'flac' => 'audio/flac',
            default => 'audio/mpeg',
        };

        return response()->file($path, [
            'Accept-Ranges' => 'bytes',
            'Content-Type' => $mime,
        ]);
    }

    // 1. HOME
    public function index() {
    // Mengambil lagu dengan pagination (16 lagu per halaman)
        $songs = Song::latest()->paginate(16); 
        $trendingSongs = Song::inRandomOrder()->limit(12)->get();
        $mostPlayed = Song::where('play_count', '>', 0)->orderByDesc('play_count')->limit(12)->get();

        // Riwayat user: 10 lagu berbeda yang terakhir diputar.
        $recentIds = DB::table('song_histories')
            ->where('user_id', auth()->id())
            ->orderByDesc('played_at')
            ->limit(40)
            ->pluck('song_id')
            ->unique()
            ->take(10)
            ->values();
        $recentlyPlayed = $recentIds->isNotEmpty()
            ? Song::whereIn('id', $recentIds)->get()->sortBy(fn($s) => array_search($s->id, $recentIds->all()))->values()
            : collect();

        // Koleksi pribadi user: lagu yang di-like + ada di playlist-nya.
        // Kosong untuk user baru (tidak lagi menampilkan semua lagu sistem).
        $savedIds = auth()->user()->favoriteSongs()->pluck('songs.id')
            ->merge(DB::table('playlist_song')->whereIn('playlist_id', auth()->user()->playlists()->pluck('id'))->pluck('song_id'))
            ->unique()->values();
        $myCollection = $savedIds->isNotEmpty()
            ? Song::whereIn('id', $savedIds)->latest()->limit(15)->get()
            : collect();
        
        return view('home', compact('songs', 'trendingSongs', 'mostPlayed', 'recentlyPlayed', 'myCollection'));
    }

    // 1b. COLLECTION GRID (JSON — navigasi halaman tanpa reload)
    public function collectionJson() {
        return response()->json(Song::latest()->paginate(16));
    }

    // 1c. TRACK PLAY — catat play count + riwayat user (dipanggil player tiap ganti lagu).
    public function trackPlay(Request $request, $songId)
    {
        $song = Song::find($songId);
        if (!$song) {
            return response()->json(['message' => 'Song not found'], 404);
        }
        $song->increment('play_count');
        $song->update(['last_played_at' => now()]);
        DB::table('song_histories')->insert([
            'user_id' => auth()->id(),
            'song_id' => $song->id,
            'played_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['status' => 'ok']);
    }

    // 2. SEARCH (toleran: abaikan beda kapital, spasi, dan tanda hubung.
    // "kpop" cocok dengan "K-Pop"/"K Pop", "hiphop" dengan "Hip Hop", dst.)
    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));
        // Versi normal: huruf kecil tanpa spasi/hubung/underscore.
        $norm = mb_strtolower(preg_replace('/[\s\-_]+/', '', $query));
        // Ekspresi SQL yang menormalisasi kolom dengan cara yang sama.
        $strip = fn($col) => "REPLACE(REPLACE(REPLACE(LOWER({$col}), '-', ''), ' ', ''), '_', '')";

        $matchCols = function ($q) use ($query, $norm, $strip) {
            $q->where('title', 'LIKE', "%{$query}%")
              ->orWhere('artist', 'LIKE', "%{$query}%")
              ->orWhere('genre', 'LIKE', "%{$query}%");
            if ($norm !== '') {
                $like = "%{$norm}%";
                $q->orWhereRaw($strip('title') . ' LIKE ?', [$like])
                  ->orWhereRaw($strip('artist') . ' LIKE ?', [$like])
                  ->orWhereRaw($strip('genre') . ' LIKE ?', [$like]);
            }
            // Alias yang tidak tertangkap normalisasi (karakter khusus).
            foreach (['rnb' => 'r&b'] as $from => $to) {
                if ($norm !== '' && str_contains($norm, $from)) {
                    $q->orWhere('title', 'LIKE', "%{$to}%")
                      ->orWhere('artist', 'LIKE', "%{$to}%")
                      ->orWhere('genre', 'LIKE', "%{$to}%");
                }
            }
        };

        $artists = Song::select('artist', 'artist_image', 'album_art')
                    ->where($matchCols)
                    ->get()
                    ->unique('artist');

        $songs = Song::where($matchCols)->get();

        return view('search', compact('songs', 'artists', 'query'));
    }

    // 3. FAVORITES
    public function favorites()
    {
        // Pastikan User mengambil lagu favorit dari relasi (bukan acak)
        if (Auth::check()) {
            $songs = Auth::user()->favoriteSongs()->latest()->get();
        } else {
            $songs = collect();
        }
        return view('favorites', compact('songs'));
    }

    public function library()
    {
        $creatorName = Auth::user()->name;

        // AMBIL PLAYLIST ASLI DARI DATABASE
        $playlists = auth()->user()->playlists()->withCount('songs')->get();

        $followedArtists = \App\Models\Song::select('artist', 'album_art', 'artist_image')
                            ->latest()->get()->unique('artist')->take(6);

        return view('library', compact('playlists', 'followedArtists'));
    }

    // 5. CREATE 
    public function storePlaylist(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $path = null;
        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('playlist_covers', 'public');
        }

        $playlist = auth()->user()->playlists()->create([
            'name' => $request->name,
            'cover_path' => $path ? '/storage/' . $path : '/images/default_playlist.jpg'
        ]);

        return redirect()->route('library')->with('success', 'Playlist created!');
    }
    public function create()
    {
        return view('create');
    }

    // 6. ARTIST
    public function artist($name)
    {
        $artistName = urldecode($name);
        $songs = Song::where('artist', $artistName)->get();
        $artistInfo = $songs->first(); 

        if (!$artistInfo) {
            return redirect()->route('home');
        }

        return view('artist', compact('songs', 'artistName', 'artistInfo'));
    }
}
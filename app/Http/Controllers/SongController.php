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
            'webm', 'weba' => 'audio/webm',
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

        // Populer per periode dihitung dari API (endpoint youtube.trending),
        // bukan dari most-play lokal.

        // Pin hero milik user (maks 8). Kosong = hero fallback ke most played.
        $heroPins = auth()->user()->heroPins()->limit(8)->get();
        $heroPinIds = $heroPins->pluck('id')->values();
        
        return view('home', compact('songs', 'trendingSongs', 'mostPlayed', 'recentlyPlayed', 'myCollection', 'heroPins', 'heroPinIds'));
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
                    ->unique(fn($s) => mb_strtolower($s->artist))
                    ->values();

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

        // Playlist publik milik user lain untuk discovery
        $publicPlaylists = \App\Models\Playlist::with('user')->withCount('songs')
            ->where('is_public', true)->where('user_id', '!=', auth()->id())
            ->latest()->limit(8)->get();

        $followedArtists = \App\Models\Song::select('artist', 'album_art', 'artist_image')
                            ->latest()->get()->unique(fn($s) => mb_strtolower($s->artist))->take(6)->values();

        return view('library', compact('playlists', 'followedArtists', 'publicPlaylists'));
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

    // 5b. SHARE SONG — halaman publik-per-link untuk satu lagu (id numerik DB).
    // Dibuka dari link share; selalu ketemu selama id-nya valid (tidak
    // bergantung pada query search yang rapuh seperti sebelumnya).
    public function sharedSong(Song $song)
    {
        return view('song_share', compact('song'));
    }

    // 6. ARTIST (gabung varian ejaan: Cortis + CORTIS = satu halaman)
    public function artist($name)
    {
        $key = mb_strtolower(trim(urldecode($name)));
        $songs = Song::whereRaw('LOWER(artist) = ?', [$key])->get();
        if ($songs->isEmpty()) {
            return redirect()->route('home');
        }

        // Nama tampil = varian dengan lagu terbanyak (ejaan kanonis).
        $artistName = $songs->countBy('artist')->sortDesc()->keys()->first();
        $artistInfo = $songs->firstWhere('artist', $artistName) ?? $songs->first();

        $isFollowing = DB::table('artist_follows')->where('user_id', auth()->id())->where('artist_name', $artistName)->exists();
        $followers = DB::table('artist_follows')->whereRaw('LOWER(artist_name) = ?', [$key])->count();

        return view('artist', compact('songs', 'artistName', 'artistInfo', 'isFollowing', 'followers'));
    }

    // 7. RADIO — prioritas: genre sama > artis sama (lintas ejaan) >
    // genre favorit user > most-played. TIDAK PERNAH tambal acak murni,
    // agar radio K-Pop tidak kecampuran hiphop/pop.
    public function radio($songId)
    {
        $song = Song::find($songId);
        if (!$song) return response()->json(['message' => 'Song not found'], 404);
        $take = 15;
        $exclude = [$song->id];
        $tracks = collect();
        $addMore = function ($q, $ignoreExclude = false) use (&$tracks, &$exclude, $take) {
            if ($tracks->count() >= $take) return;
            if (!$ignoreExclude) $q->whereNotIn('id', $exclude);
            $got = $q->limit($take - $tracks->count())->get();
            $tracks = $tracks->merge($got);
            $exclude = array_merge($exclude, $got->pluck('id')->all());
        };

        // Genre placeholder stub YouTube tidak bermakna — abaikan.
        $seedGenre = $song->genre && mb_strtolower($song->genre) !== 'youtube' ? $song->genre : null;
        $artistKey = mb_strtolower($song->artist ?? '');

        // 1) Genre sama (atau artis sama lintas ejaan: Cortis = CORTIS).
        if ($seedGenre) {
            $addMore(Song::where(function ($w) use ($seedGenre, $artistKey) {
                $w->where('genre', $seedGenre)->orWhereRaw('LOWER(artist) = ?', [$artistKey]);
            })->inRandomOrder());
        } else {
            $addMore(Song::whereRaw('LOWER(artist) = ?', [$artistKey])->inRandomOrder());
        }

        // 2) Genre sama boleh berulang (se-vibe lebih penting daripada anti-repeat
        // di library kecil) — kecuali lagu yang sedang diputar.
        if ($seedGenre && $tracks->count() < $take) {
            $addMore(Song::where('genre', $seedGenre)->where('id', '!=', $song->id)->inRandomOrder(), true);
        }

        // 3) Genre favorit pendengar (dari likes + riwayat) — tetap se-vibe.
        if ($tracks->count() < $take) {
            $topGenres = $this->topGenresFor(auth()->user(), 3);
            if ($seedGenre && !$topGenres->contains($seedGenre)) {
                $topGenres->prepend($seedGenre);
            }
            if ($topGenres->isNotEmpty()) {
                $addMore(Song::whereIn('genre', $topGenres->values())->inRandomOrder());
            }
        }

        // 4) Terakhir: most-played, lalu terbaru (bukan acak).
        if ($tracks->count() < $take) {
            $addMore(Song::where('play_count', '>', 0)->orderByDesc('play_count'));
        }
        if ($tracks->count() < $take) {
            $addMore(Song::latest());
        }

        return response()->json([
            'results' => $tracks->values(),
            'seed_genre' => $seedGenre,
        ]);
    }

    /** Genre teratas user dari likes + riwayat (tanpa placeholder YouTube). */
    private function topGenresFor($user, int $limit = 3)
    {
        $liked = $user->favoriteSongs()->pluck('songs.id');
        $hist = DB::table('song_histories')->where('user_id', $user->id)->pluck('song_id');
        $ids = $liked->merge($hist)->unique()->values();
        return Song::select('genre', DB::raw('COUNT(*) as c'))
            ->when($ids->isNotEmpty(), fn($q) => $q->whereIn('id', $ids))
            ->whereNotNull('genre')->where('genre', '!=', '')
            ->whereRaw('LOWER(genre) != ?', ['youtube'])
            ->groupBy('genre')->orderByDesc('c')->limit($limit)->pluck('genre');
    }

    // 8. REKOMENDASI — "Because you listened": genre teratas dari likes+history
    public function recommendations()
    {
        $user = auth()->user();
        $genreCounts = $this->topGenresFor($user, 3);
        if ($genreCounts->isEmpty()) {
            return response()->json(['results' => Song::where('play_count', '>', 0)->orderByDesc('play_count')->limit(10)->get()->values(), 'reason' => 'popular']);
        }
        $knownIds = $user->favoriteSongs()->pluck('songs.id')
            ->merge(DB::table('song_histories')->where('user_id', $user->id)->pluck('song_id'))->unique();
        $results = Song::whereIn('genre', $genreCounts)->whereNotIn('id', $knownIds)
            ->inRandomOrder()->limit(12)->get();
        return response()->json(['results' => $results->values(), 'reason' => $genreCounts->values()]);
    }

    // 9. FOLLOW ARTIST (selalu simpan ejaan kanonis agar tak dobel)
    public function toggleArtistFollow(Request $request, $name)
    {
        $artistName = \App\Support\ArtistNames::canonical(urldecode($name));
        $key = mb_strtolower($artistName);
        $exists = DB::table('artist_follows')->where('user_id', auth()->id())->where('artist_name', $artistName)->exists();
        if ($exists) {
            DB::table('artist_follows')->where('user_id', auth()->id())->where('artist_name', $artistName)->delete();
            $status = 'unfollowed';
        } else {
            DB::table('artist_follows')->insert(['user_id' => auth()->id(), 'artist_name' => $artistName, 'created_at' => now(), 'updated_at' => now()]);
            $status = 'followed';
        }
        $followers = DB::table('artist_follows')->whereRaw('LOWER(artist_name) = ?', [$key])->count();
        return response()->json(['status' => $status, 'followers' => $followers, 'artistName' => $artistName]);
    }
}
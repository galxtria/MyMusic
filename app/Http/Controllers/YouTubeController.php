<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Song;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class YouTubeController extends Controller
{
    /**
     * Daftar instance Piped API (failover otomatis — instance mati = coba berikutnya).
     * Urutan: yang terverifikasi hidup ditaruh paling depan.
     */
    private function pipedInstances(): array
    {
        return [
            'https://api.piped.private.coffee',
            'https://pipedapi.adminforge.de',
            'https://pipedapi.reallyaweso.me',
            'https://pipedapi.leptons.xyz',
            'https://pipedapi.r4fo.com',
            'https://pipedapi.nosebs.ru',
            'https://pipedapi.kavin.rocks',
        ];
    }

    /**
     * GET ke Piped dengan failover antar instance.
     * Return response sukses pertama, atau null jika semua gagal.
     */
    private function pipedGet(string $path, array $params = [])
    {
        foreach ($this->pipedInstances() as $base) {
            try {
                $res = Http::timeout(12)->get(rtrim($base, '/') . $path, $params);
                if ($res->successful()) return $res;
            } catch (\Throwable $e) {
                continue;
            }
        }
        return null;
    }

    /**
     * Get full audio stream URL using yt-dlp.
     */
    public function streamAudio(Request $request)
    {
        $title = $request->input('title');
        $artist = $request->input('artist');
        $videoId = $request->input('id');

        // Mode tepat: langsung ke video (dipakai halaman Import).
        // Mode cari: berdasarkan judul + artis (dipakai lagu metadata lama).
        if (!empty($videoId) && preg_match('/^[A-Za-z0-9_-]{6,16}$/', $videoId)) {
            $query = 'https://www.youtube.com/watch?v=' . $videoId;
        } elseif (!empty($title)) {
            $query = "ytsearch1: {$title} {$artist} audio";
        } else {
            abort(400, 'Title required');
        }

        $cacheKey = "ytdlp_stream_" . md5($query);

        // Jangan cache hasil null: kegagalan sesaat yt-dlp tidak boleh
        // bikin lagu 404 selama 1 jam. Hanya URL valid yang di-cache.
        $streamUrl = Cache::get($cacheKey);
        if (!$streamUrl) {
            $streamUrl = $this->resolveStreamUrl($query);
            if ($streamUrl && filter_var($streamUrl, FILTER_VALIDATE_URL)) {
                Cache::put($cacheKey, $streamUrl, 3600);
            } else {
                $streamUrl = null;
            }
        }

        if ($streamUrl && filter_var($streamUrl, FILTER_VALIDATE_URL)) {
            return redirect($streamUrl);
        }

        abort(404, 'Audio stream not found');
    }

    /**
     * Jalankan yt-dlp --get-url. Coba format m4a dulu, fallback ke bestaudio/best.
     * Return URL atau null. Tidak pernah throw.
     */
    private function resolveStreamUrl(string $query): ?string
    {
        $binary = base_path('yt-dlp.exe');
        if (!file_exists($binary)) {
            return null;
        }
        foreach (['bestaudio[ext=m4a]/bestaudio/best', 'best'] as $format) {
            try {
                // Use double quotes for the binary path and arguments to ensure Windows compatibility
                $cmd = '"' . $binary . '" --no-warnings -f ' . escapeshellarg($format) . ' --get-url ' . escapeshellarg($query) . ' 2> NUL';
                $output = shell_exec($cmd);
                if ($output) {
                    $lines = array_filter(explode("\n", trim($output)));
                    $url = end($lines);
                    if ($url && filter_var($url, FILTER_VALIDATE_URL)) return $url;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }
        return null;
    }

    /**
     * Search songs on YouTube via Piped API.
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        if (empty($query)) return response()->json(['results' => []]);

        // Filter music_songs (atau all)
        $response = $this->pipedGet('/search', [
            'q' => $query,
            'filter' => 'music_songs'
        ]);

        if ($response) {
            $data = $response->json();
            $items = $data['items'] ?? [];
            return response()->json(['results' => $items]);
        }

        return response()->json(['results' => [], 'message' => 'Semua instance Piped sedang tidak bisa dihubungi'], 500);
    }

    /**
     * Trending berbasis API (Piped) dengan periode.
     * - today: hasil live hari ini (sekaligus disimpan sebagai snapshot harian).
     * - week: gabungan snapshot 7 hari terakhir, diranking berdasar
     *   frekuensi + posisi muncul, jadi daftarnya ganti tiap minggu.
     */
    public function trending(Request $request)
    {
        $period = strtolower($request->input('period', 'today')) === 'week' ? 'week' : 'today';

        if ($period === 'week') {
            $merged = $this->weeklyTrending();
            if (!empty($merged)) return response()->json(['results' => $merged, 'period' => 'week']);
        }

        // Cache 30 menit agar pindah tab / reload tidak menghajar API berkali-kali.
        $items = Cache::remember('trending_live', now()->addMinutes(30), function () {
            return $this->fetchPopularItems();
        });
        if (empty($items)) return response()->json(['results' => []], 500);
        $this->snapshotToday($items);
        return response()->json(['results' => $items, 'period' => 'today']);
    }

    /**
     * Ambil daftar populer live dari API (dipakai tab Today + snapshot).
     */
    private function fetchPopularItems(): array
    {
        $searches = [
            ['q' => 'global hits popular songs', 'filter' => 'music_songs'],
            ['q' => 'top songs worldwide 2026', 'filter' => 'music_songs'],
        ];
        $merged = [];
        foreach ($searches as $params) {
            $response = $this->pipedGet('/search', $params);
            if (!$response) continue;
            foreach (($response->json()['items'] ?? []) as $item) {
                $key = $item['url'] ?? null;
                if ($key && !isset($merged[$key])) $merged[$key] = $item;
            }
        }
        if (empty($merged)) return [];
        $items = array_filter(array_values($merged), function ($i) {
            $dur = (int) ($i['duration'] ?? 0);
            return ($i['type'] ?? '') === 'stream'
                && empty($i['isLive'])
                && $dur > 0 && $dur <= 1200;
        });
        return array_slice(array_values($items), 0, 15);
    }

    /**
     * Simpan snapshot trending hari ini (8 hari TTL, 1 key per tanggal).
     */
    private function snapshotToday(array $items): void
    {
        try {
            Cache::put('trending_day_' . now()->toDateString(), array_values($items), now()->addDays(8));
        } catch (\Throwable $e) {
            // cache gagal = abaikan, trending live tetap jalan
        }
    }

    /**
     * Gabungkan snapshot 7 hari terakhir jadi ranking mingguan.
     * Skor = jumlah (16 - posisi) di tiap hari kemunculan: makin sering dan
     * makin atas posisinya, makin tinggi rankingnya.
     */
    private function weeklyTrending(): array
    {
        $scores = [];
        $byUrl = [];
        for ($d = 0; $d < 7; $d++) {
            try {
                $day = Cache::get('trending_day_' . now()->subDays($d)->toDateString(), []);
            } catch (\Throwable $e) {
                continue;
            }
            if (!is_array($day)) continue;
            foreach (array_values($day) as $pos => $item) {
                $url = is_array($item) ? ($item['url'] ?? null) : null;
                if (!$url) continue;
                $scores[$url] = ($scores[$url] ?? 0) + (16 - min($pos, 15));
                if (!isset($byUrl[$url])) $byUrl[$url] = $item;
            }
        }
        if (empty($scores)) return [];
        arsort($scores);
        $out = [];
        foreach (array_keys($scores) as $url) {
            $out[] = $byUrl[$url];
            if (count($out) >= 15) break;
        }
        return $out;
    }

    /**
     * Mood-based discovery.
     */
    public function moodSearch($mood)
    {
        $keywords = [
            'fokus' => 'lofi hip hop focus study',
            'energetik' => 'edm workout gaming music',
            'santai' => 'chill acoustic pop relax',
            'sedih' => 'sad emotional piano vocal',
            'popular' => 'top hits 2026 best popular songs chart'
        ];

        $q = $keywords[strtolower($mood)] ?? 'popular music';

        // Mode trending: gabung 2 query hits dunia (filter music_songs
        // mengembalikan track per track, bukan kompilasi/radio live).
        $searches = strtolower($mood) === 'popular'
            ? [
                ['q' => 'global hits popular songs', 'filter' => 'music_songs'],
                ['q' => 'top songs worldwide 2026', 'filter' => 'music_songs'],
            ]
            : [['q' => $q, 'filter' => 'all']];

        $merged = [];
        foreach ($searches as $params) {
            $response = $this->pipedGet('/search', $params);
            if (!$response) continue;
            foreach (($response->json()['items'] ?? []) as $item) {
                $key = $item['url'] ?? null;
                if ($key && !isset($merged[$key])) $merged[$key] = $item;
            }
        }

        if (!empty($merged)) {
            $data = ['items' => array_values($merged)];
            // Hanya video lagu per lagu: buang live/upcoming (durasi <= 0 / isLive)
            // dan kompilasi/mix berjam-jam (durasi > 20 menit).
            $items = array_filter($data['items'] ?? [], function($i) {
                $dur = (int)($i['duration'] ?? 0);
                return ($i['type'] ?? '') === 'stream'
                    && empty($i['isLive'])
                    && $dur > 0 && $dur <= 1200;
            });
            // Ambil 15 teratas
            $items = array_slice(array_values($items), 0, 15);
            return response()->json(['results' => $items]);
        }

        return response()->json(['results' => []], 500);
    }

    /**
     * Tambahkan lagu dari YouTube ke Library
     */
    public function addToLibrary(Request $request)
    {
        $request->validate([
            'videoId' => 'required',
            'title' => 'required',
            'uploaderName' => 'required',
            'thumbnail' => 'required',
            'duration' => 'required'
        ]);

        // Cek apakah sudah ada di library berdasarkan youtube_id
        $song = Song::where('youtube_id', $request->videoId)->first();

        if (!$song) {
            // Samakan ejaan artis dengan yang sudah ada agar tak dobel (CORTIS -> Cortis).
            $artist = \App\Support\ArtistNames::canonical($request->uploaderName);
            $song = Song::create([
                'title' => $request->title,
                'artist' => $artist,
                'youtube_id' => $request->videoId,
                'artwork_url' => $request->thumbnail,
                // Stub lagu online belum punya file lokal: isi fallback agar
                // lolos kolom NOT NULL (MySQL strict) dan cover tetap tampil.
                'album_art' => $request->thumbnail,
                'artist_image' => $request->thumbnail,
                'file_path' => '',
                'duration' => $request->duration,
                'genre' => 'YouTube', // default
            ]);
        }

        return response()->json([
            'message' => 'Lagu berhasil ditambahkan ke Library!',
            'song' => $song
        ]);
    }

    /**
     * Halaman Import admin (React).
     */
    public function importForm()
    {
        return view('admin.tools.import');
    }

    /**
     * Import 1 track: unduh audio full + cover, simpan sebagai lagu lokal.
     */
    public function importTrack(Request $request)
    {
        $request->validate([
            'videoId' => 'required|string|max:32',
            'title' => 'required|string|max:255',
            'uploaderName' => 'required|string|max:255',
            'thumbnail' => 'required|url',
            'duration' => 'required|integer|min:1',
            'genre' => 'required|string|max:64',
        ]);

        // Duplikat? Langsung kembalikan info-nya.
        $existing = Song::where('youtube_id', $request->videoId)->first();
        if ($existing && $existing->file_path) {
            return response()->json([
                'exists' => true,
                'message' => 'Lagu ini sudah ada di library.',
                'song' => $existing,
            ]);
        }

        $binary = base_path('yt-dlp.exe');
        if (!file_exists($binary)) {
            return response()->json(['message' => 'yt-dlp tidak ditemukan di server.'], 500);
        }

        if (!File::isDirectory(public_path('music'))) {
            File::makeDirectory(public_path('music'), 0777, true);
        }
        if (!File::isDirectory(public_path('images'))) {
            File::makeDirectory(public_path('images'), 0777, true);
        }

        $slug = Str::slug(Str::limit($request->title, 40, '')) ?: 'track';
        $stamp = time();
        $audioName = "{$stamp}_{$slug}_{$request->videoId}.m4a";
        $audioRel = 'music/' . $audioName;

        // Unduh audio (butuh waktu 10-60 detik tergantung durasi/koneksi).
        // Catatan: YouTube memblokir unduhan dari IP yang dicurigai (403).
        // Jika ada cookies.txt (ekspor via ekstensi "Get cookies.txt LOCALLY"),
        // sesi login dipakai agar unduhan lolos.
        set_time_limit(300);
        $url = 'https://www.youtube.com/watch?v=' . $request->videoId;
        $cookies = base_path('cookies.txt');
        $cmd = '"' . $binary . '" --no-warnings --no-playlist --retries 2 --socket-timeout 15'
            . (file_exists($cookies) ? ' --cookies "' . $cookies . '"' : '')
            . ' -f "bestaudio[ext=m4a]/bestaudio/best"'
            . ' -o "' . public_path('music') . DIRECTORY_SEPARATOR . $audioName . '"'
            . ' ' . escapeshellarg($url) . ' 2> NUL';
        shell_exec($cmd);

        $audioFull = public_path($audioRel);
        if (!file_exists($audioFull) || filesize($audioFull) < 1024) {
            if (file_exists($audioFull)) @unlink($audioFull);
            $hint = file_exists($cookies)
                ? 'Coba lagi nanti atau ganti video.'
                : 'YouTube memblokir unduhan (403). Ekspor cookies via ekstensi "Get cookies.txt LOCALLY", simpan sebagai cookies.txt di folder web, lalu coba lagi.';
            return response()->json(['message' => 'Gagal mengunduh audio. ' . $hint], 500);
        }

        // Unduh cover.
        $coverRel = null;
        try {
            $img = Http::timeout(30)->get($request->thumbnail);
            if ($img->successful() && strlen($img->body()) > 1024) {
                $coverName = "import_{$stamp}_{$request->videoId}.jpg";
                File::put(public_path('images/' . $coverName), $img->body());
                $coverRel = '/images/' . $coverName;
            }
        } catch (\Throwable $e) {
            $coverRel = null;
        }

        $secs = (int) $request->duration;
        $duration = floor($secs / 60) . ':' . str_pad($secs % 60, 2, '0', STR_PAD_LEFT);

        // Samakan ejaan artis dengan varian yang sudah ada (anti-duplikat).
        $canonArtist = \App\Support\ArtistNames::canonical($request->uploaderName);

        // Lirik otomatis (LRC + timestamp) dari lrclib — tidak perlu input manual.
        // Gagal/tidak ketemu = lagu tetap diimpor tanpa lirik, tidak error.
        $autoLyrics = $this->fetchSyncedLyrics($request->title, $request->uploaderName, $secs);
        // Import K-Pop: utamakan teks Korea (Hangul) bila hasil pertama romanized.
        if (mb_strtolower($request->genre) === 'k-pop' && !\App\Support\LyricsFixer::hasKorean($autoLyrics)) {
            $better = \App\Support\LyricsFixer::bestSynced($request->title, $canonArtist, $secs, true);
            if ($better['lrc'] !== '') $autoLyrics = $better['lrc'];
        }

        $song = Song::updateOrCreate(
            ['youtube_id' => $request->videoId],
            [
                'title' => $request->title,
                'artist' => $canonArtist,
                'album_art' => $coverRel ?? '/images/default-cover.png',
                'artist_image' => $coverRel ?? '/images/default_artist.jpg',
                'file_path' => '/' . $audioRel,
                'preview_url' => null,
                'duration' => $duration,
                'genre' => $request->genre,
                'lyrics' => $autoLyrics,
            ]
        );

        return response()->json([
            'exists' => false,
            'message' => 'Berhasil diimpor ke library!' . ($autoLyrics !== '' ? ' (+ lirik otomatis)' : ' (lirik tidak ditemukan otomatis)'),
            'lyrics_found' => $autoLyrics !== '',
            'song' => $song,
        ]);
    }

    /**
     * Bersihkan judul YouTube agar cocok dengan database lirik.
     * "Yellow (Official Music Video)" -> "Yellow", "Shallow - Lady Gaga MV" -> "Shallow", dst.
     */
    private function cleanTitleForLyrics(string $title): string
    {
        $t = $title;
        // Potong pola "Artis - Judul" versi Topic/VEVO? simpan judulnya saja jika ada " - "
        // (hati-hati: biarkan jika tidak jelas, cukup bersihkan kurungannya)
        $t = preg_replace('/\s*[\(\[]\s*(official\s+)?(music\s+)?(video|audio|lyric(s)?|mv|m\/v|visualizer|teaser|trailer).*?[\)\]]/i', '', $t);
        $t = preg_replace('/\s*[\(\[][^\(\)\[\]]*[\)\]]\s*$/', '', $t); // sisa kurung di ujung
        $t = preg_replace('/\s*(ft\.?|feat\.?|featuring)\s+.+$/i', '', $t);
        $t = preg_replace('/\s*[\|\-–—]\s*(official|mv|m\/v|audio|video).*$/i', '', $t);
        $t = preg_replace('/\s+/', ' ', trim($t));
        return $t;
    }

    private function cleanArtistForLyrics(string $artist): string
    {
        $a = preg_replace('/\s*-\s*topic$/i', '', $artist);
        $a = preg_replace('/\s*vevo$/i', '', $a);
        $a = preg_replace('/\s+/', ' ', trim($a));
        return $a;
    }

    /**
     * Ambil lirik sinkron (format LRC) dari lrclib.net (gratis, tanpa key).
     * Return string LRC atau '' jika tidak ketemu. Tidak pernah throw.
     */
    private function fetchSyncedLyrics(string $title, string $artist, int $durationSecs = 0): string
    {
        try {
            $t = $this->cleanTitleForLyrics($title);
            $a = $this->cleanArtistForLyrics($artist);
            if ($t === '') return '';

            // 1) Coba kecocokan langsung.
            $res = Http::timeout(15)->get('https://lrclib.net/api/get', [
                'artist_name' => $a,
                'track_name' => $t,
            ]);
            if ($res->successful()) {
                $lrc = $this->pickSynced($res->json(), $durationSecs);
                if ($lrc !== '') return $lrc;
            }

            // 2) Fallback: cari daftar kandidat, ambil yang durasinya PALING MIRIP
            // (bukan asal kandidat pertama) agar timestamp sinkron dengan versinya.
            $res = Http::timeout(15)->get('https://lrclib.net/api/search', [
                'q' => trim($t . ' ' . $a),
            ]);
            if ($res->successful() && is_array($res->json())) {
                $lrc = $this->pickBestSynced($res->json(), $durationSecs);
                if ($lrc !== '') return $lrc;
            }
        } catch (\Throwable $e) {
            // abaikan — import tetap jalan tanpa lirik
        }
        return '';
    }

    /**
     * Ambil syncedLyrics dari satu item lrclib jika valid.
     * Cocokkan durasi (±15 detik) agar tidak dapat lirik lagu yang salah.
     */
    private function pickSynced($item, int $durationSecs): string
    {
        if (!is_array($item) || empty($item['syncedLyrics'])) return '';
        if ($durationSecs > 0 && isset($item['duration']) && is_numeric($item['duration'])) {
            if (abs((float) $item['duration'] - $durationSecs) > 15) return '';
        }
        $lrc = trim((string) $item['syncedLyrics']);
        // Minimal 3 baris timestamp agar layak disebut sinkron.
        if (substr_count($lrc, '[') < 3) return '';
        return $lrc;
    }

    /**
     * Dari daftar kandidat lrclib, pilih syncedLyrics yang durasinya paling
     * mirip dengan audio (toleransi ±15 detik). Mencegah dapat timestamp milik
     * versi live/radio-edit yang selisihnya beberapa detik.
     */
    private function pickBestSynced($items, int $durationSecs): string
    {
        if (!is_array($items)) return '';
        $best = '';
        $bestDiff = null;
        foreach ($items as $item) {
            if (!is_array($item) || empty($item['syncedLyrics'])) continue;
            $lrc = trim((string) $item['syncedLyrics']);
            if (substr_count($lrc, '[') < 3) continue;
            if ($durationSecs > 0 && isset($item['duration']) && is_numeric($item['duration'])) {
                $diff = abs((float) $item['duration'] - $durationSecs);
                if ($diff > 15) continue;
                if ($bestDiff === null || $diff < $bestDiff) {
                    $bestDiff = $diff;
                    $best = $lrc;
                }
            } elseif ($best === '') {
                $best = $lrc; // tanpa info durasi: pakai kandidat valid pertama
            }
        }
        return $best;
    }
}

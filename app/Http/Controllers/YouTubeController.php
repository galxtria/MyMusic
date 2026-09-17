<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Song;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class YouTubeController extends Controller
{
    // Gunakan public instance Piped API
    private $apiUrl = 'https://pipedapi.kavin.rocks';

    /**
     * Get full audio stream URL using yt-dlp.
     */
    public function streamAudio(Request $request)
    {
        $title = $request->input('title');
        $artist = $request->input('artist');
        
        if (empty($title)) {
            abort(400, 'Title required');
        }

        $query = "ytsearch1: {$title} {$artist} audio";
        $cacheKey = "ytdlp_stream_" . md5($query);

        $streamUrl = Cache::remember($cacheKey, 3600, function () use ($query) {
            $binary = base_path('yt-dlp.exe');
            if (!file_exists($binary)) {
                return null;
            }
            // Use double quotes for the binary path and arguments to ensure Windows compatibility
            $cmd = '"' . $binary . '" --no-warnings -f "bestaudio[ext=m4a]" --get-url ' . escapeshellarg($query) . ' 2> NUL';
            $output = shell_exec($cmd);
            
            if ($output) {
                $lines = array_filter(explode("\n", trim($output)));
                return end($lines); 
            }
            return null;
        });

        if ($streamUrl && filter_var($streamUrl, FILTER_VALIDATE_URL)) {
            return redirect($streamUrl);
        }

        abort(404, 'Audio stream not found');
    }

    /**
     * Search songs on YouTube via Piped API.
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        if (empty($query)) return response()->json(['results' => []]);

        // Filter music_songs (atau all)
        $response = Http::get("{$this->apiUrl}/search", [
            'q' => $query,
            'filter' => 'music_songs'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $items = $data['items'] ?? [];
            return response()->json(['results' => $items]);
        }

        return response()->json(['results' => []], 500);
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
            'popular' => 'global top hits popular songs 2024'
        ];

        $q = $keywords[strtolower($mood)] ?? 'popular music';

        // Filter all agar lebih banyak dapet hasil campuran atau video musik biasa
        $response = Http::get("{$this->apiUrl}/search", [
            'q' => $q,
            'filter' => 'all'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $items = array_filter($data['items'] ?? [], function($i) {
                return $i['type'] === 'stream';
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
            $song = Song::create([
                'title' => $request->title,
                'artist' => $request->uploaderName,
                'youtube_id' => $request->videoId,
                'artwork_url' => $request->thumbnail,
                'duration' => $request->duration,
                'genre' => 'YouTube', // default
            ]);
        }

        return response()->json([
            'message' => 'Lagu berhasil ditambahkan ke Library!',
            'song' => $song
        ]);
    }
}

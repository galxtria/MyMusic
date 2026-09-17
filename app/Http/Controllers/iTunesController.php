<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class iTunesController extends Controller
{
    /**
     * Proxy pencarian ke iTunes Search API.
     * GET /api/itunes/search?term=...&limit=25
     */
    public function search(Request $request)
    {
        $term = $request->input('term', '');
        $limit = $request->input('limit', 25);

        if (empty(trim($term))) {
            return response()->json(['results' => [], 'resultCount' => 0]);
        }

        try {
            $response = Http::timeout(10)->get('https://itunes.apple.com/search', [
                'term'    => $term,
                'media'   => 'music',
                'entity'  => 'song',
                'limit'   => min((int) $limit, 50),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Transform hasil untuk frontend
                $results = collect($data['results'] ?? [])->map(function ($track) {
                    return [
                        'trackId'        => $track['trackId'] ?? null,
                        'trackName'      => $track['trackName'] ?? 'Unknown',
                        'artistName'     => $track['artistName'] ?? 'Unknown',
                        'artistId'       => $track['artistId'] ?? null,
                        'collectionName' => $track['collectionName'] ?? '',
                        'artworkUrl100'  => $track['artworkUrl100'] ?? '',
                        'artworkUrl600'  => str_replace('100x100', '600x600', $track['artworkUrl100'] ?? ''),
                        'previewUrl'     => $track['previewUrl'] ?? '',
                        'primaryGenreName' => $track['primaryGenreName'] ?? '',
                        'trackTimeMillis'  => $track['trackTimeMillis'] ?? 0,
                    ];
                })->filter(fn($t) => !empty($t['previewUrl']))->values();

                return response()->json([
                    'results'     => $results,
                    'resultCount' => $results->count(),
                ]);
            }

            return response()->json(['results' => [], 'resultCount' => 0], 502);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'iTunes API tidak dapat dihubungi.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Simpan lagu dari iTunes ke database lokal (find-or-create).
     * POST /api/itunes/add-to-library
     */
    public function addToLibrary(Request $request)
    {
        $request->validate([
            'trackId'     => 'required|integer',
            'trackName'   => 'required|string|max:255',
            'artistName'  => 'required|string|max:255',
            'artistId'    => 'nullable|integer',
            'previewUrl'  => 'required|url',
            'artworkUrl'  => 'required|url',
            'genre'       => 'nullable|string|max:100',
            'duration'    => 'nullable|string|max:20',
        ]);

        $song = Song::firstOrCreate(
            ['itunes_track_id' => $request->trackId],
            [
                'title'            => $request->trackName,
                'artist'           => $request->artistName,
                'itunes_artist_id' => $request->artistId,
                'preview_url'      => $request->previewUrl,
                'artwork_url'      => $request->artworkUrl,
                'album_art'        => $request->artworkUrl,
                'file_path'        => $request->previewUrl,
                'genre'            => $request->genre ?? 'Music',
                'duration'         => $request->duration ?? '0:30',
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => $song->wasRecentlyCreated ? 'Lagu ditambahkan ke Library!' : 'Lagu sudah ada di Library.',
            'song'    => [
                'id'    => $song->id,
                'title' => $song->title,
                'artist' => $song->artist,
            ],
        ]);
    }

    /**
     * Pencarian mood-based dengan mapping pre-defined.
     * GET /api/itunes/mood/{mood}
     */
    public function moodSearch(string $mood)
    {
        $moodMap = [
            'fokus'    => 'Lo-Fi Beats Study Chill',
            'energetik'=> 'Workout Energy Hits Pump',
            'santai'   => 'Chill Acoustic Relax Calm',
            'sedih'    => 'Sad Emotional Ballad Songs',
            'popular'  => 'Top Hits Popular Songs 2024',
        ];

        $term = $moodMap[strtolower($mood)] ?? $mood;

        try {
            $response = Http::timeout(10)->get('https://itunes.apple.com/search', [
                'term'   => $term,
                'media'  => 'music',
                'entity' => 'song',
                'limit'  => 20,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $results = collect($data['results'] ?? [])->map(function ($track) {
                    return [
                        'trackId'        => $track['trackId'] ?? null,
                        'trackName'      => $track['trackName'] ?? 'Unknown',
                        'artistName'     => $track['artistName'] ?? 'Unknown',
                        'artistId'       => $track['artistId'] ?? null,
                        'artworkUrl100'  => $track['artworkUrl100'] ?? '',
                        'artworkUrl600'  => str_replace('100x100', '600x600', $track['artworkUrl100'] ?? ''),
                        'previewUrl'     => $track['previewUrl'] ?? '',
                        'primaryGenreName' => $track['primaryGenreName'] ?? '',
                        'trackTimeMillis'  => $track['trackTimeMillis'] ?? 0,
                    ];
                })->filter(fn($t) => !empty($t['previewUrl']))->values();

                return response()->json([
                    'mood'        => $mood,
                    'searchTerm'  => $term,
                    'results'     => $results,
                    'resultCount' => $results->count(),
                ]);
            }

            return response()->json(['results' => [], 'resultCount' => 0], 502);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'iTunes API tidak dapat dihubungi.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

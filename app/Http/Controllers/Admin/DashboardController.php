<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSongs = Song::count();
        $totalUsers = User::count();
        $totalPlays = (int) Song::sum('play_count');
        $totalPlaylists = DB::table('playlists')->count();

        // Plays per hari 14 hari terakhir (dari song_histories)
        $days = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $days[$d] = 0;
        }
        $rows = DB::table('song_histories')
            ->select(DB::raw('DATE(played_at) as d'), DB::raw('COUNT(*) as c'))
            ->where('played_at', '>=', now()->subDays(14))
            ->groupBy('d')
            ->pluck('c', 'd');
        foreach ($rows as $d => $c) {
            if (isset($days[$d])) $days[$d] = (int) $c;
        }

        $topSongs = Song::where('play_count', '>', 0)->orderByDesc('play_count')->limit(8)->get();
        $topUsers = User::select('users.id', 'users.name', 'users.email')
            ->selectSub(function ($q) {
                $q->from('song_histories')->selectRaw('COUNT(*)')->whereColumn('song_histories.user_id', 'users.id');
            }, 'plays')
            ->orderByDesc('plays')
            ->limit(6)
            ->get();

        // Distribusi genre BERDASARKAN PLAYS (bukan jumlah lagu katalog),
        // agar mencerminkan kebiasaan dengar aktual. K-Pop dan Pop dihitung
        // sebagai dua genre terpisah (sesuai nilai kolom genre).
        $genreDist = Song::select('genre', DB::raw('COALESCE(SUM(play_count),0) as plays'), DB::raw('COUNT(*) as songs'))
            ->groupBy('genre')->orderByDesc('plays')->orderByDesc('songs')->limit(6)->get()
            ->map(fn($r) => ['genre' => $r->genre, 'c' => (int) $r->plays, 'songs' => (int) $r->songs]);

        // Artis yang ejaannya dobel (Cortis vs CORTIS) untuk alat merge.
        $dupArtists = \App\Support\ArtistNames::duplicateGroups();

        return view('admin.dashboard', compact(
            'totalSongs', 'totalUsers', 'totalPlays', 'totalPlaylists', 'days', 'topSongs', 'topUsers', 'genreDist', 'dupArtists'
        ));
    }

    /** Gabungkan varian ejaan artis ke satu nama kanonis. */
    public function mergeArtists(\Illuminate\Http\Request $request)
    {
        $request->validate(['canonical' => 'required|string|max:255']);
        $n = \App\Support\ArtistNames::merge($request->canonical);
        return redirect()->back()->with('success', "Merged to {$request->canonical} ({$n} songs updated).");
    }
}

<?php

namespace App\Support;

use App\Models\Song;
use Illuminate\Support\Facades\DB;

/**
 * Normalisasi nama artis TANPA merusak stilasi resmi ("aespa",
 * "BABYMONSTER", "LE SSERAFIM" tidak boleh di-Title-Case paksa).
 *
 * Strategi: ejaan varian pertama yang sudah ada di DB dianggap kanonis.
 * Import baru yang sama secara case-insensitive memakai ejaan kanonis itu.
 */
class ArtistNames
{
    /** Ejaan kanonis untuk nama artis (varian terbanyak di DB, fallback input rapi). */
    public static function canonical(string $name): string
    {
        $name = trim(preg_replace('/\s+/', ' ', $name));
        if ($name === '') return $name;
        $hit = Song::select('artist', DB::raw('COUNT(*) as c'))
            ->whereRaw('LOWER(artist) = ?', [mb_strtolower($name)])
            ->groupBy('artist')
            ->orderByDesc('c')
            ->first();
        return $hit ? $hit->artist : $name;
    }

    /** Semua varian ejaan yang tercatat untuk satu artis (case-insensitive). */
    public static function variants(string $name): array
    {
        return Song::whereRaw('LOWER(artist) = ?', [mb_strtolower(trim($name))])
            ->distinct()->pluck('artist')->values()->all();
    }

    /**
     * Grup artis yang terduplikat ejaannya:
     * [['key' => 'cortis', 'variants' => ['Cortis','CORTIS'], 'songs' => 5], ...]
     */
    public static function duplicateGroups(): array
    {
        $rows = DB::select(
            "SELECT LOWER(artist) AS k FROM songs GROUP BY k HAVING COUNT(DISTINCT artist) > 1"
        );
        $out = [];
        foreach ($rows as $r) {
            $variants = Song::whereRaw('LOWER(artist) = ?', [$r->k])->distinct()->pluck('artist')->values();
            $out[] = [
                'key' => $r->k,
                'variants' => $variants,
                'canonical' => self::canonical($variants->first()),
                'songs' => Song::whereRaw('LOWER(artist) = ?', [$r->k])->count(),
            ];
        }
        return $out;
    }

    /**
     * Gabungkan semua varian ke satu ejaan kanonis.
     * Return jumlah lagu yang diubah.
     */
    public static function merge(string $canonical): int
    {
        $canonical = trim($canonical);
        if ($canonical === '') return 0;
        return DB::transaction(function () use ($canonical) {
            $key = mb_strtolower($canonical);
            // Follows: hindari pelanggaran unique (user_id, artist_name)
            // bila user sudah follow varian lain — buang baris dobelnya.
            foreach (DB::table('artist_follows')->whereRaw('LOWER(artist_name) = ?', [$key])->get() as $row) {
                $exists = DB::table('artist_follows')
                    ->where('user_id', $row->user_id)->where('artist_name', $canonical)
                    ->where('id', '!=', $row->id)->exists();
                if ($exists) {
                    DB::table('artist_follows')->where('id', $row->id)->delete();
                } elseif ($row->artist_name !== $canonical) {
                    DB::table('artist_follows')->where('id', $row->id)->update(['artist_name' => $canonical]);
                }
            }
            return Song::whereRaw('LOWER(artist) = ?', [$key])->where('artist', '!=', $canonical)->update(['artist' => $canonical]);
        });
    }
}

<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

/**
 * Ambil lirik sinkron (LRC) dari lrclib.net dengan preferensi teks Korea (Hangul).
 * Tidak pernah throw — return '' bila tak ketemu yang layak.
 */
class LyricsFixer
{
    /** Kata kunci varian yang dideprioritaskan (bukan versi studio asli). */
    private const VARIANT_HINTS = ['instrumental', 'paused', 'slowed', 'sped up', 'speed up', 'karaoke', 'cover', 'live', 'remix', 'acoustic version'];

    public static function cleanTitle(string $title): string
    {
        $t = $title;
        $t = preg_replace('/\s*[\(\[]\s*(official\s+)?(music\s+)?(video|audio|lyric(s)?|mv|m\/v|visualizer|teaser|trailer).*?[\)\]]/i', '', $t);
        $t = preg_replace('/\s*[\(\[][^\(\)\[\]]*[\)\]]\s*$/', '', $t);
        $t = preg_replace('/\s*(ft\.?|feat\.?|featuring)\s+.+$/i', '', $t);
        $t = preg_replace('/\s*[\|\-–—]\s*(official|mv|m\/v|audio|video).*$/i', '', $t);
        return trim(preg_replace('/\s+/', ' ', $t));
    }

    public static function cleanArtist(string $artist): string
    {
        $a = preg_replace('/\s*-\s*topic$/i', '', $artist);
        $a = preg_replace('/\s*vevo$/i', '', $a);
        return trim(preg_replace('/\s+/', ' ', $a));
    }

    public static function hasKorean(string $lrc): bool
    {
        return (bool) preg_match('/[\x{AC00}-\x{D7AF}]/u', $lrc);
    }

    public static function timestampLines(string $lrc): int
    {
        return substr_count($lrc, '[');
    }

    private static function isVariantTitle(string $trackName): bool
    {
        $n = mb_strtolower($trackName);
        foreach (self::VARIANT_HINTS as $h) {
            if (str_contains($n, $h)) return true;
        }
        return false;
    }

    /**
     * Cari LRC terbaik. $preferKorean=true mengutamakan yang ada Hangul
     * (untuk K-Pop), tapi tetap menerima non-Hangul bila tak ada yang cocok.
     * Semua kandidat (direct + search) dinilai, skor tertinggi menang —
     * versi yang lebih lengkap (baris lebih banyak) diberi bonus.
     * Return ['lrc' => string, 'info' => string] untuk logging.
     */
    public static function bestSynced(string $title, string $artist, int $durationSecs = 0, bool $preferKorean = false): array
    {
        $t = self::cleanTitle($title);
        $a = self::cleanArtist($artist);
        if ($t === '') return ['lrc' => '', 'info' => 'empty-title'];

        $pool = [];
        // 1) Kecocokan langsung — masuk pool kandidat, bukan vonis akhir.
        try {
            $res = Http::timeout(15)->get('https://lrclib.net/api/get', [
                'artist_name' => $a, 'track_name' => $t,
            ]);
            if ($res->successful() && is_array($res->json())) $pool[] = $res->json();
        } catch (\Throwable $e) {
        }

        // 2) Daftar kandidat search — nilai semua, skor tertinggi menang.
        try {
            $res = Http::timeout(15)->get('https://lrclib.net/api/search', ['q' => trim("$t $a")]);
            if ($res->successful() && is_array($res->json())) {
                foreach ($res->json() as $item) $pool[] = $item;
            }
        } catch (\Throwable $e) {
        }
        if (empty($pool)) return ['lrc' => '', 'info' => 'no-candidates'];
        $best = ['lrc' => '', 'info' => 'no-match', 'score' => -1];
        foreach ($pool as $item) {
            $got = self::scoreOne($item, $t, $durationSecs, $preferKorean);
            if (($got['score'] ?? -1) > $best['score']) $best = $got;
        }
        unset($best['score']);
        return $best;
    }

    private static function scoreOne($item, string $wantTitle, int $durationSecs, bool $preferKorean): array
    {
        if (!is_array($item) || empty($item['syncedLyrics'])) return ['lrc' => '', 'info' => 'empty', 'score' => -1];
        $lrc = trim((string) $item['syncedLyrics']);
        if (self::timestampLines($lrc) < 3) return ['lrc' => '', 'info' => 'too-few-lines', 'score' => -1];

        $diff = null;
        if ($durationSecs > 0 && isset($item['duration']) && is_numeric($item['duration'])) {
            $diff = abs((float) $item['duration'] - $durationSecs);
            if ($diff > 15) return ['lrc' => '', 'info' => 'duration-mismatch', 'score' => -1];
        }

        $score = 100;
        $flags = [];
        if (self::hasKorean($lrc)) {
            $score += $preferKorean ? 50 : 5;
            $flags[] = 'korean';
        } elseif ($preferKorean) {
            $score -= 30; // masih boleh menang bila tak ada kandidat Korea
            $flags[] = 'romanized';
        }
        $tn = mb_strtolower(trim((string) ($item['trackName'] ?? '')));
        if ($tn === mb_strtolower($wantTitle)) {
            $score += 20;
            $flags[] = 'exact-title';
        } elseif (self::isVariantTitle($tn)) {
            $score -= 25;
            $flags[] = 'variant';
        }
        if ($diff !== null) $score -= $diff; // makin mirip durasi makin bagus
        // Bonus kelengkapan: versi 142 baris mengalahkan versi 57 baris
        // bila judul & durasi sama-sama cocok (maks +15).
        $lines = self::timestampLines($lrc);
        $score += min($lines, 150) / 10;

        $info = ($item['trackName'] ?? '?') . ' / ' . ($item['artistName'] ?? '?')
            . ($diff !== null ? sprintf(' (Δ%.0fs', $diff) . ')' : '')
            . ($flags ? ' [' . implode(',', $flags) . ']' : '');
        return ['lrc' => $lrc, 'info' => $info, 'score' => $score];
    }

    /** "3:20" -> 200 detik. Return 0 bila tak bisa diparse. */
    public static function durationToSeconds(?string $duration): int
    {
        if (!$duration) return 0;
        $duration = trim($duration);
        if (is_numeric($duration)) return (int) $duration;
        $parts = array_map('intval', explode(':', $duration));
        if (count($parts) === 2) return $parts[0] * 60 + $parts[1];
        if (count($parts) === 3) return $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
        return 0;
    }
}

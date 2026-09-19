<?php

namespace App\Console\Commands;

use App\Models\Song;
use App\Support\LyricsFixer;
use Illuminate\Console\Command;

class FixLyrics extends Command
{
    protected $signature = 'lyrics:fix
        {--genre=K-Pop : Hanya lagu dengan genre ini (pakai --genre= untuk semua genre)}
        {--ids= : Batasi ke id lagu tertentu, pisahkan koma (mis. --ids=4,5,6)}
        {--force : Timpa juga lirik yang sudah ada Hangul-nya}
        {--dry-run : Tampilkan rencana tanpa mengubah database}';

    protected $description = 'Perbaiki lirik dari lrclib, utamakan teks Korea (Hangul) untuk K-Pop';

    public function handle(): int
    {
        $query = Song::query();
        if ($this->option('ids')) {
            $ids = array_filter(array_map('intval', explode(',', $this->option('ids'))));
            $query->whereIn('id', $ids);
        } elseif ($this->option('genre') !== '') {
            $query->where('genre', $this->option('genre'));
        }
        $songs = $query->orderBy('id')->get();
        if ($songs->isEmpty()) {
            $this->warn('Tidak ada lagu yang cocok.');
            return self::SUCCESS;
        }

        $dry = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $updated = $skipped = $failed = 0;

        foreach ($songs as $song) {
            $alreadyKorean = LyricsFixer::hasKorean($song->lyrics ?? '');
            if ($alreadyKorean && !$force) {
                $this->line("  [skip] #{$song->id} {$song->title} — sudah ada Hangul");
                $skipped++;
                continue;
            }
            $preferKorean = mb_strtolower($song->genre ?? '') === 'k-pop';
            $secs = LyricsFixer::durationToSeconds($song->duration);
            $got = LyricsFixer::bestSynced($song->title, $song->artist, $secs, $preferKorean);

            if ($got['lrc'] === '') {
                $this->warn("  [gagal] #{$song->id} {$song->title} — {$got['info']}");
                $failed++;
                continue;
            }
            $kr = LyricsFixer::hasKorean($got['lrc']) ? 'KR' : 'romaji';
            $lines = LyricsFixer::timestampLines($got['lrc']);
            if ($dry) {
                $this->info("  [rencana] #{$song->id} {$song->title} -> {$got['info']} [$kr, $lines baris]");
            } else {
                $song->lyrics = $got['lrc'];
                $song->save();
                $this->info("  [ok] #{$song->id} {$song->title} -> {$got['info']} [$kr, $lines baris]");
            }
            $updated++;
            sleep(1); // sopan ke API gratis
        }

        $this->newLine();
        $this->line($dry ? "Rencana (dry-run): $updated akan diperbarui, $skipped dilewati, $failed gagal."
            : "Selesai: $updated diperbarui, $skipped dilewati, $failed gagal.");
        return self::SUCCESS;
    }
}

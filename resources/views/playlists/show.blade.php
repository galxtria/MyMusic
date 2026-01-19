@extends('layouts.app')

@section('content')
<style>
    /* --- VARIABLES --- */
    .playlist-scope {
        --glass-surface: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.08);
        --accent-color: #2C74B3;
    }

    /* --- IMMERSIVE BACKGROUND --- */
    .immersive-bg {
        position: fixed; inset: 0; z-index: 0;
        background: #050b14;
        pointer-events: none;
    }
    .immersive-blur {
        position: absolute; top: -10%; left: -10%; width: 60%; height: 60%;
        background: radial-gradient(circle, rgba(44, 116, 179, 0.15) 0%, transparent 70%);
        filter: blur(80px);
    }

    .playlist-content { position: relative; z-index: 1; padding: 40px; }

    /* --- HEADER SECTION --- */
    .header-container { display: flex; align-items: flex-end; gap: 30px; margin-bottom: 40px; }
    
    .cover-wrapper {
        width: 260px; height: 260px; flex-shrink: 0;
        border-radius: 20px; overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        border: 1px solid var(--glass-border);
    }
    .cover-img { width: 100%; height: 100%; object-fit: cover; }

    .header-info { padding-bottom: 10px; }
    .badge-label {
        font-size: 0.75rem; font-weight: 800; letter-spacing: 2px;
        color: var(--accent-color); text-transform: uppercase; margin-bottom: 10px; display: block;
    }
    .playlist-title {
        font-size: clamp(2.5rem, 5vw, 4.5rem); font-weight: 900; color: white;
        letter-spacing: -2px; margin-bottom: 15px; line-height: 1;
    }
    .playlist-meta { color: rgba(255,255,255,0.6); font-weight: 600; font-size: 0.95rem; }

    /* --- ACTION BAR --- */
    .action-bar { display: flex; align-items: center; gap: 20px; margin-bottom: 40px; }
    
    .btn-play-all {
        width: 65px; height: 65px; border-radius: 50%; background: #fff;
        display: flex; align-items: center; justify-content: center;
        color: #000; font-size: 1.5rem; transition: 0.3s; border: none;
        box-shadow: 0 10px 25px rgba(255,255,255,0.2);
    }
    .btn-play-all:hover { transform: scale(1.1); box-shadow: 0 15px 35px rgba(255,255,255,0.3); }

    .btn-circle-outline {
        width: 45px; height: 45px; border-radius: 50%;
        border: 1px solid var(--glass-border); background: var(--glass-surface);
        color: white; display: flex; align-items: center; justify-content: center;
        transition: 0.3s; text-decoration: none;
    }
    .btn-circle-outline:hover { background: rgba(255,255,255,0.1); border-color: white; }

    /* --- SONG TABLE --- */
    .song-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .song-table th { color: rgba(255,255,255,0.4); font-size: 0.75rem; text-transform: uppercase; padding: 10px 20px; font-weight: 800; }
    
    .song-row { 
        background: transparent; transition: 0.3s; cursor: pointer;
        border-radius: 12px;
    }
    .song-row:hover { background: var(--glass-surface); }
    
    .song-row td { padding: 12px 20px; color: white; border-top: 1px solid transparent; border-bottom: 1px solid transparent; }
    .song-row td:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; border-left: 1px solid transparent; }
    .song-row td:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; border-right: 1px solid transparent; }
    
    .song-row:hover td { border-color: var(--glass-border); }

    .song-idx { color: rgba(255,255,255,0.4); font-family: monospace; width: 40px; }
    .song-thumb { width: 45px; height: 45px; border-radius: 8px; object-fit: cover; }
    .song-title { font-weight: 700; margin-bottom: 2px; }
    .song-artist { font-size: 0.85rem; color: rgba(255,255,255,0.5); }

    .btn-remove { 
        color: rgba(255,255,255,0.2); transition: 0.3s; background: none; border: none;
    }
    .song-row:hover .btn-remove { color: #ff4d4d; }

    .animate-enter { animation: fadeInUp 0.8s ease forwards; opacity: 0; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

</style>

<div class="playlist-scope">
    <div class="immersive-bg">
        <div class="immersive-blur"></div>
    </div>

    <div class="playlist-content container-fluid">
        {{-- Header --}}
        <div class="header-container animate-enter">
            <div class="cover-wrapper">
                <img src="{{ asset($playlist->cover_path ?? 'images/default_playlist.jpg') }}" class="cover-img">
            </div>
            <div class="header-info">
                <span class="badge-label">Personal Playlist</span>
                <h1 class="playlist-title">{{ $playlist->name }}</h1>
                <div class="playlist-meta">
                    <span class="text-white">{{ Auth::user()->name }}</span> • 
                    {{ $playlist->songs->count() }} Tracks • 
                    <span class="text-white-50 small">Created {{ $playlist->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="action-bar animate-enter" style="animation-delay: 0.1s">
            <button class="btn-play-all" onclick="playWholePlaylist()" title="Play All">
                <i class="fas fa-play"></i>
            </button>
            <a href="{{ route('playlist.edit', $playlist->id) }}" class="btn-circle-outline" title="Edit Playlist">
                <i class="fas fa-pen"></i>
            </a>
            <form action="{{ route('playlist.delete', $playlist->id) }}" method="POST" onsubmit="return confirm('Hapus playlist ini?')" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn-circle-outline" style="color: #ff4d4d;" title="Delete Playlist">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>

        {{-- Song List --}}
        <div class="animate-enter" style="animation-delay: 0.2s">
            <table class="song-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Artist</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody id="playlistTracks">
                    @forelse($playlist->songs as $index => $song)
                    <tr class="song-row queue-item" 
                        data-id="{{ $song->id }}"
                        data-title="{{ $song->title }}"
                        data-artist="{{ $song->artist }}"
                        data-src="{{ route('music.stream', ['filename' => basename($song->file_path)]) }}"
                        data-cover="{{ asset($song->album_art) }}"
                        data-lyrics="{{ $song->lyrics ?? '' }}">
                        
                        <td class="song-idx">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ asset($song->album_art) }}" class="song-thumb shadow">
                                <div>
                                    <div class="song-title">{{ $song->title }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="song-artist">{{ $song->artist }}</td>
                        <td class="text-end">
                            <form action="{{ route('playlist.removeSong', [$playlist->id, $song->id]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-remove" title="Hapus dari playlist">
                                    <i class="fas fa-minus-circle fa-lg"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="text-white-50 mb-3">Playlist ini masih kosong.</div>
                            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-light rounded-pill px-4">Cari Lagu</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>



<script>
    function playWholePlaylist() {
        const tracks = document.querySelectorAll('#playlistTracks .queue-item');
        if (tracks.length === 0) return;

        const queue = Array.from(tracks).map(tr => ({
            id: tr.dataset.id,
            title: tr.dataset.title,
            artist: tr.dataset.artist,
            src: tr.dataset.src,
            cover: tr.dataset.cover,
            lyrics: tr.dataset.lyrics
        }));

        if (window.playerState) {
            window.playerState.queue = queue;
            playSongAtIndex(0);
        }
    }

    // Klik baris lagu untuk putar
    document.querySelectorAll('.song-row').forEach((row, idx) => {
        row.addEventListener('click', function(e) {
            if (e.target.closest('button') || e.target.closest('form')) return;
            
            // Build queue
            const tracks = document.querySelectorAll('#playlistTracks .queue-item');
            window.playerState.queue = Array.from(tracks).map(tr => ({
                id: tr.dataset.id,
                title: tr.dataset.title,
                artist: tr.dataset.artist,
                src: tr.dataset.src,
                cover: tr.dataset.cover,
                lyrics: tr.dataset.lyrics
            }));
            
            playSongAtIndex(idx);
        });
    });
</script>
@endsection
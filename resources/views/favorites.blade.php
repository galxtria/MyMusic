@extends('layouts.app')

@section('content')
<style>
    .fav-scope {
        --c-fav: #ec4899; 
        --glass-border: rgba(255, 255, 255, 0.1);
        --glass-surface: rgba(15, 23, 42, 0.6);
    }
    .page-background {
        position: fixed; inset: 0; z-index: 0;
        background: #050b14;
        background-image: 
            radial-gradient(circle at 50% -20%, rgba(236, 72, 153, 0.15), transparent 50%),
            radial-gradient(circle at 0% 100%, rgba(79, 70, 229, 0.05), transparent 40%);
    }
    .content-wrapper { position: relative; z-index: 1; padding: 60px 0 150px; }
    .header-section { display: flex; align-items: flex-end; gap: 30px; margin-bottom: 40px; }
    .fav-icon-box {
        width: 180px; height: 180px; border-radius: 24px;
        background: linear-gradient(135deg, #ec4899, #8b5cf6);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 20px 50px rgba(236, 72, 153, 0.3);
    }
    .page-title {
        font-size: clamp(2.5rem, 6vw, 5rem); font-weight: 900; color: white;
        letter-spacing: -3px; line-height: 0.9; margin: 0;
    }
    .song-table-header {
        display: grid; grid-template-columns: 50px 1fr 1fr 100px 80px;
        padding: 10px 25px; color: #94a3b8; font-size: 0.75rem;
        font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
        border-bottom: 1px solid var(--glass-border); margin-bottom: 15px;
    }
    .fav-row {
        display: grid; grid-template-columns: 50px 1fr 1fr 100px 80px;
        align-items: center; padding: 12px 25px; border-radius: 16px;
        background: transparent; transition: 0.2s; cursor: pointer;
        border: 1px solid transparent; margin-bottom: 4px;
    }
    .fav-row:hover { background: rgba(255, 255, 255, 0.05); border-color: var(--glass-border); }
    .song-info { display: flex; align-items: center; gap: 15px; }
    .song-img { width: 45px; height: 45px; border-radius: 6px; object-fit: cover; }
    .title-text { color: white; font-weight: 700; margin: 0; font-size: 1rem; }
    .artist-text { color: #94a3b8; font-size: 0.85rem; margin: 0; }
    .btn-heart-active { background: transparent; border: none; color: #ec4899; font-size: 1.1rem; }
    .btn-play-row { width: 35px; height: 35px; border-radius: 50%; background: white; color: black; display: flex; align-items: center; justify-content: center; opacity: 0; transition: 0.2s; border: none; }
    .fav-row:hover .btn-play-row { opacity: 1; }
</style>

<div class="fav-scope">
    <div class="page-background"></div>
    <div class="container content-wrapper">
        <div class="header-section">
            <div class="fav-icon-box"><i class="fas fa-heart fa-5x text-white"></i></div>
            <div>
                <span class="text-white-50 fw-bold small uppercase mb-2 d-block">Playlist</span>
                <h1 class="page-title">Favorite <br> Songs</h1>
                <div class="mt-3 d-flex align-items-center gap-2">
                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}" class="rounded-circle" width="25">
                    <span class="text-white fw-bold">{{ Auth::user()->name }}</span>
                    <span class="text-white-50">• {{ $songs->count() }} lagu</span>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <button class="btn btn-light rounded-pill px-4 fw-bold shadow-lg" onclick="playFavSong(0)">
                <i class="fas fa-play me-2"></i> Play All
            </button>
        </div>

        <div class="song-table-header">
            <div>#</div><div>Judul</div><div>Genre</div><div>Durasi</div><div class="text-end pe-3"><i class="far fa-clock"></i></div>
        </div>

        <div id="favoriteList">
            @forelse($songs as $index => $song)
                <div class="fav-row queue-item" 
                     onclick="playFavSong({{ $index }})"
                     data-id="{{ $song->id }}"
                     data-src="{{ route('music.stream', ['filename' => basename($song->file_path)]) }}"
                     data-title="{{ $song->title }}"
                     data-artist="{{ $song->artist }}"
                     data-cover="{{ asset($song->album_art) }}"
                     data-lyrics="{{ $song->lyrics ?? '' }}">
                    
                    <div class="text-white-50 fw-bold">{{ $index + 1 }}</div>
                    <div class="song-info">
                        <img src="{{ asset($song->album_art) }}" class="song-img">
                        <div><p class="title-text">{{ $song->title }}</p><p class="artist-text">{{ $song->artist }}</p></div>
                    </div>
                    <div class="text-white-50 small">{{ $song->genre }}</div>
                    <div class="text-white-50 font-monospace small">{{ $song->duration }}</div>
                    <div class="d-flex justify-content-end align-items-center gap-3">
                        {{-- Class fav-btn ditambahkan di sini --}}
                        <button class="btn-heart-active fav-btn" onclick="event.stopPropagation(); removeFromFav({{ $song->id }}, this)">
                            <i class="fas fa-heart"></i>
                        </button>
                        <button class="btn-play-row"><i class="fas fa-play fa-xs"></i></button>
                    </div>
                </div>
            @empty
                <p class="text-white-50 text-center py-5">Belum ada lagu favorit.</p>
            @endforelse
        </div>
    </div>
</div>

<script>
    // 1. GUNAKAN window. AGAR TETAP AKTIF SAAT PINDAH HALAMAN AJAX
    window.playFavSong = function(idx) {
        const items = document.querySelectorAll('#favoriteList .queue-item');
        if(window.loadQueue) {
            window.loadQueue(Array.from(items), idx);
        }
    };

    window.removeFromFav = function(songId, element) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/favorites/toggle/${songId}`, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': token, 
                'Accept': 'application/json' 
            }
        })
        .then(response => response.json())
        .then(data => {
            // Hapus baris dari daftar favorit dengan animasi
            const row = element.closest('.fav-row');
            row.style.transition = '0.3s';
            row.style.opacity = '0';
            row.style.transform = 'translateX(20px)';
            
            setTimeout(() => {
                row.remove();
                // Opsional: Update counter jumlah lagu di UI
            }, 300);

            // Sinkronisasi Ikon di Player Bar jika lagu yang dihapus sedang diputar
            const audio = document.getElementById('audioPlayer');
            const playerLikeIcon = document.querySelector('#playerLikeBtn i');
            
            // Jika ID lagu yang dihapus sama dengan lagu di antrean player yang sedang aktif
            if(window.playerState && window.playerState.queue[window.playerState.currentIndex]?.id == songId) {
                if(playerLikeIcon) playerLikeIcon.className = 'far fa-heart';
            }
        });
    };
</script>
@endsection
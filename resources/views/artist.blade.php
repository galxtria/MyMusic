@extends('layouts.app')

@section('content')

<style>
    /* --- SCOPED VARIABLES (ARTIST) --- */
    .artist-scope {
        --c-deep: #050b14;
        --c-accent-1: #06b6d4; 
        --c-accent-2: #3b82f6; 
        --glass-border: rgba(255, 255, 255, 0.08);
        --glass-surface: rgba(255, 255, 255, 0.03);
        --glass-hover: rgba(255, 255, 255, 0.08);
    }

    /* --- ANIMATIONS --- */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-enter { animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; opacity: 0; }
    .d-1 { animation-delay: 0.1s; }

    /* --- BACKGROUND --- */
    .page-background {
        position: fixed; inset: 0; z-index: 0;
        background: #050b14;
        background-image: 
            radial-gradient(circle at 10% 20%, rgba(6, 182, 212, 0.15), transparent 40%), 
            radial-gradient(circle at 90% 80%, rgba(59, 130, 246, 0.15), transparent 40%);
    }

    .artist-content-wrapper { position: relative; z-index: 1; }

    /* --- HERO HEADER --- */
    .hero-glass {
        position: relative; overflow: hidden; border-radius: 32px;
        background: rgba(255, 255, 255, 0.02); border: 1px solid var(--glass-border);
        padding: 40px; display: flex; align-items: center; gap: 40px;
        backdrop-filter: blur(30px); box-shadow: 0 40px 80px rgba(0,0,0,0.5);
        margin-top: 40px; margin-bottom: 40px;
    }

    .hero-img-box { position: relative; width: 200px; height: 200px; flex-shrink: 0; }
    .hero-img {
        width: 100%; height: 100%; border-radius: 50%; object-fit: cover;
        border: 4px solid rgba(255,255,255,0.1); position: relative; z-index: 2;
    }
    .hero-img-box::before {
        content: ''; position: absolute; inset: -10px; border-radius: 50%;
        background: linear-gradient(45deg, var(--c-accent-1), var(--c-accent-2));
        opacity: 0.6; filter: blur(20px); z-index: 1;
    }

    .hero-title { 
        font-size: clamp(3rem, 5vw, 5rem); font-weight: 900; line-height: 1; margin: 0;
        letter-spacing: -2px; color: white;
    }

    .hero-actions { display: flex; align-items: center; gap: 20px; margin-top: 25px; }

    .hero-play-btn {
        width: 60px; height: 60px; background: white; color: black;
        border-radius: 50%; border: none; font-size: 1.5rem;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; box-shadow: 0 0 30px rgba(255,255,255,0.3);
        transition: 0.3s;
    }
    .hero-play-btn:hover { transform: scale(1.1); }

    /* --- SONG STRIP --- */
    .song-strip {
        display: flex; align-items: center;
        padding: 12px 20px; border-radius: 12px;
        background: transparent; border: 1px solid transparent;
        transition: 0.2s; cursor: pointer;
    }
    .song-strip:hover { background: var(--glass-hover); border-color: var(--glass-border); }
    
    .s-img-box { position: relative; width: 45px; height: 45px; margin: 0 20px; border-radius: 8px; overflow: hidden; flex-shrink: 0; }
    .s-img { width: 100%; height: 100%; object-fit: cover; }

    .s-info { flex-grow: 1; min-width: 0; }
    .s-title { color: white; font-weight: 600; font-size: 1rem; margin-bottom: 2px; }
    .s-plays { color: #94a3b8; font-size: 0.85rem; }

    .text-pink { color: #ec4899 !important; }
    .fav-btn { transition: 0.2s; cursor: pointer; padding: 10px; }
    .fav-btn:hover { transform: scale(1.2); }

    /* Visualizer */
    .visualizer-mini { display: flex; gap: 3px; align-items: flex-end; height: 15px; width: 15px; margin-right: 15px; opacity: 0; }
    .song-strip:hover .visualizer-mini { opacity: 1; }
    .v-bar { width: 3px; background: var(--c-accent-1); animation: bounce 1s infinite; }
    @keyframes bounce { 0%, 100% { height: 40%; } 50% { height: 100%; } }
</style>

<div class="artist-scope">
    <div class="page-background"></div>

    @php
        $favoriteIds = auth()->user() ? auth()->user()->favoriteSongs->pluck('id')->toArray() : [];
    @endphp

    <div class="container pb-5 artist-content-wrapper">

        <div class="hero-glass animate-enter">
            <div class="hero-img-box">
                <img src="{{ $artistInfo->artist_image ?? asset($artistInfo->album_art) }}" class="hero-img">
            </div>

            <div class="hero-info">
                <div class="verified-badge" style="color: var(--c-accent-1); font-size: 0.8rem; font-weight: 700;">
                    <i class="fas fa-check-circle"></i> VERIFIED ARTIST
                </div>
                <h1 class="hero-title">{{ $artistName }}</h1>
                <div class="hero-stats" style="color: #94a3b8; margin-top: 10px;">
                    {{ number_format(rand(100000, 5000000)) }} Monthly Listeners • {{ $songs->count() }} Tracks
                </div>

                <div class="hero-actions">
                    <button class="hero-play-btn" onclick="playArtistSong(0)">
                        <i class="fas fa-play ms-1"></i>
                    </button>
                    <button class="btn btn-outline-light rounded-pill px-4 fw-bold">FOLLOW</button>
                </div>
            </div>
        </div>

        <div class="animate-enter d-1">
            <h3 style="color: white; font-weight: 800; margin-bottom: 20px;">Popular Tracks</h3>
            
            <div class="song-list-wrapper">
                @foreach($songs as $index => $song)
                <div class="song-strip queue-item"
                    onclick="playArtistSong({{ $index }})"
                    data-id="{{ $song->id }}"
                    data-src="{{ route('music.stream', ['filename' => basename($song->file_path)]) }}"
                    data-title="{{ $song->title }}"
                    data-artist="{{ $song->artist }}"
                    data-cover="{{ asset($song->album_art) }}"
                    data-lyrics="{{ $song->lyrics ?? '' }}">
                    
                    <div style="width: 30px; color: #64748b; font-family: monospace;">{{ $index + 1 }}</div>
                    
                    <div class="s-img-box">
                        <img src="{{ asset($song->album_art) }}" class="s-img">
                    </div>
                    
                    <div class="s-info">
                        <div class="s-title text-truncate">{{ $song->title }}</div>
                        <div class="s-plays">{{ number_format(rand(10000, 900000)) }} plays</div>
                    </div>

                    <div class="visualizer-mini">
                        <div class="v-bar" style="height: 60%;"></div>
                        <div class="v-bar" style="height: 100%;"></div>
                        <div class="v-bar" style="height: 40%;"></div>
                    </div>

                    <div style="color: #94a3b8; font-family: monospace; margin-right: 20px;">{{ $song->duration ?? '3:45' }}</div>
                    
                    <div class="fav-btn" onclick="event.stopPropagation(); toggleFavorite({{ $song->id }}, this)">
                        <i class="{{ in_array($song->id, $favoriteIds) ? 'fas text-pink' : 'far' }} fa-heart text-white-50"></i>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <div style="height: 150px;"></div>
    </div>
</div>

<script>
    // Versi Global agar tetap aktif setelah pindah page (AJAX)
    window.toggleFavorite = function(songId, element) {
        fetch(`/favorites/toggle/${songId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const icon = element.querySelector('i');
            icon.className = (data.status === 'liked') ? 'fas fa-heart text-pink' : 'far fa-heart text-white-50';
        });
    }

    window.playArtistSong = function(idx) {
        const items = document.querySelectorAll('.queue-item');
        if (window.loadQueue) window.loadQueue(Array.from(items), idx);
    }
</script>

@endsection
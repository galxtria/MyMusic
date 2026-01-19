@extends('layouts.app')

@section('content')

<style>
    /* --- SCOPED VARIABLES --- */
    .home-scope {
        --c-deep: #050b14;
        --c-accent-1: #4f46e5;
        --c-accent-2: #06b6d4;
        --c-accent-3: #ec4899;
        --glass-border: rgba(255, 255, 255, 0.08);
        --glass-surface: rgba(255, 255, 255, 0.03);
    }

    /* --- ANIMATIONS --- */
    @keyframes gradientMove {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-enter { animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; opacity: 0; }
    .d-1 { animation-delay: 0.1s; }
    .d-2 { animation-delay: 0.2s; }
    .d-3 { animation-delay: 0.3s; }
    .d-4 { animation-delay: 0.4s; }

    /* --- BACKGROUND --- */
    .page-background {
        position: fixed; inset: 0; z-index: 0;
        background: #050b14;
        background-image: 
            radial-gradient(circle at 10% 20%, rgba(44, 116, 179, 0.1), transparent 40%), 
            radial-gradient(circle at 90% 80%, rgba(79, 70, 229, 0.1), transparent 40%);
    }

    .home-content-wrapper { position: relative; z-index: 1; }

    .big-greeting {
        font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 900; letter-spacing: -2px; line-height: 1;
        background: linear-gradient(to right, #fff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 20px;
    }

    .filter-bar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 30px; }
    .filter-chip {
        padding: 8px 20px; border-radius: 50px; background: rgba(255,255,255,0.05);
        border: 1px solid var(--glass-border); color: #fff; font-size: 0.9rem; font-weight: 600;
        cursor: pointer; transition: 0.3s; text-decoration: none;
    }
    .filter-chip:hover, .filter-chip.active {
        background: white; color: black; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255,255,255,0.2);
    }

    /* --- 3D TILT HERO --- */
    .tilt-card { transform-style: preserve-3d; transform: perspective(1000px); }
    .hero-glass {
        position: relative; overflow: hidden; border-radius: 32px;
        background: rgba(255, 255, 255, 0.02); border: 1px solid var(--glass-border);
        padding: 40px; display: flex; align-items: center; justify-content: space-between;
        backdrop-filter: blur(20px); box-shadow: 0 40px 80px rgba(0,0,0,0.5);
        transition: transform 0.1s ease-out;
    }
    
    .hero-content-layer { transform: translateZ(50px); }
    .hero-img-layer { transform: translateZ(80px); }

    .hero-album-art {
        width: 280px; height: 280px; border-radius: 20px; object-fit: cover;
        box-shadow: -20px 20px 50px rgba(0,0,0,0.6);
    }

    .hero-btn-play {
        background: white; color: black; width: 60px; height: 60px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
        cursor: pointer; box-shadow: 0 0 30px rgba(255,255,255,0.4); transition: 0.3s;
    }
    .hero-btn-play:hover { transform: scale(1.1); background: #f8f9fa; }

    /* --- VIBE CARDS --- */
    .vibe-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-bottom: 40px; }
    .vibe-card {
        height: 100px; border-radius: 16px; position: relative; overflow: hidden;
        display: flex; align-items: center; justify-content: center;
        text-decoration: none; border: 1px solid rgba(255,255,255,0.1);
        transition: 0.3s; cursor: pointer;
    }
    .vibe-card:hover { transform: translateY(-5px) scale(1.02); }
    .vibe-bg { position: absolute; inset: 0; opacity: 0.6; transition: 0.3s; background-size: 200% 200%; animation: gradientMove 5s ease infinite; }
    .vibe-title { position: relative; z-index: 2; font-weight: 800; font-size: 1.1rem; color: white; text-transform: uppercase; letter-spacing: 1px; text-shadow: 0 2px 10px rgba(0,0,0,0.5); }

    .v-relax { background: linear-gradient(45deg, #4f46e5, #818cf8); }
    .v-energy { background: linear-gradient(45deg, #f59e0b, #ef4444); }
    .v-focus { background: linear-gradient(45deg, #06b6d4, #3b82f6); }
    .v-kpop { background: linear-gradient(45deg, #ff00cc, #333399); } 

    /* --- ARTIST RINGS --- */
    .artist-ring-wrapper { display: flex; flex-direction: column; align-items: center; margin-right: 25px; cursor: pointer; transition: 0.3s; text-decoration: none; flex-shrink: 0; }
    .artist-ring-wrapper:hover { transform: translateY(-5px); }
    .artist-ring { width: 80px; height: 80px; border-radius: 50%; padding: 3px; background: linear-gradient(45deg, #fff, #94a3b8); margin-bottom: 10px; }
    .artist-photo { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 3px solid #050b14; }
    .artist-name-small { font-size: 0.75rem; font-weight: 600; color: #cbd5e1; text-align: center; }

    /* --- SONG CARD --- */
    .card-ultra {
        background: var(--glass-surface); border: 1px solid var(--glass-border);
        border-radius: 20px; padding: 15px; transition: 0.4s; overflow: hidden; position: relative; cursor: pointer;
    }
    .card-ultra:hover { background: rgba(255,255,255,0.08); transform: translateY(-8px); border-color: rgba(255,255,255,0.2); }
    .card-img-container { position: relative; overflow: hidden; border-radius: 12px; margin-bottom: 12px; aspect-ratio: 1/1; }
    .card-img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
    .card-ultra:hover .card-img { transform: scale(1.05); filter: brightness(0.6); }
    
    .card-play-btn {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.5);
        width: 45px; height: 45px; background: white; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; color: black;
        opacity: 0; transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .card-ultra:hover .card-play-btn { opacity: 1; transform: translate(-50%, -50%) scale(1); } 
    .text-pink { color: #ec4899 !important; }

    /* --- PAGINATION MODERN STYLES --- */
    .pagination-wrapper { display: flex; justify-content: center; }
    .pag-btn {
        width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;
        background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px; color: #94a3b8; text-decoration: none; font-weight: 700;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); backdrop-filter: blur(10px);
    }
    .pag-btn:hover:not(.disabled):not(.active) {
        background: rgba(255, 255, 255, 0.1); color: #fff; border-color: rgba(255, 255, 255, 0.2); transform: translateY(-3px);
    }
    .pag-btn.active { background: #fff; color: #000; border-color: #fff; box-shadow: 0 10px 20px rgba(255, 255, 255, 0.15); }
    .pag-btn.disabled { opacity: 0.3; cursor: not-allowed; }
    .pag-btn.prev-next { background: rgba(255, 255, 255, 0.06); color: #fff; }
    .pag-info { color: #94a3b8; font-weight: 600; padding: 0 15px; }

    /* --- ADD TO PLAYLIST BTN --- */
    .add-playlist-btn {
        color: rgba(255,255,255,0.4); transition: 0.3s; cursor: pointer;
    }
    .add-playlist-btn:hover { color: #fff; transform: scale(1.2); }
</style>

<div class="home-scope">
    <div class="page-background"></div>

    @php
        $hour = date('H');
        $greeting = $hour < 12 ? 'Good Morning' : ($hour < 18 ? 'Good Afternoon' : 'Good Evening');
        $featured = $songs->first(); 
        $favoriteIds = auth()->user() ? auth()->user()->favoriteSongs->pluck('id')->toArray() : [];
    @endphp

    <div class="container pb-5 home-content-wrapper">

        <div class="pt-4 mb-4 animate-enter">
            <h1 class="big-greeting">{{ $greeting }}, {{ Auth::user()->name }}</h1>
            
            <div class="filter-bar">
                <a href="#" class="filter-chip active">All</a>
                <a href="{{ route('search', ['q' => 'Relax']) }}" class="filter-chip">Relax</a>
                <a href="{{ route('search', ['q' => 'Rock']) }}" class="filter-chip">Energize</a>
                <a href="{{ route('search', ['q' => 'K-Pop']) }}" class="filter-chip">K-Wave</a>
                <a href="{{ route('search', ['q' => 'Jazz']) }}" class="filter-chip">Jazz</a>
            </div>
        </div>

        @if($featured)
        <div class="tilt-card animate-enter d-1" id="heroTilt">
            <div class="hero-glass queue-item" 
                 data-id="{{ $featured->id }}"
                 data-src="{{ route('music.stream', ['filename' => basename($featured->file_path)]) }}"
                 data-title="{{ $featured->title }}"
                 data-artist="{{ $featured->artist }}"
                 data-cover="{{ asset($featured->album_art) }}"
                 data-lyrics="{{ $featured->lyrics ?? '' }}">
                <div class="row align-items-center w-100 m-0 hero-content-layer">
                    <div class="col-lg-7 ps-lg-4">
                        <div class="d-inline-flex align-items-center gap-2 mb-3 px-3 py-1 rounded-pill" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.1);">
                            <i class="fas fa-fire text-warning"></i> 
                            <span class="text-white fw-bold" style="font-size: 0.75rem; letter-spacing: 1px;">TRENDING NOW</span>
                        </div>
                        
                        <h2 class="display-4 fw-bold text-white mb-2" style="letter-spacing: -1px;">{{ $featured->title }}</h2>
                        
                        <div class="d-flex align-items-center gap-3 mb-4 text-white-50">
                            <img src="{{ $featured->artist_image ?? $featured->album_art }}" class="rounded-circle" width="30" height="30" style="object-fit: cover;">
                            <span class="fw-bold text-white">{{ $featured->artist }}</span>
                            <span>•</span>
                            <span>{{ $featured->genre ?? 'Music' }}</span>
                        </div>
                        
                        <div class="d-flex align-items-center gap-4">
                            <div class="hero-btn-play" onclick="playHeroTrack(0)">
                                <i class="fas fa-play ms-1"></i>
                            </div>

                            <button onclick="event.stopPropagation(); toggleFavorite({{ $featured->id }}, this)" class="btn btn-outline-light rounded-pill px-4 py-2 fw-bold" style="border-color: rgba(255,255,255,0.2);">
                                <i class="{{ in_array($featured->id, $favoriteIds) ? 'fas text-pink' : 'far' }} fa-heart me-2"></i> 
                                <span class="fav-text">{{ in_array($featured->id, $favoriteIds) ? 'Saved' : 'Save' }}</span>
                            </button>
                            
                            {{-- TOMBOL + HERO --}}
                            <div onclick="event.stopPropagation(); window.setTargetSong({{ $featured->id }})" 
                                 data-bs-toggle="modal" 
                                 data-bs-target="#addToPlaylistModal"
                                 class="add-playlist-btn fs-4" style="cursor: pointer;">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5 d-none d-lg-flex justify-content-center hero-img-layer">
                        <img src="{{ asset($featured->album_art) }}" class="hero-album-art" alt="Album Cover">
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- VIBE CHECK SECTION --}}
        <div class="mt-5 pt-2 animate-enter d-2">
            <h5 class="text-white fw-bold mb-3 px-1">Vibe Check</h5>
            <div class="vibe-grid">
                <a href="{{ route('search', ['q' => 'Relax']) }}" class="vibe-card">
                    <div class="vibe-bg v-relax"></div>
                    <div class="vibe-title">Chill</div>
                </a>
                <a href="{{ route('search', ['q' => 'Rock']) }}" class="vibe-card">
                    <div class="vibe-bg v-energy"></div>
                    <div class="vibe-title">Energy</div>
                </a>
                <a href="{{ route('search', ['q' => 'Jazz']) }}" class="vibe-card">
                    <div class="vibe-bg v-focus"></div>
                    <div class="vibe-title">Focus</div>
                </a>
                <a href="{{ route('search', ['q' => 'K-Pop']) }}" class="vibe-card">
                    <div class="vibe-bg v-kpop"></div>
                    <div class="vibe-title">K-Wave</div>
                </a>
            </div>
        </div>

        {{-- POPULAR ARTISTS SECTION --}}
        <div class="mt-4 animate-enter d-3">
            <h5 class="text-white fw-bold mb-3 px-1">Popular Artists</h5>
            <div class="d-flex overflow-auto pb-3" style="scrollbar-width: none;">
                @foreach($songs->unique('artist')->take(10) as $s)
                <a href="{{ route('artist.show', ['name' => $s->artist]) }}" class="artist-ring-wrapper">
                    <div class="artist-ring">
                        <img src="{{ $s->artist_image ?? $s->album_art }}" class="artist-photo">
                    </div>
                    <span class="artist-name-small">{{ Str::limit($s->artist, 10) }}</span>
                </a>
                @endforeach
            </div>
        </div>

        {{-- QUICK PICKS & DISCOVER GRID --}}
        <div class="mt-4 animate-enter d-4">
            <div class="d-flex justify-content-between align-items-end mb-3 px-1">
                <h5 class="text-white fw-bold mb-0">Quick Picks</h5>
                <a href="#" class="text-white-50 text-decoration-none small fw-bold">SEE ALL</a>
            </div>

            <div id="paginationContainer">
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3" id="discoverGrid">
                    @foreach($songs as $index => $song)
                    <div class="col">
                        <div class="card-ultra queue-item"
                            onclick="playQuickPick({{ $index }})"
                            data-id="{{ $song->id }}"
                            data-index="{{ $index }}"
                            data-src="{{ route('music.stream', ['filename' => basename($song->file_path)]) }}"
                            data-title="{{ $song->title }}"
                            data-artist="{{ $song->artist }}"
                            data-cover="{{ asset($song->album_art) }}"
                            data-lyrics="{{ $song->lyrics ?? '' }}">
                            
                            <div class="card-img-container">
                                <img src="{{ asset($song->album_art) }}" class="card-img">
                                <div class="card-play-btn">
                                    <i class="fas fa-play ps-1"></i>
                                </div>
                            </div>
                            
                            <div class="px-1 d-flex justify-content-between align-items-center">
                                <div class="overflow-hidden">
                                    <h6 class="text-white fw-bold mb-1 text-truncate" style="font-size: 0.95rem;">{{ $song->title }}</h6>
                                    <span class="text-white-50 small hover-white">{{ $song->artist }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    {{-- TOMBOL + QUICK PICK --}}
                                    <div onclick="event.stopPropagation(); window.setTargetSong({{ $song->id }})" 
                                         data-bs-toggle="modal" 
                                         data-bs-target="#addToPlaylistModal"
                                         class="add-playlist-btn" style="cursor: pointer; font-size: 0.9rem;">
                                        <i class="fas fa-plus-circle"></i>
                                    </div>
                                    <div onclick="event.stopPropagation(); toggleFavorite({{ $song->id }}, this)" style="cursor: pointer;">
                                        <i class="{{ in_array($song->id, $favoriteIds) ? 'fas text-pink' : 'far' }} fa-heart text-white-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- PAGINATION SECTION (RESTORED) --}}
                <div class="pagination-wrapper mt-5 mb-5 animate-enter d-4">
                    <div class="pagination-modern d-flex align-items-center justify-content-center gap-2">
                        @if ($songs->onFirstPage())
                            <span class="pag-btn disabled"><i class="fas fa-chevron-left"></i></span>
                        @else
                            <a href="{{ $songs->previousPageUrl() }}" class="pag-btn prev-next"><i class="fas fa-chevron-left"></i></a>
                        @endif

                        <div class="d-none d-md-flex gap-2">
                            @foreach ($songs->getUrlRange(max(1, $songs->currentPage() - 2), min($songs->lastPage(), $songs->currentPage() + 2)) as $page => $url)
                                <a href="{{ $url }}" class="pag-btn num {{ $page == $songs->currentPage() ? 'active' : '' }}">
                                    {{ $page }}
                                </a>
                            @endforeach
                        </div>

                        <span class="pag-info d-md-none">
                            {{ $songs->currentPage() }} / {{ $songs->lastPage() }}
                        </span>

                        @if ($songs->hasMorePages())
                            <a href="{{ $songs->nextPageUrl() }}" class="pag-btn prev-next"><i class="fas fa-chevron-right"></i></a>
                        @else
                            <span class="pag-btn disabled"><i class="fas fa-chevron-right"></i></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div style="height: 150px;"></div>
    </div>
</div>

<script>
    // 1. Logic 3D Tilt Hero
    window.initHomeTilt = function() {
        const heroCard = document.getElementById('heroTilt');
        const heroGlass = document.querySelector('.hero-glass');
        if(heroCard && heroGlass) {
            heroCard.addEventListener('mousemove', (e) => {
                const rect = heroCard.getBoundingClientRect();
                const rotateX = ((e.clientY - rect.top - rect.height/2) / rect.height/2) * -5; 
                const rotateY = ((e.clientX - rect.left - rect.width/2) / rect.width/2) * 5;
                heroGlass.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            });
            heroCard.addEventListener('mouseleave', () => heroGlass.style.transform = `rotateX(0deg) rotateY(0deg)`);
        }
    };
    window.initHomeTilt();

    // 2. Favorite Toggle AJAX
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
            const isLiked = data.status === 'liked';
            const icon = element.querySelector('i');
            icon.className = isLiked ? 'fas fa-heart text-pink' : 'far fa-heart text-white-50';
            
            // Sync dengan Player Bar
            if (window.playerState && window.playerState.queue[window.playerState.currentIndex]?.id == songId) {
                const playerLikeIcon = document.querySelector('#playerLikeBtn i');
                if (playerLikeIcon) {
                    playerLikeIcon.className = isLiked ? 'fas fa-heart text-pink' : 'far fa-heart';
                    playerLikeIcon.style.color = isLiked ? '#ec4899' : '';
                }
            }
            
            // Tampilkan notifikasi jika window.showToast tersedia di app.blade.php
            if (typeof window.showToast === 'function') {
                window.showToast(isLiked ? "Added to Favorites" : "Removed from Favorites");
            }
        });
    };

    // 3. Player Logic & Queue Management
    function mapSongFromElement(el) {
        return {
            id: el.dataset.id, title: el.dataset.title, artist: el.dataset.artist,
            src: el.dataset.src, cover: el.dataset.cover, lyrics: el.dataset.lyrics
        };
    }

    window.playHeroTrack = function(index) {
        const heroItem = document.querySelector('.hero-glass');
        const gridItems = Array.from(document.querySelectorAll('#discoverGrid .queue-item'));
        window.playerState.queue = [heroItem, ...gridItems].map(mapSongFromElement);
        if(typeof playSongAtIndex === 'function') playSongAtIndex(0);
    };

    window.playQuickPick = function(index) {
        const heroItem = document.querySelector('.hero-glass');
        const gridItems = Array.from(document.querySelectorAll('#discoverGrid .queue-item'));
        window.playerState.queue = heroItem ? [heroItem, ...gridItems].map(mapSongFromElement) : gridItems.map(mapSongFromElement);
        if(typeof playSongAtIndex === 'function') playSongAtIndex(heroItem ? index + 1 : index);
    };

    // 4. AJAX Pagination Logic (Safe Restore)
    document.addEventListener('click', function (e) {
        const pagBtn = e.target.closest('.pag-btn:not(.disabled):not(.active)');
        if (pagBtn && pagBtn.tagName === 'A') {
            e.preventDefault();
            const url = pagBtn.href;
            const container = document.getElementById('paginationContainer');
            if(!container) return;

            document.getElementById('discoverGrid').style.opacity = '0.5';

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('paginationContainer');
                    if (newContent) {
                        container.innerHTML = newContent.innerHTML;
                        document.getElementById('discoverGrid').style.opacity = '1';
                        document.getElementById('discoverGrid').scrollIntoView({ behavior: 'smooth', block: 'start' });
                        window.initHomeTilt();
                    }
                })
                .catch(err => console.error("Pagination Error:", err));
        }
    });
</script>
@endsection
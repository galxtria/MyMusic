@extends('layouts.app')

@section('content')

<style>
    /* --- SCOPED VARIABLES (SEARCH) --- */
    .search-scope {
        --c-deep: #050b14;
        --c-accent-1: #4f46e5;
        --c-accent-2: #06b6d4;
        --c-accent-3: #ec4899;
        --glass-border: rgba(255, 255, 255, 0.08);
        --glass-surface: rgba(255, 255, 255, 0.03);
    }

    /* --- ANIMATIONS --- */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-enter { animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; opacity: 0; }
    .d-1 { animation-delay: 0.1s; }
    .d-2 { animation-delay: 0.2s; }
    .d-3 { animation-delay: 0.3s; }

    /* --- BACKGROUND --- */
    .page-background {
        position: fixed; inset: 0; z-index: 0;
        background: #050b14;
        background-image: 
            radial-gradient(circle at 80% 20%, rgba(6, 182, 212, 0.1), transparent 40%), 
            radial-gradient(circle at 20% 80%, rgba(79, 70, 229, 0.1), transparent 40%);
    }

    .search-content-wrapper { position: relative; z-index: 1; }

    /* --- SEARCH HERO --- */
    .search-hero { position: relative; padding: 60px 0 40px 0; text-align: center; }
    
    .search-input-wrapper {
        position: relative; max-width: 700px; margin: 0 auto;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        border-radius: 50px;
        backdrop-filter: blur(20px);
        transition: all 0.3s ease;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    }
    .search-input-wrapper:focus-within {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.3);
        box-shadow: 0 20px 60px rgba(79, 70, 229, 0.2);
        transform: scale(1.02);
    }

    .glass-input {
        width: 100%; background: transparent; border: none;
        padding: 25px 70px 25px 35px; color: white; font-size: 1.3rem; font-weight: 600;
        outline: none;
    }
    .glass-input::placeholder { color: rgba(255,255,255,0.3); }

    .search-btn-absolute {
        position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
        width: 60px; height: 60px; background: white; color: black;
        border-radius: 50%; border: none; font-size: 1.2rem;
        display: flex; align-items: center; justify-content: center;
        transition: 0.3s; cursor: pointer;
    }
    .search-btn-absolute:hover { transform: translateY(-50%) scale(1.1); box-shadow: 0 0 20px rgba(255,255,255,0.5); }

    /* --- SECTION TITLES --- */
    .section-header { margin-bottom: 25px; border-bottom: 1px solid var(--glass-border); padding-bottom: 15px; }
    .section-title { font-size: 1.5rem; font-weight: 800; color: white; margin: 0; }

    /* --- ARTIST CARD (GLASS STYLE) --- */
    .artist-glass-card {
        background: var(--glass-surface);
        border: 1px solid var(--glass-border);
        border-radius: 24px; padding: 25px;
        display: flex; flex-direction: column; align-items: center;
        text-decoration: none; transition: 0.4s ease;
        position: relative; overflow: hidden;
    }
    .artist-glass-card:hover {
        background: rgba(255,255,255,0.08);
        transform: translateY(-10px);
        border-color: rgba(255,255,255,0.2);
    }
    
    .artist-img-wrapper {
        width: 120px; height: 120px; border-radius: 50%; padding: 4px;
        background: linear-gradient(45deg, var(--c-accent-1), var(--c-accent-3));
        margin-bottom: 20px;
        transition: 0.3s;
    }
    .artist-img-circle { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 4px solid #050b14; }
    
    .artist-name { color: white; font-weight: 700; font-size: 1.1rem; text-align: center; }
    .artist-badge { 
        margin-top: 10px; background: rgba(255,255,255,0.1); 
        padding: 5px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; color: #cbd5e1;
    }

    /* --- ULTRA SONG CARD (SAME AS HOME) --- */
    .card-ultra {
        position: relative; background: var(--glass-surface);
        border: 1px solid var(--glass-border); border-radius: 20px;
        padding: 15px; transition: all 0.4s ease; overflow: hidden; cursor: pointer;
    }
    .card-ultra:hover {
        background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.2);
        transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    }
    .card-img-container {
        position: relative; border-radius: 15px; overflow: hidden;
        aspect-ratio: 1/1; margin-bottom: 15px;
    }
    .card-img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
    .card-ultra:hover .card-img { transform: scale(1.1); filter: brightness(0.7); }
    
    .card-play-btn {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.5);
        width: 50px; height: 50px; background: rgba(255,255,255,0.3);
        backdrop-filter: blur(5px); border-radius: 50%; border: 1px solid rgba(255,255,255,0.5);
        display: flex; align-items: center; justify-content: center;
        color: white; opacity: 0; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .card-ultra:hover .card-play-btn { opacity: 1; transform: translate(-50%, -50%) scale(1); }
    .card-play-btn:hover { background: white; color: black; }

    /* --- GENRE CARDS --- */
    .genre-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px; }
    .genre-card {
        height: 120px; border-radius: 20px; padding: 20px; position: relative;
        overflow: hidden; cursor: pointer; transition: 0.3s; border: 1px solid rgba(255,255,255,0.1);
        text-decoration: none;
    }
    .genre-card:hover { transform: scale(1.03); border-color: rgba(255,255,255,0.3); }
    .genre-title { font-size: 1.4rem; font-weight: 800; color: white; position: relative; z-index: 2; text-shadow: 0 4px 10px rgba(0,0,0,0.3); }
    
    .g-pop { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
    .g-rock { background: linear-gradient(135deg, #ef4444, #b91c1c); }
    .g-jazz { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .g-indie { background: linear-gradient(135deg, #10b981, #059669); }
    .g-kpop { background: linear-gradient(135deg, #ec4899, #be185d); }
    .g-dangdut { background: linear-gradient(135deg, #f97316, #c2410c); }
    .g-relax { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .g-electronic { background: linear-gradient(135deg, #06b6d4, #0891b2); }
    
    .genre-shape {
        position: absolute; bottom: -20px; right: -20px; width: 90px; height: 90px;
        background: rgba(255,255,255,0.2); border-radius: 50%; transform: rotate(25deg);
        filter: blur(5px);
    }
</style>

<div class="search-scope">
    
    <div class="page-background"></div>

    <div class="container pb-5 search-content-wrapper">

        <div class="search-hero animate-enter">
            <h1 class="text-white fw-900 mb-4" style="font-size: 3rem; text-shadow: 0 4px 20px rgba(0,0,0,0.5);">Explore</h1>
            
            <form action="{{ route('search') }}" method="GET">
                <div class="search-input-wrapper">
                    <input type="text" name="q" class="glass-input" 
                           placeholder="What do you want to listen to?" 
                           value="{{ $query ?? '' }}" autocomplete="off" autofocus>
                    <button type="submit" class="search-btn-absolute">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        @if(isset($query) && $query != "")
            
            <div class="mt-5 animate-enter d-1">
                
                @if($artists->count() > 0)
                <div class="mb-5">
                    <div class="section-header">
                        <h3 class="section-title">Artists</h3>
                    </div>
                    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-4">
                        @foreach($artists as $artist)
                        <div class="col">
                            <a href="{{ route('artist.show', ['name' => $artist->artist]) }}" class="artist-glass-card">
                                <div class="artist-img-wrapper">
                                    <img src="{{ $artist->artist_image ?? $artist->album_art ?? asset('images/default_artist.jpg') }}" 
                                         class="artist-img-circle" alt="{{ $artist->artist }}">
                                </div>
                                <div class="artist-name">{{ $artist->artist }}</div>
                                <span class="artist-badge">Artist</span>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($songs->count() > 0)
                <div class="mb-5 animate-enter d-2">
                    <div class="section-header">
                        <h3 class="section-title">Songs</h3>
                    </div>

                    <div id="searchResultContainer" class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-4">
                        @foreach($songs as $index => $song)
                        <div class="col">
                            <div class="card-ultra play-song-btn queue-item"
                                 onclick="playSearchSong({{ $index }})"
                                 data-index="{{ $index }}"
                                 data-src="{{ asset($song->file_path) }}"
                                 data-title="{{ $song->title }}"
                                 data-artist="{{ $song->artist }}"
                                 data-cover="{{ $song->album_art }}"
                                 data-lyrics="{{ $song->lyrics ?? '' }}">
                                
                                <div class="card-img-container">
                                    <img src="{{ $song->album_art }}" class="card-img">
                                    <div class="card-play-btn">
                                        <i class="fas fa-play ps-1"></i>
                                    </div>
                                </div>

                                <h6 class="text-white fw-bold mb-1 text-truncate">{{ $song->title }}</h6>
                                
                                <a href="{{ route('artist.show', ['name' => $song->artist]) }}" 
                                   class="text-white-50 text-decoration-none small hover-white"
                                   onclick="event.stopPropagation();">
                                   {{ $song->artist }}
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($songs->count() == 0 && $artists->count() == 0)
                <div class="text-center py-5">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-10 rounded-circle mb-4" style="width: 100px; height: 100px;">
                        <i class="fas fa-search fa-3x text-white-50"></i>
                    </div>
                    <h3 class="text-white fw-bold">No results found.</h3>
                    <p class="text-white-50">Try searching for a different keyword or genre.</p>
                </div>
                @endif
            </div>

        @else
            <div class="mt-5 animate-enter d-1">
                <div class="section-header"><h3 class="section-title">Browse All</h3></div>
                <div class="genre-grid">
                    <a href="{{ route('search', ['q' => 'Pop']) }}" class="genre-card g-pop"><div class="genre-title">Pop</div><div class="genre-shape"></div></a>
                    <a href="{{ route('search', ['q' => 'Rock']) }}" class="genre-card g-rock"><div class="genre-title">Rock</div><div class="genre-shape"></div></a>
                    <a href="{{ route('search', ['q' => 'Jazz']) }}" class="genre-card g-jazz"><div class="genre-title">Jazz</div><div class="genre-shape"></div></a>
                    <a href="{{ route('search', ['q' => 'Indie']) }}" class="genre-card g-indie"><div class="genre-title">Indie</div><div class="genre-shape"></div></a>
                    <a href="{{ route('search', ['q' => 'K-Pop']) }}" class="genre-card g-kpop"><div class="genre-title">K-Pop</div><div class="genre-shape"></div></a>
                    <a href="{{ route('search', ['q' => 'Dangdut']) }}" class="genre-card g-dangdut"><div class="genre-title">Dangdut</div><div class="genre-shape"></div></a>
                    <a href="{{ route('search', ['q' => 'Relax']) }}" class="genre-card g-relax"><div class="genre-title">Relax</div><div class="genre-shape"></div></a>
                    <a href="{{ route('search', ['q' => 'Electronic']) }}" class="genre-card g-electronic"><div class="genre-title">Electronic</div><div class="genre-shape"></div></a>
                </div>
            </div>
        @endif
        
        <div style="height: 150px;"></div>
    </div>
</div>

<script>
    function playSearchSong(index) {
        const searchResults = document.querySelectorAll('.queue-item');
        if(searchResults.length > 0 && window.loadQueue) {
            window.loadQueue(searchResults, index);
        }
    }
</script>

@endsection
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MyMusic') }}</title>

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3Cpath fill='%232C74B3' d='M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 368c-66.3 0-120-53.7-120-120s53.7-120 120-120 120 53.7 120 120-53.7 120-120 120zm0-176c-30.9 0-56 25.1-56 56s25.1 56 56 56 56-25.1 56-56-25.1-56-56-56z'/%3E%3C/svg%3E">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        /* --- GLOBAL VARIABLES --- */
        :root {
            --c-bg: #050b14;
            --c-sidebar: rgba(5, 11, 20, 0.75);
            --c-player: rgba(15, 23, 42, 0.85);
            --c-accent: #2C74B3;   
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-main: #ffffff;
            --text-muted: #94a3b8;
            --sidebar-width: 260px;
        }

        body { 
            background-color: var(--c-bg); 
            color: var(--text-main); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            overflow: hidden; 
            margin: 0; 
        }

        body::before {
            content: ""; position: fixed; inset: 0; z-index: -1;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--c-accent); }

        .wrapper { display: flex; width: 100%; height: 100vh; }

        #sidebar { 
            width: var(--sidebar-width); 
            background-color: var(--c-sidebar); 
            backdrop-filter: blur(25px); 
            display: flex; flex-direction: column; 
            padding: 30px 20px; 
            flex-shrink: 0; height: 100vh; z-index: 100; 
            border-right: 1px solid var(--glass-border); 
        }

        #content { flex-grow: 1; position: relative; height: 100vh; overflow-y: auto; padding-bottom: 140px; }

        .sidebar-logo { 
            font-size: 1.5rem; font-weight: 800; color: white; text-decoration: none; 
            margin-bottom: 40px; display: flex; align-items: center; gap: 12px; 
            padding-left: 10px; letter-spacing: -0.5px;
        }
        .logo-icon { color: var(--c-accent); filter: drop-shadow(0 0 8px rgba(44, 116, 179, 0.6)); }

        .nav-link { 
            color: var(--text-muted); font-weight: 600; padding: 12px 20px; 
            display: flex; align-items: center; gap: 16px; text-decoration: none; 
            border-radius: 16px; margin-bottom: 5px; 
            transition: all 0.3s; font-size: 0.95rem; border: 1px solid transparent;
        }
        .nav-link:hover { color: white; background-color: rgba(255,255,255,0.05); transform: translateX(3px); }
        .nav-link.active { 
            color: white; background: linear-gradient(90deg, rgba(44, 116, 179, 0.15), transparent); 
            border: 1px solid rgba(44, 116, 179, 0.3); box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        .nav-link.active i { color: #5ba4e5; }
        .sidebar-divider { height: 1px; background: linear-gradient(to right, transparent, rgba(255,255,255,0.1), transparent); margin: 25px 0; }

        .top-header { 
            position: sticky; top: 0; height: 80px; z-index: 50; 
            display: flex; align-items: center; justify-content: flex-end; 
            padding: 0 40px; pointer-events: none;
            background: linear-gradient(to bottom, var(--c-bg) 0%, transparent 100%);
        }
        .top-header > * { pointer-events: auto; }
        .user-btn { background-color: rgba(255,255,255,0.05); backdrop-filter: blur(10px); color: white; border-radius: 30px; padding: 6px 16px 6px 6px; display: flex; align-items: center; gap: 10px; text-decoration: none; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(255,255,255,0.1); transition: 0.3s; }
        .user-btn:hover { background-color: rgba(255,255,255,0.15); border-color: rgba(255,255,255,0.3); }
        .dropdown-menu-dark { background-color: #0f172a; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 10px 40px rgba(0,0,0,0.5); border-radius: 12px; padding: 10px; }
        .dropdown-item { border-radius: 8px; color: #cbd5e1; padding: 8px 15px; margin-bottom: 2px;}
        .dropdown-item:hover { background-color: rgba(255,255,255,0.1); color: white; }

        .player-bar { 
            position: fixed; bottom: 25px; left: calc(var(--sidebar-width) + 25px); right: 25px; 
            height: 96px; background: var(--c-player); 
            backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.1); border-top: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 28px; display: flex; align-items: center; justify-content: space-between; 
            padding: 0 35px; z-index: 999; box-shadow: 0 30px 70px rgba(0, 0, 0, 0.7); 
            transform: translateY(150%); opacity: 0; visibility: hidden; 
            transition: all 0.6s cubic-bezier(0.22, 1, 0.36, 1); 
        }
        .player-bar.show { transform: translateY(0); opacity: 1; visibility: visible; }

        @keyframes spin { 100% { transform: rotate(360deg); } }
        .player-cover { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; box-shadow: 0 0 15px rgba(0,0,0,0.5); border: 2px solid rgba(255,255,255,0.1); }
        .player-bar.playing .player-cover { animation: spin 8s linear infinite; } 

        .player-left { width: 30%; display: flex; align-items: center; gap: 18px; }
        .player-title { color: white; font-size: 1rem; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
        .player-artist { color: var(--text-muted); font-size: 0.8rem; font-weight: 500; }
        .player-like-btn { color: #64748b; background: none; border: none; cursor: pointer; transition: all 0.2s; font-size: 1.2rem; }
        .player-like-btn:hover, .player-like-btn.liked { color: #ec4899; transform: scale(1.1); }

        .player-center { width: 40%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; }
        .player-controls { display: flex; align-items: center; gap: 28px; }
        .control-btn { background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 1.1rem; transition: all 0.3s; }
        .control-btn:hover { color: white; transform: scale(1.1); }
        .play-pause-btn { width: 50px; height: 50px; background: white; color: black; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; border: none; transition: all 0.3s; box-shadow: 0 0 25px rgba(255,255,255,0.3); }
        .play-pause-btn:hover { transform: scale(1.1); box-shadow: 0 0 40px rgba(255,255,255,0.6); }

        .progress-container { width: 100%; display: flex; align-items: center; gap: 12px; font-size: 0.75rem; color: #94a3b8; font-weight: 600; font-family: monospace; }
        .custom-range-slider { -webkit-appearance: none; width: 100%; height: 4px; border-radius: 4px; background: rgba(255, 255, 255, 0.1); outline: none; cursor: pointer; position: relative; }
        .custom-range-slider::-webkit-slider-thumb { -webkit-appearance: none; width: 12px; height: 12px; border-radius: 50%; background: white; cursor: pointer; box-shadow: 0 0 15px rgba(255,255,255,0.9); transition: transform 0.1s; opacity: 0; }
        .player-bar:hover .custom-range-slider::-webkit-slider-thumb { opacity: 1; }
        
        .player-right { width: 30%; display: flex; align-items: center; justify-content: flex-end; gap: 20px; }
        .volume-slider { width: 80px; }

        .text-pink { color: #ec4899 !important; }

        .lyrics-overlay { 
            position: fixed; top: 0; left: 0; width: 100%; height: 100vh; 
            z-index: 2000; display: flex; opacity: 0; visibility: hidden; 
            transition: opacity 0.5s cubic-bezier(0.2, 0.8, 0.2, 1); 
            background: #000; 
        }
        .lyrics-overlay.show { opacity: 1; visibility: visible; }

        .lyrics-bg-image {
            position: absolute; inset: 0; background-size: cover; background-position: center;
            filter: blur(100px) brightness(0.5) saturate(1.2); 
            transform: scale(1.2); z-index: -1;
            transition: background-image 0.8s ease;
        }

        .lyrics-container { 
            display: flex; width: 100%; height: 100%; 
            align-items: center; justify-content: center;
            padding: 40px; gap: 80px;
        }
        
        .lyrics-left { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; max-width: 500px; }
        .lyrics-cover-art { width: 450px; height: 450px; object-fit: cover; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); transform: scale(0.9); transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .lyrics-overlay.show .lyrics-cover-art { transform: scale(1); }
        .lyrics-song-info { text-align: center; margin-top: 30px; }
        .lyrics-song-title { font-size: 2rem; font-weight: 800; color: white; margin-bottom: 8px; line-height: 1.1; letter-spacing: -0.5px; }
        .lyrics-song-artist { font-size: 1.2rem; color: rgba(255,255,255,0.6); font-weight: 600; }

        .lyrics-right { flex: 1; height: 80vh; overflow-y: auto; padding-right: 20px; max-width: 650px; mask-image: linear-gradient(to bottom, transparent 0%, black 15%, black 85%, transparent 100%); scrollbar-width: none; }
        .lyrics-right::-webkit-scrollbar { display: none; }
        .lyrics-line { font-size: 2.2rem; font-weight: 800; color: rgba(255,255,255,0.3); margin-bottom: 30px; cursor: pointer; transition: all 0.4s ease; line-height: 1.3; text-align: left; transform-origin: left center; }
        .lyrics-line.active { color: #ffffff; transform: scale(1.05); text-shadow: 0 0 20px rgba(255,255,255,0.3); }

        .close-lyrics { position: absolute; top: 40px; right: 40px; background: rgba(255,255,255,0.1); border: none; color: white; width: 50px; height: 50px; border-radius: 50%; cursor: pointer; transition: 0.3s; z-index: 10; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .close-lyrics:hover { background: rgba(255,255,255,0.3); }

        #page-loader { position: fixed; top: 0; left: 0; width: 100%; height: 3px; background: linear-gradient(to right, var(--c-accent), #5ba4e5); z-index: 2000; display: none; box-shadow: 0 2px 10px var(--c-accent); }

        /* --- STYLED TOAST ALERT --- */
        .custom-alert {
            background: rgba(15, 23, 42, 0.95) !important;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 16px !important;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>

@php $hidePlayer = request()->routeIs('login', 'register', 'welcome') || request()->is('/'); @endphp

<body class="{{ $hidePlayer ? 'no-player' : '' }}">
    <div id="page-loader"></div>

    <div class="toast-container position-fixed bottom-0 end-0 p-4" style="z-index: 99999">
        <div id="statusToast" class="toast custom-alert text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex p-3 align-items-center">
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 35px; height: 35px;">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="toast-body fw-bold p-0" id="toastMessage">
                    Pesan status di sini.
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    {{-- Overlay Lirik --}}
    <div class="lyrics-overlay" id="lyricsModal">
        <div class="lyrics-bg-image" id="lyricsBg"></div>
        <button class="close-lyrics" id="closeLyricsBtn"><i class="fas fa-times"></i></button>
        <div class="lyrics-container">
            <div class="lyrics-left">
                <img src="" class="lyrics-cover-art" id="lyricsCoverDisplay">
                <div class="lyrics-song-info">
                    <h2 class="lyrics-song-title" id="lyricsTitleDisplay">Judul Lagu</h2>
                    <p class="lyrics-song-artist" id="lyricsArtistDisplay">Nama Artis</p>
                </div>
            </div>
            <div class="lyrics-right" id="lyricsText"></div>
        </div>
    </div>

    {{-- MODAL PLAYLIST --}}
    <div class="modal fade" id="addToPlaylistModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: rgba(15, 23, 42, 0.98); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-white fw-bold">Simpan ke Playlist</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="modalTargetSongId">
                    <div id="playlistOptions" class="d-grid gap-2">
                        @auth
                            @php $userPls = \App\Models\Playlist::where('user_id', auth()->id())->get(); @endphp
                            @forelse($userPls as $pl)
                                <button class="btn btn-outline-light border-0 text-start p-3 rounded-4 d-flex align-items-center gap-3 w-100" 
                                        onclick="confirmAddToPlaylist({{ $pl->id }})" 
                                        style="background: rgba(255,255,255,0.03); transition: 0.3s;">
                                    <img src="{{ asset($pl->cover_path ?? 'images/default_playlist.jpg') }}" width="40" height="40" class="rounded">
                                    <span class="fw-bold text-white">{{ $pl->name }}</span>
                                </button>
                            @empty
                                <div class="text-center py-4 text-white-50">
                                    <p class="small">Kamu belum punya playlist.</p>
                                    <a href="{{ route('create') }}" class="btn btn-sm btn-primary rounded-pill">Buat Playlist Sekarang</a>
                                </div>
                            @endforelse
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wrapper">
        @auth
        <nav id="sidebar">
            <a href="{{ route('home') }}" class="sidebar-logo">
                <i class="fas fa-compact-disc fa-lg logo-icon"></i> 
                <span>MyMusic</span>
            </a>
            <div class="sidebar-divider"></div>
            <div class="d-flex flex-column gap-1">
                @if(request()->is('admin*'))
                    @include('partials.sidebar-admin')
                @else
                    @include('partials.sidebar-user')
                @endif
            </div>
        </nav>
        @endauth

        <div id="content">
            <div class="top-header">
                @guest
                    <div class="d-flex gap-3 align-items-center">
                        <a href="{{ route('register') }}" class="text-secondary fw-bold text-decoration-none hover-white">Sign up</a>
                        <a href="{{ route('login') }}" class="btn btn-light rounded-pill fw-bold px-4 py-2" style="background: white; border:none; color: black;">Log in</a>
                    </div>
                @else
                    <div class="dropdown">
                        <a href="#" class="user-btn dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="user-avatar"><i class="fas fa-user text-white" style="font-size: 14px;"></i></div>
                            <span class="me-1">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end mt-2">
                            <li><a class="dropdown-item" href="#"><i class="far fa-user me-2"></i> Profile</a></li>
                            @if(request()->is('admin*'))
                                <li><a class="dropdown-item text-warning" href="{{ route('home') }}"><i class="fas fa-arrow-left me-2"></i> Back to App</a></li>
                            @else
                                @if(Auth::user()->role === 'admin')
                                    <li><a class="dropdown-item" href="{{ route('admin.songs.index') }}"><i class="fas fa-cog me-2"></i> Admin Panel</a></li>
                                @endif
                            @endif
                            <li><hr class="dropdown-divider bg-secondary opacity-25"></li>
                            <li><a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-2"></i> Log out</a><form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form></li>
                        </ul>
                    </div>
                @endguest
            </div>
            <div id="main-page-content">@yield('content')</div>
        </div>
    </div>

    @unless($hidePlayer)
    <div class="player-bar" id="mainPlayerBar">
        <audio id="audioPlayer" preload="metadata"></audio>
        <div class="player-left">
            <img src="" class="player-cover d-none" id="playerCover" alt="Cover">
            <div class="player-cover bg-secondary d-flex align-items-center justify-content-center text-white-50" id="playerCoverPlaceholder" style="font-size: 1.5rem;">
                <i class="fas fa-music"></i>
            </div>
            <div class="player-info">
                <div class="player-title" id="playerTitle">Pilih Lagu</div>
                <div class="player-artist" id="playerArtist">-</div>
            </div>
            <button class="player-like-btn ms-3" id="playerLikeBtn" title="Like"><i class="far fa-heart"></i></button>
        </div>

        <div class="player-center">
            <div class="player-controls">
                <button class="control-btn" title="Shuffle"><i class="fas fa-random"></i></button>
                <button class="control-btn" id="prevBtn" title="Previous"><i class="fas fa-step-backward"></i></button>
                <button class="play-pause-btn" id="playPauseBtn" title="Play/Pause"><i class="fas fa-play ms-1" id="playIcon"></i></button>
                <button class="control-btn" id="nextBtn" title="Next"><i class="fas fa-step-forward"></i></button>
                <button class="control-btn" id="repeatBtn" title="Repeat"><i class="fas fa-redo"></i></button>
            </div>
            <div class="progress-container">
                <span id="currentTime">0:00</span>
                <input type="range" class="custom-range-slider progress-bar-range" id="progressBar" min="0" max="1000" value="0">
                <span id="duration">0:00</span>
            </div>
        </div>

        <div class="player-right">
            <button class="control-btn" id="lyricsBtn" title="Lyrics" style="font-size: 1.2rem;"><i class="fas fa-microphone-alt"></i></button>
            <div class="d-flex align-items-center gap-2 ms-4">
                <i class="fas fa-volume-up text-secondary small"></i>
                <input type="range" class="custom-range-slider volume-slider" id="volumeControl" min="0" max="1" step="0.01" value="0.7">
            </div>
        </div>
    </div>
    @endunless

<script>
    // 1. GLOBAL STATE
    window.playerState = { 
        parsedLyrics: [], 
        isDragging: false, 
        lastLyricIndex: -1, 
        queue: [], 
        currentIndex: -1,
        isRepeat: false 
    };
    window.audio = document.getElementById('audioPlayer');

    // --- FUNGSI ALERT TOAST ---
    window.showToast = function(msg) {
        const toastEl = document.getElementById('statusToast');
        document.getElementById('toastMessage').textContent = msg;
        if (typeof bootstrap !== 'undefined') {
            const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
            toast.show();
        } else {
            toastEl.classList.add('show');
            setTimeout(() => toastEl.classList.remove('show'), 3000);
        }
    }

    // 2. LOGIKA MUSIC PLAYER 
    function playSongAtIndex(index) {
        if (!window.audio || index < 0 || index >= window.playerState.queue.length) return;
        const song = window.playerState.queue[index];
        window.playerState.currentIndex = index;

        const playerIcon = document.querySelector('#playerLikeBtn i');
        if (playerIcon) { playerIcon.className = 'far fa-heart'; playerIcon.style.color = ''; }

        document.getElementById('playerTitle').textContent = song.title || 'Unknown Title';
        document.getElementById('playerArtist').textContent = song.artist || 'Unknown Artist';
        
        const coverImg = document.getElementById('playerCover');
        if(song.cover && coverImg) {
            coverImg.src = song.cover;
            coverImg.classList.remove('d-none');
            document.getElementById('playerCoverPlaceholder').classList.add('d-none');
        }

        if (document.getElementById('lyricsTitleDisplay')) {
            document.getElementById('lyricsTitleDisplay').textContent = song.title;
            document.getElementById('lyricsArtistDisplay').textContent = song.artist;
            document.getElementById('lyricsCoverDisplay').src = song.cover;
            document.getElementById('lyricsBg').style.backgroundImage = `url('${song.cover}')`;
        }

        window.playerState.parsedLyrics = parseLyrics(song.lyrics || "");
        renderLyricsUI(); 

        if (song.id) { checkFavoriteStatus(song.id); }

        window.audio.src = song.src;
        document.getElementById('mainPlayerBar').classList.add('show');
        window.audio.play().then(() => {
            document.getElementById('playIcon').className = 'fas fa-pause';
        }).catch(console.error);
    }

    // 3. PLAYLIST LOGIC
    window.setTargetSong = function(songId) {
        const input = document.getElementById('modalTargetSongId');
        if (input) input.value = songId;
    };

    window.confirmAddToPlaylist = function(playlistId) {
        const songId = document.getElementById('modalTargetSongId').value;
        const token = document.querySelector('meta[name="csrf-token"]').content;
        if (!songId) return;

        fetch('{{ route("playlist.add-song") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify({ playlist_id: playlistId, song_id: songId })
        })
        .then(r => r.json())
        .then(data => {
            window.showToast(data.message); // Ganti alert dengan toast
            const closeBtn = document.querySelector('#addToPlaylistModal .btn-close');
            if (closeBtn) closeBtn.click();
        })
        .catch(err => {
            console.error(err);
            window.showToast("Gagal menambahkan lagu.");
        });
    };

    // Navigasi & Player Controls
    function nextSong() {
        let nextIndex = window.playerState.currentIndex + 1;
        if (nextIndex >= window.playerState.queue.length) nextIndex = 0;
        playSongAtIndex(nextIndex);
    }

    function prevSong() {
        let prevIndex = window.playerState.currentIndex - 1;
        if (prevIndex < 0) prevIndex = window.playerState.queue.length - 1;
        playSongAtIndex(prevIndex);
    }

    function updateSliderVisual(el) {
        if (!el) return;
        const pct = (el.value - el.min) / (el.max - el.min) * 100;
        el.style.background = `linear-gradient(to right, #2C74B3 ${pct}%, rgba(255,255,255,0.1) ${pct}%)`;
    }

    // --- ORIGINAL AUDIO EVENTS & CLICK LISTENERS ---
    document.addEventListener('click', function(e) {
        const repeatBtn = e.target.closest('#repeatBtn');
        if (repeatBtn) {
            window.playerState.isRepeat = !window.playerState.isRepeat;
            repeatBtn.style.color = window.playerState.isRepeat ? '#ec4899' : '#94a3b8';
            return;
        }

        if (e.target.closest('#nextBtn')) { nextSong(); return; }
        if (e.target.closest('#prevBtn')) { prevSong(); return; }

        const favBtn = e.target.closest('.fav-btn, .btn-heart-active, #playerLikeBtn');
        if (favBtn) {
            e.preventDefault(); e.stopPropagation();
            let songId = (favBtn.id === 'playerLikeBtn') 
                ? window.playerState.queue[window.playerState.currentIndex]?.id 
                : favBtn.closest('.queue-item')?.getAttribute('data-id');
            if (songId) toggleFavoriteAction(songId, favBtn);
            return;
        }

        const trackItem = e.target.closest('.queue-item');
        if (trackItem) {
            if (e.target.closest('.add-playlist-btn') || e.target.closest('.fa-plus-circle')) return; 

            const allTracks = Array.from(document.querySelectorAll('.queue-item'));
            window.playerState.queue = allTracks.map(el => ({
                id: el.getAttribute('data-id'),
                title: el.dataset.title, artist: el.dataset.artist,
                src: el.dataset.src, cover: el.dataset.cover, lyrics: el.dataset.lyrics
            }));
            playSongAtIndex(allTracks.indexOf(trackItem));
            return;
        }

        if (e.target.closest('#playPauseBtn')) {
            if (window.audio.paused) { window.audio.play(); document.getElementById('playIcon').className = 'fas fa-pause'; }
            else { window.audio.pause(); document.getElementById('playIcon').className = 'fas fa-play'; }
        }
        if (e.target.closest('#lyricsBtn')) document.getElementById('lyricsModal').classList.add('show');
        if (e.target.closest('#closeLyricsBtn')) document.getElementById('lyricsModal').classList.remove('show');
    });

    window.audio.onended = function() {
        if (window.playerState.isRepeat) { window.audio.currentTime = 0; window.audio.play(); } 
        else { nextSong(); }
    };

    function parseLyrics(lrc) {
        if (!lrc) return [];
        const result = [];
        const reg = /\[(\d{1,3}):(\d{1,2})(?:[.:](\d{1,3}))?\]/;
        lrc.split('\n').forEach(line => {
            const m = reg.exec(line);
            if (m) {
                const t = parseFloat(m[1]) * 60 + parseFloat(m[2]) + (m[3] ? parseFloat("0." + m[3]) : 0);
                const txt = line.replace(reg, '').trim();
                if (txt) result.push({ t, txt });
            }
        });
        return result.sort((a, b) => a.t - b.t);
    }

    function renderLyricsUI() {
        const container = document.getElementById('lyricsText');
        if (!container) return;
        container.innerHTML = window.playerState.parsedLyrics.map((l, idx) => 
            `<div class="lyrics-line" data-index="${idx}" onclick="window.audio.currentTime=${l.t}">${l.txt}</div>`
        ).join('');
        window.playerState.lastLyricIndex = -1;
    }

    setInterval(() => {
        if (!window.audio.paused && document.getElementById('lyricsModal').classList.contains('show')) {
            const cur = window.audio.currentTime;
            const idx = window.playerState.parsedLyrics.findLastIndex(l => cur >= l.t);
            if (idx !== -1 && idx !== window.playerState.lastLyricIndex) {
                const lines = document.querySelectorAll('.lyrics-line');
                if (window.playerState.lastLyricIndex !== -1 && lines[window.playerState.lastLyricIndex]) {
                    lines[window.playerState.lastLyricIndex].classList.remove('active');
                }
                if (lines[idx]) {
                    lines[idx].classList.add('active');
                    lines[idx].scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                window.playerState.lastLyricIndex = idx;
            }
        }
    }, 150);

    document.addEventListener('input', function(e) {
        if (e.target.id === 'volumeControl') { window.audio.volume = e.target.value; updateSliderVisual(e.target); }
        if (e.target.id === 'progressBar') { window.playerState.isDragging = true; updateSliderVisual(e.target); }
    });

    document.addEventListener('change', function(e) {
        if (e.target.id === 'progressBar') {
            window.audio.currentTime = (e.target.value / 1000) * window.audio.duration;
            window.playerState.isDragging = false;
        }
    });

    window.audio.addEventListener('timeupdate', function() {
        const prog = document.getElementById('progressBar');
        if (!window.playerState.isDragging && isFinite(window.audio.duration)) {
            prog.value = (window.audio.currentTime / window.audio.duration) * 1000;
            updateSliderVisual(prog);
            document.getElementById('currentTime').textContent = formatTime(window.audio.currentTime);
            document.getElementById('duration').textContent = formatTime(window.audio.duration);
        }
    });

    function checkFavoriteStatus(songId) {
        fetch(`/favorites/check/${songId}?t=${Date.now()}`).then(r => r.json()).then(data => {
            const icon = document.querySelector('#playerLikeBtn i');
            if (icon) {
                icon.className = data.is_favorite ? 'fas fa-heart text-pink' : 'far fa-heart';
                icon.style.color = data.is_favorite ? '#ec4899' : '';
            }
        });
    }

    function toggleFavoriteAction(songId, btnElement) {
        fetch(`/favorites/toggle/${songId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        }).then(r => r.json()).then(data => {
            const active = data.status === 'liked';
            window.showToast(active ? "Disimpan ke Favorit" : "Dihapus dari Favorit");
            checkFavoriteStatus(songId);
            document.querySelectorAll(`.queue-item[data-id="${songId}"] i`).forEach(icon => {
                if (icon.classList.contains('fa-heart')) {
                    icon.className = active ? 'fas fa-heart text-pink' : 'far fa-heart';
                    icon.style.color = active ? '#ec4899' : '';
                }
            });
        });
    }

    function formatTime(s) { if(!isFinite(s)) return "0:00"; const m=Math.floor(s/60),sec=Math.floor(s%60); return m+":"+(sec<10?'0':'')+sec; }
    
    document.addEventListener('DOMContentLoaded', () => {
        updateSliderVisual(document.getElementById('volumeControl'));
        updateSliderVisual(document.getElementById('progressBar'));
    });
</script>
</body>
</html>
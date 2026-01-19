@extends('layouts.app')

@section('content')
<style>
    .library-scope { --glass-border: rgba(255, 255, 255, 0.1); --glass-surface: rgba(255, 255, 255, 0.03); --c-accent: #2C74B3; }
    .page-background { position: fixed; inset: 0; z-index: 0; background: #050b14; background-image: radial-gradient(circle at 85% 10%, rgba(236, 72, 153, 0.05), transparent 40%); }
    .library-content-wrapper { position: relative; z-index: 1; padding: 40px 0 100px; }
    .library-title { font-size: 2.5rem; font-weight: 800; background: linear-gradient(to right, #fff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .card-ultra { background: var(--glass-surface); border: 1px solid var(--glass-border); backdrop-filter: blur(10px); border-radius: 24px; padding: 16px; transition: 0.4s; height: 100%; display: flex; flex-direction: column; text-decoration: none; position: relative; }
    .card-ultra:hover { background: rgba(255, 255, 255, 0.07); transform: translateY(-10px); }
    .card-img-container { position: relative; aspect-ratio: 1/1; border-radius: 16px; overflow: hidden; margin-bottom: 14px; box-shadow: 0 8px 20px rgba(0,0,0,0.3); }
    .card-img { width: 100%; height: 100%; object-fit: cover; }
    .more-options { position: absolute; top: 20px; right: 20px; z-index: 10; opacity: 0; transition: 0.3s; }
    .card-ultra:hover .more-options { opacity: 1; }
    .btn-more { background: rgba(0,0,0,0.4); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; }
    .liked-hero { background: linear-gradient(135deg, #4f46e5 0%, #ec4899 100%); border-radius: 28px; padding: 35px; min-height: 280px; display: flex; flex-direction: column; justify-content: flex-end; position: relative; text-decoration: none; overflow: hidden; }
    .create-card { border: 2px dashed rgba(255,255,255,0.1); display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 280px; border-radius: 28px; text-decoration: none; color: #64748b; transition: 0.3s; }
    .create-card:hover { border-color: var(--c-accent); color: #fff; background: rgba(44, 116, 179, 0.05); }
    .playlist-badge { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1.5px; color: var(--c-accent); font-weight: 800; }
</style>

<div class="library-scope">
    <div class="page-background"></div>
    <div class="container library-content-wrapper">
        <div class="mb-5">
            <span class="playlist-badge">Collection</span>
            <h1 class="library-title">Your Library</h1>
        </div>

        <div class="row g-4">
            {{-- Liked Songs --}}
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('favorites') }}" class="liked-hero shadow-lg">
                    <i class="fas fa-heart position-absolute" style="font-size:10rem; right:-20px; top:-20px; opacity:0.1"></i>
                    <h2 class="text-white fw-bold">Liked Songs</h2>
                    <p class="text-white text-opacity-75 mb-0">{{ auth()->user()->favoriteSongs->count() }} Tracks</p>
                </a>
            </div>

            {{-- Create Card --}}
            <div class="col-lg-2 col-md-3 col-6">
                <a href="{{ route('create') }}" class="create-card">
                    <i class="fas fa-plus fa-2x mb-3"></i>
                    <span class="fw-bold">New Playlist</span>
                </a>
            </div>

            {{-- Loop Playlist --}}
            @foreach($playlists as $playlist)
            <div class="col-lg-2 col-md-3 col-6">
                <div class="card-ultra shadow-sm">
                    <div class="more-options">
                        <div class="dropdown">
                            <button class="btn-more" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg">
                                <li><a class="dropdown-item" href="{{ route('playlist.edit', $playlist->id) }}"><i class="fas fa-edit me-2"></i> Edit</a></li>
                                <li><hr class="dropdown-divider opacity-10"></li>
                                <li>
                                    <form action="{{ route('playlist.destroy', $playlist->id) }}" method="POST" onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i> Delete</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <a href="{{ route('playlist.show', $playlist->id) }}" class="text-decoration-none">
                        <div class="card-img-container">
                            <img src="{{ asset($playlist->cover_path) }}" class="card-img">
                        </div>
                        <h6 class="text-white fw-bold mb-1 text-truncate">{{ $playlist->name }}</h6>
                        <p class="text-white-50 small mb-0">{{ $playlist->songs->count() }} Tracks</p>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
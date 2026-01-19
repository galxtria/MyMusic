@extends('layouts.app')

@section('content')

<style>
    /* --- SCOPED VARIABLES (ADMIN) --- */
    .admin-scope {
        --c-deep: #050b14;
        --c-accent: #2C74B3;
        --glass-border: rgba(255, 255, 255, 0.15); 
        --glass-surface: rgba(15, 23, 42, 0.75);   
        --glass-hover: rgba(30, 41, 59, 0.9);
    }

    /* --- ANIMATIONS --- */
    @keyframes gradientMove {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-enter { animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; opacity: 0; }
    .d-1 { animation-delay: 0.1s; }
    .d-2 { animation-delay: 0.2s; }

    /* --- BACKGROUND --- */
    .page-background {
        position: fixed; inset: 0; z-index: 0;
        background: #050b14;
        background-image: 
            radial-gradient(circle at 10% 20%, rgba(44, 116, 179, 0.1), transparent 40%), 
            radial-gradient(circle at 90% 80%, rgba(79, 70, 229, 0.1), transparent 40%);
    }

    .admin-content-wrapper { position: relative; z-index: 1; }

    /* --- HEADER --- */
    .admin-header {
        display: flex; justify-content: space-between; align-items: flex-end;
        margin-bottom: 40px; padding-bottom: 20px;
        border-bottom: 1px solid var(--glass-border);
    }
    .page-title {
        font-size: clamp(2rem, 4vw, 3rem); font-weight: 900; line-height: 1; margin: 0;
        background: linear-gradient(to right, #fff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }
    .page-subtitle { color: #94a3b8; margin-top: 5px; font-size: 1rem; }

    /* --- STATS CARDS --- */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
    
    .stat-card {
        position: relative; overflow: hidden; border-radius: 20px; padding: 25px;
        background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);
        display: flex; flex-direction: column; justify-content: center;
        transition: 0.3s;
    }
    .stat-card:hover { transform: translateY(-5px); border-color: rgba(255,255,255,0.3); }
    
    .stat-bg {
        position: absolute; inset: 0; opacity: 0.1;
        background: linear-gradient(45deg, var(--c-accent), #4f46e5);
        background-size: 200% 200%; animation: gradientMove 5s ease infinite;
    }
    
    .stat-value { font-size: 2.5rem; font-weight: 800; color: white; position: relative; z-index: 1; line-height: 1; margin-bottom: 5px; }
    .stat-label { font-size: 0.9rem; color: #cbd5e1; position: relative; z-index: 1; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
    .stat-icon { position: absolute; right: 20px; bottom: 20px; font-size: 3rem; color: white; opacity: 0.05; z-index: 0; }

    /* --- SEARCH BAR --- */
    .search-bar-wrapper {
        display: flex; justify-content: space-between; align-items: center; gap: 20px; margin-bottom: 20px;
    }
    .admin-search-input {
        background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border);
        color: white; padding: 12px 25px; border-radius: 50px; width: 300px;
        transition: 0.3s;
    }
    .admin-search-input:focus { outline: none; background: rgba(0,0,0,0.6); border-color: var(--c-accent); width: 350px; }

    .btn-add-new {
        background: white; color: black; padding: 12px 30px; border-radius: 50px;
        font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 10px;
        transition: 0.3s; box-shadow: 0 0 20px rgba(255,255,255,0.1);
    }
    .btn-add-new:hover { transform: scale(1.05); box-shadow: 0 0 30px rgba(255,255,255,0.3); }

    /* --- TABLE STYLING --- */
    /* Sinkronisasi Kolom Grid antara Header dan Baris */
    .admin-grid-layout {
        display: grid; 
        grid-template-columns: 80px 3fr 2fr 1.5fr 1fr 100px; 
        gap: 20px; 
        align-items: center;
    }

    .table-header {
        padding: 0 20px 15px; 
        color: #94a3b8; 
        font-size: 0.8rem; 
        font-weight: 700; 
        text-transform: uppercase; 
        letter-spacing: 1px;
    }

    .glass-row {
        background: var(--glass-surface); 
        border: 1px solid var(--glass-border);
        padding: 15px 20px; border-radius: 16px; margin-bottom: 10px;
        transition: 0.2s;
    }
    .glass-row:hover {
        background: var(--glass-hover); 
        transform: scale(1.005); 
        border-color: rgba(255,255,255,0.3);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .row-img { width: 50px; height: 50px; border-radius: 8px; object-fit: cover; border: 1px solid rgba(255,255,255,0.1); }
    .row-title { color: #ffffff; font-weight: 700; font-size: 1rem; margin-bottom: 2px; }
    .row-sub { color: #cbd5e1; font-size: 0.85rem; } 
    .row-badge { 
        display: inline-block; padding: 4px 12px; border-radius: 20px; 
        font-size: 0.75rem; font-weight: 700; 
        background: rgba(44, 116, 179, 0.2); color: #5ba4e5; border: 1px solid rgba(44, 116, 179, 0.4);
    }

    /* Actions */
    .action-btn {
        width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        border: none; cursor: pointer; transition: 0.2s; color: white;
    }
    .btn-edit { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
    .btn-edit:hover { background: #fbbf24; color: black; }
    .btn-delete { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
    .btn-delete:hover { background: #ef4444; color: white; }

    /* --- PAGINATION --- */
    .pagination { justify-content: center; margin-top: 30px; gap: 5px; }
    .page-item .page-link {
        background: transparent; border: 1px solid var(--glass-border);
        color: #cbd5e1; border-radius: 8px; padding: 8px 16px; transition: 0.3s;
    }
    .page-item .page-link:hover { background: rgba(255,255,255,0.1); color: white; border-color: white; }
    .page-item.active .page-link {
        background: var(--c-accent); border-color: var(--c-accent); color: white;
        box-shadow: 0 0 15px rgba(44, 116, 179, 0.5);
    }

    @media (max-width: 992px) {
        .table-header { display: none; }
        .glass-row { grid-template-columns: 60px 1fr auto; gap: 15px; padding: 15px; }
        .col-hide-mobile { display: none; }
        .search-bar-wrapper { flex-direction: column-reverse; align-items: stretch; }
        .admin-search-input { width: 100%; }
        .admin-search-input:focus { width: 100%; }
    }
</style>

<div class="admin-scope">
    <div class="page-background"></div>
    <div class="container pb-5 admin-content-wrapper">

        <div class="admin-header pt-5 animate-enter">
            <div>
                <h1 class="page-title">Library Manager</h1>
                <p class="page-subtitle">Pusat kendali konten musik Anda.</p>
            </div>
            <div class="d-none d-md-block text-end">
                <div class="text-white fw-bold">{{ Auth::user()->name }}</div>
                <div class="text-white-50 small">Administrator</div>
            </div>
        </div>

        <div class="stats-grid animate-enter d-1">
            <div class="stat-card">
                <div class="stat-bg"></div>
                <div class="stat-value">{{ $songs->total() }}</div>
                <div class="stat-label">Total Songs</div>
                <i class="fas fa-music stat-icon"></i>
            </div>
            <div class="stat-card">
                <div class="stat-bg" style="background: linear-gradient(45deg, #ec4899, #8b5cf6);"></div>
                @php $genreCount = \App\Models\Song::distinct('genre')->count('genre'); @endphp
                <div class="stat-value">{{ $genreCount }}</div>
                <div class="stat-label">Unique Genres</div>
                <i class="fas fa-tags stat-icon"></i>
            </div>
            <div class="stat-card">
                <div class="stat-bg" style="background: linear-gradient(45deg, #10b981, #3b82f6);"></div>
                @php $artistCount = \App\Models\Song::distinct('artist')->count('artist'); @endphp
                <div class="stat-value">{{ $artistCount }}</div>
                <div class="stat-label">Artists</div>
                <i class="fas fa-microphone stat-icon"></i>
            </div>
        </div>

        <div class="search-bar-wrapper animate-enter d-2">
            <form action="{{ route('admin.songs.index') }}" method="GET" class="flex-grow-1">
                <div class="position-relative d-flex align-items-center">
                    <i class="fas fa-search position-absolute text-white-50" style="left: 20px;"></i>
                    <input type="text" 
                        name="search" 
                        class="admin-search-input" 
                        placeholder="Cari judul, artis, atau genre..." 
                        value="{{ request('search') }}"
                        style="padding-left: 50px;">
                    @if(request('search'))
                        <a href="{{ route('admin.songs.index') }}" class="position-absolute text-white-50" style="right: 20px; text-decoration: none;">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>

            <a href="{{ route('admin.songs.create') }}" class="btn-add-new">
                <i class="fas fa-plus"></i> Upload Song
            </a>
        </div>

        <div class="animate-enter d-2">
            <div class="admin-grid-layout table-header d-none d-lg-grid">
                <div>Cover</div>
                <div>Song Info</div>
                <div>Genre</div>
                <div>Duration</div>
                <div>Uploaded</div>
                <div class="text-end">Actions</div>
            </div>

            @forelse($songs as $song)
            <div class="admin-grid-layout glass-row">
                <div>
                    <img src="{{ asset($song->album_art) }}" class="row-img" alt="Cover">
                </div>

                <div>
                    <div class="row-title">{{ $song->title }}</div>
                    <div class="row-sub">{{ $song->artist }}</div>
                </div>

                <div class="col-hide-mobile">
                    @if($song->genre)
                        <span class="row-badge">{{ $song->genre }}</span>
                    @else
                        <span class="text-white-50 small">-</span>
                    @endif
                </div>

                <div class="col-hide-mobile text-white-50 font-monospace small">
                    {{ $song->duration ?? '--:--' }}
                </div>

                <div class="col-hide-mobile text-white-50 small">
                    {{ $song->created_at->diffForHumans() }}
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.songs.edit', $song->id) }}" class="action-btn btn-edit" title="Edit">
                        <i class="fas fa-pen small"></i>
                    </a>
                    <form action="{{ route('admin.songs.destroy', $song->id) }}" method="POST" onsubmit="return confirm('Hapus lagu ini?')">
                        @csrf @method('DELETE')
                        <button class="action-btn btn-delete" title="Hapus">
                            <i class="fas fa-trash small"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-5 glass-row" style="display: block;">
                <div class="mb-3 opacity-50">
                    <i class="fas fa-box-open fa-3x text-white"></i>
                </div>
                <h5 class="text-white">Tidak ada data ditemukan.</h5>
                <p class="text-white-50">Coba kata kunci lain atau upload lagu baru.</p>
            </div>
            @endforelse

            @if($songs->hasPages())
            <div class="mt-4">
                {{ $songs->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
        <div style="height: 100px;"></div>
    </div>
</div>

@endsection
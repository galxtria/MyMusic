<div class="d-flex flex-column gap-1">
    <a href="{{ route('home') }}" class="nav-link ajax-link {{ request()->routeIs('home') ? 'active' : '' }}">
        <i class="fas fa-house"></i> <span>Home</span>
    </a>
    <a href="{{ route('search') }}" class="nav-link ajax-link {{ request()->routeIs('search') ? 'active' : '' }}">
        <i class="fas fa-magnifying-glass"></i> <span>Search</span>
    </a>
    <a href="{{ route('library') }}" class="nav-link ajax-link {{ request()->routeIs('library') ? 'active' : '' }}">
        <i class="fas fa-layer-group"></i> <span>Your Library</span>
    </a>
</div>

<div class="sidebar-section-label">COLLECTION</div>
<div class="d-flex flex-column gap-1">
    <a href="{{ route('create') }}" class="nav-link ajax-link {{ request()->routeIs('create') ? 'active' : '' }}">
        <span class="d-inline-flex align-items-center justify-content-center rounded-3" style="width: 30px; height: 30px; background: linear-gradient(135deg, #38BDF8, #2563EB); box-shadow: 0 6px 16px rgba(59,130,246,0.4); border: 1px solid rgba(255,255,255,0.2);">
            <i class="fas fa-plus text-white" style="font-size: 11px;"></i>
        </span>
        <span>Create Playlist</span>
    </a>
    <a href="{{ route('favorites') }}" class="nav-link ajax-link {{ request()->routeIs('favorites') ? 'active' : '' }}">
        <span class="d-inline-flex align-items-center justify-content-center rounded-3" style="width: 30px; height: 30px; background: linear-gradient(135deg, #1D4ED8, #0A1730); box-shadow: 0 6px 16px rgba(29,78,216,0.5); border: 1px solid rgba(125,211,252,0.35);">
            <i class="fas fa-heart text-white" style="font-size: 11px;"></i>
        </span>
        <span>Liked Songs</span>
    </a>
</div>

<div class="mt-auto pt-4">
    <div class="glass rounded-4 p-3 d-flex align-items-center gap-3" style="border-radius: 18px;">
        <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; background: linear-gradient(135deg, #38BDF8, #1D4ED8);">
            <i class="fas fa-bolt text-white" style="font-size: 13px;"></i>
        </div>
        <div style="min-width: 0;">
            <div class="text-white fw-bold" style="font-size: 0.82rem;">Premium Sound</div>
            <div style="font-size: 0.72rem; color: var(--text-dim);">Lossless • Spatial</div>
        </div>
    </div>
</div>

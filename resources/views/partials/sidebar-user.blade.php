<div class="d-flex flex-column gap-1">
    <a href="{{ route('home') }}" class="nav-link ajax-link {{ request()->routeIs('home') ? 'active' : '' }}">
        <i class="fas fa-home"></i> Home
    </a>
    <a href="{{ route('search') }}" class="nav-link ajax-link {{ request()->routeIs('search') ? 'active' : '' }}">
        <i class="fas fa-search"></i> Search
    </a>
    <a href="{{ route('library') }}" class="nav-link ajax-link {{ request()->routeIs('library') ? 'active' : '' }}">
        <i class="fas fa-stream"></i> Your Library
    </a>
</div>

<div class="mt-4">
    <a href="{{ route('create') }}" class="nav-link ajax-link">
        <div class="bg-white text-black d-flex align-items-center justify-content-center rounded-1" style="width: 28px; height: 28px;">
            <i class="fas fa-plus" style="font-size: 14px;"></i>
        </div> 
        Create Playlist
    </a>
    <a href="{{ route('favorites') }}" class="nav-link ajax-link">
        <div class="heart-box">
            <i class="fas fa-heart text-white" style="font-size: 14px;"></i>
        </div> 
        Liked Songs
    </a>
</div>


<div class="d-flex flex-column gap-1">
    <div class="text-white-50 small fw-bold px-3 mb-2 mt-2">ADMIN PANEL</div>
    
    <a href="{{ route('admin.songs.index') }}" class="nav-link ajax-link {{ request()->routeIs('admin.songs.*') ? 'active' : '' }}">
        <i class="fas fa-music"></i> Manage Songs
    </a>
    
    <a href="{{ route('admin.songs.create') }}" class="nav-link ajax-link {{ request()->routeIs('admin.songs.create') ? 'active' : '' }}">
        <i class="fas fa-cloud-upload-alt"></i> Upload Song
    </a>

    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
    <i class="fas fa-users"></i> <span>Manage Users</span>
    </a>

    <div class="text-white-50 small fw-bold px-3 mb-2 mt-4">TOOLS</div>
    
    <a href="{{ route('admin.tools.lrc-maker') }}" class="nav-link ajax-link {{ request()->routeIs('admin.tools.lrc-maker') ? 'active' : '' }}">
        <i class="fas fa-magic"></i> LRC Maker
    </a>
</div>


<div class="d-flex flex-column gap-1">
    <div class="sidebar-section-label" style="margin-top: 0;">ADMIN PANEL</div>

    <a href="{{ route('admin.songs.index') }}" class="nav-link ajax-link {{ request()->routeIs('admin.songs.*') ? 'active' : '' }}">
        <i class="fas fa-music"></i> <span>Manage Songs</span>
    </a>

    <a href="{{ route('admin.songs.create') }}" class="nav-link ajax-link {{ request()->routeIs('admin.songs.create') ? 'active' : '' }}">
        <i class="fas fa-cloud-arrow-up"></i> <span>Upload Song</span>
    </a>

    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
        <i class="fas fa-users"></i> <span>Manage Users</span>
    </a>

    <div class="sidebar-section-label">TOOLS</div>

    <a href="{{ route('admin.tools.import') }}" class="nav-link ajax-link {{ request()->routeIs('admin.tools.import') ? 'active' : '' }}">
        <i class="fas fa-cloud-arrow-down"></i> <span>Import Music</span>
    </a>
</div>

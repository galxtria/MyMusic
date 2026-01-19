@extends('layouts.app')

@section('content')

<style>
    /* --- SCOPED VARIABLES (ADMIN USERS) --- */
    .admin-user-scope {
        --c-deep: #050b14;
        --c-accent: #10b981; /* Hijau Emerald untuk User */
        --c-admin: #f43f5e;  /* Merah Rose untuk Admin */
        --glass-border: rgba(255, 255, 255, 0.08);
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
            radial-gradient(circle at 80% 10%, rgba(16, 185, 129, 0.15), transparent 40%), 
            radial-gradient(circle at 20% 90%, rgba(244, 63, 94, 0.15), transparent 40%);
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
        background: linear-gradient(to right, #fff, #a7f3d0); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }
    .page-subtitle { color: #94a3b8; margin-top: 5px; font-size: 1rem; }

    /* --- STATS CARDS --- */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
    
    .stat-card {
        position: relative; overflow: hidden; border-radius: 20px; padding: 25px;
        background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);
        display: flex; flex-direction: column; justify-content: center; transition: 0.3s;
    }
    .stat-card:hover { transform: translateY(-5px); border-color: rgba(255,255,255,0.3); }
    
    .stat-bg {
        position: absolute; inset: 0; opacity: 0.1;
        background: linear-gradient(45deg, var(--c-accent), #06b6d4);
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
        color: white; padding: 12px 25px; border-radius: 50px; width: 100%;
        transition: 0.3s;
    }
    .admin-search-input:focus { outline: none; background: rgba(0,0,0,0.6); border-color: var(--c-accent); }

    /* --- TABLE (GLASS ROWS) --- */
    .table-header {
        display: grid; grid-template-columns: 80px 2fr 2fr 1fr 1fr 120px; gap: 20px;
        padding: 0 20px 15px; color: #94a3b8; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
    }

    .glass-row {
        display: grid; grid-template-columns: 80px 2fr 2fr 1fr 1fr 120px; gap: 20px; align-items: center;
        background: var(--glass-surface); border: 1px solid var(--glass-border);
        padding: 15px 20px; border-radius: 16px; margin-bottom: 10px;
        transition: 0.2s;
    }
    .glass-row:hover {
        background: var(--glass-hover); transform: scale(1.005); 
        border-color: rgba(255,255,255,0.3); box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    /* Avatar */
    .user-avatar-box {
        width: 50px; height: 50px; border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 1.2rem; color: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .row-name { color: #ffffff; font-weight: 700; font-size: 1rem; margin-bottom: 2px; }
    .row-email { color: #cbd5e1; font-size: 0.85rem; font-family: monospace; }
    
    /* Role Badges */
    .badge-role {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; letter-spacing: 0.5px;
    }
    .role-admin { 
        background: rgba(244, 63, 94, 0.15); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.3);
        box-shadow: 0 0 10px rgba(244, 63, 94, 0.1);
    }
    .role-user { 
        background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); 
    }

    /* Actions */
    .action-btn {
        width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        border: none; cursor: pointer; transition: 0.2s; color: white;
    }
    .btn-toggle { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }
    .btn-toggle:hover { background: #3b82f6; color: white; }
    .btn-delete { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
    .btn-delete:hover { background: #ef4444; color: white; }

    /* Pagination */
    .pagination { justify-content: center; margin-top: 30px; gap: 5px; }
    .page-item .page-link {
        background: transparent; border: 1px solid var(--glass-border);
        color: #cbd5e1; border-radius: 8px; padding: 8px 16px; transition: 0.3s;
    }
    .page-item .page-link:hover { background: rgba(255,255,255,0.1); color: white; border-color: white; }
    .page-item.active .page-link {
        background: var(--c-accent); border-color: var(--c-accent); color: white;
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
    }
    .page-item.disabled .page-link { background: transparent; color: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.05); }

    /* Responsive */
    @media (max-width: 992px) {
        .table-header { display: none; }
        .glass-row { grid-template-columns: 60px 1fr auto; gap: 15px; padding: 20px; }
        .col-hide-mobile { display: none; }
        .search-bar-wrapper { flex-direction: column; align-items: stretch; }
    }
</style>

<div class="admin-user-scope">
    
    <div class="page-background"></div>

    <div class="container pb-5 admin-content-wrapper">

        <div class="admin-header pt-5 animate-enter">
            <div>
                <h1 class="page-title">User Management</h1>
                <p class="page-subtitle">Kelola akses pengguna dan peran administrator.</p>
            </div>
            <div class="d-none d-md-block text-end">
                <div class="text-white fw-bold">{{ Auth::user()->name }}</div>
                <div class="text-white-50 small">Super Admin</div>
            </div>
        </div>

        <div class="stats-grid animate-enter d-1">
            <div class="stat-card">
                <div class="stat-bg"></div>
                <div class="stat-value">{{ $users->total() }}</div>
                <div class="stat-label">Registered Users</div>
                <i class="fas fa-users stat-icon"></i>
            </div>
            
            <div class="stat-card">
                <div class="stat-bg" style="background: linear-gradient(45deg, #f43f5e, #fb7185);"></div>
                @php $adminCount = \App\Models\User::where('role', 'admin')->count(); @endphp
                <div class="stat-value">{{ $adminCount }}</div>
                <div class="stat-label">Administrators</div>
                <i class="fas fa-user-shield stat-icon"></i>
            </div>

            <div class="stat-card">
                <div class="stat-bg" style="background: linear-gradient(45deg, #3b82f6, #60a5fa);"></div>
                @php $newUsers = \App\Models\User::whereDate('created_at', \Carbon\Carbon::today())->count(); @endphp
                <div class="stat-value">+{{ $newUsers }}</div>
                <div class="stat-label">New Today</div>
                <i class="fas fa-user-plus stat-icon"></i>
            </div>
        </div>

        <div class="search-bar-wrapper animate-enter d-2">
            <form action="{{ route('admin.users.index') }}" method="GET" class="w-100">
                <div class="position-relative">
                    <input type="text" name="search" class="admin-search-input" placeholder="Cari nama atau email pengguna..." value="{{ request('search') }}">
                    <i class="fas fa-search position-absolute text-white-50" style="right: 20px; top: 12px;"></i>
                </div>
            </form>
        </div>

        <div class="animate-enter d-2">
            
            <div class="table-header d-none d-lg-grid">
                <div>Avatar</div>
                <div>User Details</div>
                <div>Email</div>
                <div>Role Access</div>
                <div>Joined</div>
                <div class="text-end">Actions</div>
            </div>

            @forelse($users as $user)
            <div class="glass-row">
                
                <div>
                    <div class="user-avatar-box">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                </div>

                <div>
                    <div class="row-name">{{ $user->name }}</div>
                    <div class="d-lg-none mt-1">
                        @if($user->role == 'admin')
                            <span class="badge-role role-admin" style="font-size: 0.6rem;">ADMIN</span>
                        @else
                            <span class="badge-role role-user" style="font-size: 0.6rem;">USER</span>
                        @endif
                    </div>
                </div>

                <div class="col-hide-mobile row-email">
                    {{ $user->email }}
                </div>

                <div class="col-hide-mobile">
                    @if($user->role == 'admin')
                        <span class="badge-role role-admin"><i class="fas fa-shield-alt"></i> ADMIN</span>
                    @else
                        <span class="badge-role role-user"><i class="fas fa-user"></i> USER</span>
                    @endif
                </div>

                <div class="col-hide-mobile text-white-50 small">
                    {{ $user->created_at->format('d M Y') }}
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <form action="{{ route('admin.users.toggle-role', $user->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="action-btn btn-toggle" title="Ubah Role (Admin/User)">
                            <i class="fas fa-user-cog"></i>
                        </button>
                    </form>

                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin hapus user ini? Data tidak bisa dikembalikan.')">
                        @csrf @method('DELETE')
                        <button class="action-btn btn-delete" title="Hapus User">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>

            </div>
            @empty
            <div class="text-center py-5">
                <div class="mb-3 opacity-50">
                    <i class="fas fa-users-slash fa-3x text-white"></i>
                </div>
                <h5 class="text-white">Tidak ada user ditemukan.</h5>
            </div>
            @endforelse

            @if($users->hasPages())
            <div class="mt-4">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
            @endif

        </div>

        <div style="height: 100px;"></div>
    </div>
</div>

@endsection
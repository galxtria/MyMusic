@extends('layouts.app')

@section('content')

<style>
    /* --- OVERRIDE LAYOUT --- */
    #sidebar, .top-header { display: none !important; }
    #content { margin-left: 0 !important; padding: 0 !important; }

    /* --- PREMIUM DARK PALETTE --- */
    :root {
        --bg-deep: #050b14;
        --glass-surface: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.08);
        --accent-blue: #2C74B3;
        --accent-glow: rgba(44, 116, 179, 0.4);
    }

    body { 
        background-color: var(--bg-deep); 
        background-image: 
            radial-gradient(circle at 90% 10%, rgba(44, 116, 179, 0.12), transparent 40%),
            radial-gradient(circle at 10% 90%, rgba(20, 66, 114, 0.12), transparent 40%);
        font-family: 'Plus Jakarta Sans', sans-serif; 
        color: #FFFFFF;
        overflow-x: hidden;
    }

    .register-wrapper {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 20px;
    }

    /* --- GLASS CARD EFFECT --- */
    .register-card {
        width: 100%;
        max-width: 480px; /* Sedikit lebih lebar untuk form pendaftaran */
        background: var(--glass-surface);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 28px;
        padding: 40px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        animation: fadeInSlide 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
    }

    @keyframes fadeInSlide {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .app-logo {
        text-align: center;
        margin-bottom: 24px;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .logo-icon-wrapper {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #144272, #2C74B3);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 16px var(--accent-glow);
    }

    .logo-text {
        font-size: 1.6rem;
        font-weight: 800;
        letter-spacing: -1px;
        color: white;
    }

    .register-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .register-header h2 {
        font-size: 1.4rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 8px;
    }

    .register-header p {
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.9rem;
    }

    /* --- INPUT STYLE --- */
    .form-group { margin-bottom: 18px; }
    
    .input-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 8px;
        display: block;
        padding-left: 4px;
    }

    .input-container {
        position: relative;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 12px;
        border: 1px solid var(--glass-border);
        transition: all 0.3s ease;
    }

    .input-container:focus-within {
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 4px var(--accent-glow);
        background: rgba(0, 0, 0, 0.3);
    }

    .input-container i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.3);
        font-size: 0.9rem;
    }

    .form-input {
        width: 100%;
        background: transparent;
        border: none;
        padding: 14px 15px 14px 45px;
        color: white;
        outline: none;
        font-size: 0.95rem;
    }

    .form-input::placeholder {
        color: rgba(255, 255, 255, 0.2);
    }

    /* --- PRIMARY BUTTON --- */
    .btn-register-premium {
        width: 100%;
        background: linear-gradient(135deg, #144272, #2C74B3);
        color: white;
        border: none;
        padding: 16px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        margin-top: 10px;
    }

    .btn-register-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 25px var(--accent-glow);
        filter: brightness(1.1);
    }

    .footer-links {
        text-align: center;
        margin-top: 30px;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.5);
    }

    .text-accent {
        color: var(--accent-blue);
        text-decoration: none;
        font-weight: 700;
    }

    .text-accent:hover { text-decoration: underline; }

    /* Custom Switch / Checkbox if needed */
    .form-text-muted {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.4);
        margin-top: 6px;
        display: block;
        padding-left: 4px;
    }

</style>

<div class="register-wrapper">
    <div class="register-card">
        <a href="{{ url('/') }}" class="app-logo">
            <div class="logo-icon-wrapper">
                <i class="fas fa-compact-disc fa-lg text-white"></i>
            </div>
            <span class="logo-text">MyMusic</span>
        </a>

        <div class="register-header">
            <h2>Buat Akun Baru</h2>
            <p>Mulai petualangan musikmu hari ini.</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="form-group">
                <label class="input-label">Nama Profil</label>
                <div class="input-container">
                    <i class="fas fa-user"></i>
                    <input id="name" type="text" name="name" class="form-input @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Nama lengkap Anda" required autofocus>
                </div>
                @error('name')
                    <span class="text-danger small mt-2 d-block">{{ $message }}</span>
                @enderror
                <span class="form-text-muted">Nama ini akan muncul di profil publik Anda.</span>
            </div>

            <div class="form-group">
                <label class="input-label">Alamat Email</label>
                <div class="input-container">
                    <i class="fas fa-envelope"></i>
                    <input id="email" type="email" name="email" class="form-input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="email@contoh.com" required>
                </div>
                @error('email')
                    <span class="text-danger small mt-2 d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label">Kata Sandi</label>
                <div class="input-container">
                    <i class="fas fa-lock"></i>
                    <input id="password" type="password" name="password" class="form-input @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required>
                </div>
                @error('password')
                    <span class="text-danger small mt-2 d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label">Konfirmasi Kata Sandi</label>
                <div class="input-container">
                    <i class="fas fa-shield-alt"></i>
                    <input id="password-confirm" type="password" name="password_confirmation" class="form-input" placeholder="Ulangi kata sandi" required>
                </div>
            </div>

            <button type="submit" class="btn-register-premium">
                Buat Akun Sekarang
            </button>
        </form>

        <div class="footer-links">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-accent">Masuk di sini</a>
        </div>
    </div>
</div>

@endsection
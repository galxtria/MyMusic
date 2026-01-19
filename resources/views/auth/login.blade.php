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
            radial-gradient(circle at 10% 20%, rgba(44, 116, 179, 0.1), transparent 40%),
            radial-gradient(circle at 90% 80%, rgba(20, 66, 114, 0.1), transparent 40%);
        font-family: 'Plus Jakarta Sans', sans-serif; 
        color: #FFFFFF;
        overflow: hidden;
    }

    .login-wrapper {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    /* --- GLASS CARD EFFECT --- */
    .login-card {
        width: 100%;
        max-width: 420px;
        background: var(--glass-surface);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
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
        margin-bottom: 32px;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .logo-icon-wrapper {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #144272, #2C74B3);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 20px var(--accent-glow);
        margin-bottom: 8px;
    }

    .logo-text {
        font-size: 1.8rem;
        font-weight: 800;
        letter-spacing: -1px;
        color: white;
    }

    /* --- SOCIAL LOGIN GHOST STYLE --- */
    .social-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 12px;
        margin-bottom: 24px;
    }

    .btn-social-icon {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        color: white;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }

    .btn-social-icon:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: var(--accent-blue);
        transform: translateY(-2px);
    }

    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 24px 0;
        color: rgba(255, 255, 255, 0.3);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .divider::before, .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid var(--glass-border);
    }

    .divider span { padding: 0 15px; }

    /* --- INPUT STYLE --- */
    .form-group { margin-bottom: 20px; }
    
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
    }

    .input-container i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.4);
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

    /* --- PRIMARY BUTTON --- */
    .btn-login-premium {
        width: 100%;
        background: linear-gradient(135deg, #144272, #2C74B3);
        color: white;
        border: none;
        padding: 14px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    }

    .btn-login-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 25px var(--accent-glow);
        filter: brightness(1.1);
    }

    .footer-links {
        text-align: center;
        margin-top: 24px;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.5);
    }

    .text-accent {
        color: var(--accent-blue);
        text-decoration: none;
        font-weight: 700;
    }

    .text-accent:hover { text-decoration: underline; }

</style>

<div class="login-wrapper">
    <div class="login-card">
        <a href="{{ url('/') }}" class="app-logo">
            <div class="logo-icon-wrapper">
                <i class="fas fa-compact-disc fa-2x text-white"></i>
            </div>
            <span class="logo-text">MyMusic</span>
        </a>

        <div class="social-grid">
            <button class="btn-social-icon" title="Google"><i class="fab fa-google"></i></button>
            <button class="btn-social-icon" title="Facebook"><i class="fab fa-facebook-f"></i></button>
            <button class="btn-social-icon" title="Apple"><i class="fab fa-apple"></i></button>
        </div>

        <div class="divider"><span>ATAU MASUK DENGAN EMAIL</span></div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <div class="input-container">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" class="form-input" placeholder="Alamat Email" required autofocus>
                </div>
                @error('email')
                    <span class="text-danger small mt-2 d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <div class="input-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" class="form-input" placeholder="Kata Sandi" required>
                </div>
                @error('password')
                    <span class="text-danger small mt-2 d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 px-1">
                <div class="form-check m-0 p-0 d-flex align-items-center">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input" style="margin-right: 8px; cursor:pointer;">
                    <label for="remember" class="small text-white-50 p-0 m-0" style="cursor:pointer;">Ingat saya</label>
                </div>
                <a href="{{ route('password.request') }}" class="small text-accent">Lupa Sandi?</a>
            </div>

            <button type="submit" class="btn-login-premium">Masuk ke Akun</button>
        </form>

        <div class="footer-links">
            Belum punya akun? <a href="{{ route('register') }}" class="text-accent">Daftar Sekarang</a>
        </div>
    </div>
</div>
@endsection
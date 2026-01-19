@extends('layouts.app')

@section('content')

<style>
    /* --- RESET & GLOBAL --- */
    #sidebar, .top-header, .player-bar { display: none !important; }
    #content { margin-left: 0 !important; padding: 0 !important; overflow-x: hidden; }

    :root {
        --c-primary: #2C74B3;
        --c-accent: #5ba4e5;
        --c-dark: #050b14;
        --glass: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.08);
    }

    body {
        background-color: var(--c-dark);
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: white;
    }

    /* --- ANIMATIONS --- */
    @keyframes float {
        0% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(2deg); }
        100% { transform: translateY(0px) rotate(0deg); }
    }

    @keyframes pulse-glow {
        0% { box-shadow: 0 0 0 0 rgba(44, 116, 179, 0.4); }
        70% { box-shadow: 0 0 0 20px rgba(44, 116, 179, 0); }
        100% { box-shadow: 0 0 0 0 rgba(44, 116, 179, 0); }
    }

    /* --- NAVBAR --- */
    .landing-nav {
        background: rgba(5, 11, 20, 0.8);
        backdrop-filter: blur(20px);
        height: 90px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 8%;
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
        border-bottom: 1px solid var(--glass-border);
    }

    .logo-brand {
        color: white;
        font-weight: 800;
        font-size: 1.8rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 12px;
        letter-spacing: -1px;
    }

    .logo-brand i { color: var(--c-primary); }

    .nav-btn-login {
        color: white;
        text-decoration: none;
        font-weight: 700;
        padding: 10px 25px;
        transition: 0.3s;
    }

    .nav-btn-signup {
        background: white;
        color: black !important;
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 800;
        text-decoration: none;
        transition: 0.3s;
    }

    .nav-btn-signup:hover { transform: scale(1.05); background: var(--c-accent); color: white !important; }

    /* --- HERO SECTION --- */
    .hero-section {
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding: 120px 8% 60px;
        position: relative;
        overflow: hidden;
    }

    /* Efek Cahaya Latar Belakang */
    .hero-glow {
        position: absolute;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(44, 116, 179, 0.2) 0%, transparent 70%);
        top: -100px;
        right: -100px;
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 10;
        max-width: 700px;
    }

    .hero-badge {
        display: inline-block;
        padding: 8px 20px;
        background: var(--glass);
        border: 1px solid var(--glass-border);
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--c-accent);
        margin-bottom: 25px;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .hero-title {
        font-size: clamp(3rem, 8vw, 5.5rem);
        font-weight: 900;
        line-height: 0.95;
        margin-bottom: 30px;
        letter-spacing: -3px;
    }

    .hero-title span {
        background: linear-gradient(to right, #ffffff, #2C74B3);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        color: var(--text-muted);
        line-height: 1.6;
        margin-bottom: 45px;
        max-width: 500px;
    }

    .hero-visual {
        position: absolute;
        right: 5%;
        top: 55%;
        transform: translateY(-50%);
        width: 450px;
        z-index: 5;
        animation: float 6s ease-in-out infinite;
    }

    .hero-visual img {
        width: 100%;
        border-radius: 30px;
        box-shadow: 0 50px 100px rgba(0,0,0,0.8);
        border: 1px solid var(--glass-border);
    }

    /* --- FEATURE CARDS --- */
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        padding: 0 8% 100px;
    }

    .f-card {
        background: var(--glass);
        border: 1px solid var(--glass-border);
        padding: 40px;
        border-radius: 32px;
        transition: 0.4s;
    }

    .f-card:hover {
        background: rgba(255,255,255,0.06);
        transform: translateY(-10px);
        border-color: var(--c-primary);
    }

    .f-icon {
        width: 60px;
        height: 60px;
        background: var(--c-primary);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 25px;
    }

    /* --- CTA BUTTON --- */
    .btn-cta {
        background: var(--c-primary);
        color: white;
        padding: 20px 45px;
        border-radius: 50px;
        font-weight: 800;
        text-decoration: none;
        display: inline-block;
        font-size: 1.1rem;
        transition: 0.3s;
        animation: pulse-glow 2s infinite;
        border: none;
    }

    .btn-cta:hover {
        background: var(--c-accent);
        color: white;
        transform: scale(1.05);
    }

    /* --- FOOTER --- */
    .landing-footer {
        padding: 80px 8% 40px;
        border-top: 1px solid var(--glass-border);
        text-align: center;
    }

    @media (max-width: 1100px) {
        .hero-visual { display: none; }
        .hero-content { max-width: 100%; text-align: center; }
        .hero-subtitle { margin: 0 auto 45px; }
    }
</style>

<nav class="landing-nav">
    <a href="#" class="logo-brand">
        <i class="fas fa-compact-disc fa-spin-hover"></i>MyMusic
    </a>

    <div class="nav-links d-none d-md-flex">
        @guest
            <a href="{{ route('login') }}" class="nav-btn-login">Log In</a>
            <a href="{{ route('register') }}" class="nav-btn-signup shadow-sm">Sign Up Free</a>
        @else
            <a href="{{ url('/home') }}" class="nav-btn-signup">Open Dashboard</a>
        @endguest
    </div>
</nav>

<div class="hero-section">
    <div class="hero-glow"></div>
    
    <div class="hero-content">
        <div class="hero-badge">Next-Gen Audio Experience</div>
        <h1 class="hero-title">Your <span>Rhythm</span>,<br>Your Rules.</h1>
        <p class="hero-subtitle">Stream millions of tracks, sync lyrics in real-time, and discover your next favorite artist in crystal clear quality.</p>

        @auth
            <a href="{{ url('/home') }}" class="btn-cta">Go to Player <i class="fas fa-arrow-right ms-2"></i></a>
        @else
            <a href="{{ route('register') }}" class="btn-cta">Start Listening Now <i class="fas fa-play-circle ms-2"></i></a>
        @endauth
    </div>

    <div class="hero-visual">
        <img src="https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?auto=format&fit=crop&q=80&w=1000" alt="Music Experience">
    </div>
</div>

<div class="features-grid">
    <div class="f-card">
        <div class="f-icon"><i class="fas fa-bolt"></i></div>
        <h3>Ultra Stream</h3>
        <p class="text-white-50">Zero lag, zero buffering. High-fidelity audio streaming tailored for your connection.</p>
    </div>
    <div class="f-card">
        <div class="f-icon" style="background: #ec4899;"><i class="fas fa-microphone"></i></div>
        <h3>Live Lyrics</h3>
        <p class="text-white-50">Sing along with frame-accurate lyrics synchronization inspired by Apple Music.</p>
    </div>
    <div class="f-card">
        <div class="f-icon" style="background: #8b5cf6;"><i class="fas fa-layer-group"></i></div>
        <h3>Smart Library</h3>
        <p class="text-white-50">Organize your music with ease. Create playlists and follow your top artists.</p>
    </div>
</div>

<footer class="landing-footer">
    <div class="mb-4">
        <a href="#" class="logo-brand justify-content-center mb-3">
            <i class="fas fa-compact-disc"></i>MyMusic
        </a>
        <p class="text-white-50 small">Crafted for music lovers around the globe.</p>
    </div>
    <div class="d-flex justify-content-center gap-4 mb-4">
        <a href="#" class="text-white-50 hover-white"><i class="fab fa-instagram fa-lg"></i></a>
        <a href="#" class="text-white-50 hover-white"><i class="fab fa-twitter fa-lg"></i></a>
        <a href="#" class="text-white-50 hover-white"><i class="fab fa-spotify fa-lg"></i></a>
    </div>
    <p class="text-white-50" style="font-size: 0.8rem;">&copy; {{ date('Y') }} MyMusic AB. Built with passion and code.</p>
</footer>

@endsection
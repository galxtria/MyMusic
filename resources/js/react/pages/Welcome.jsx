import { ArrowRight, AudioLines, Compass, Disc3, Play, Radio, Search, Sparkles } from 'lucide-react';

export default function Welcome({ user }) {
  return (
    <div style={{ minHeight: '100vh', background: '#060608' }}>
      <nav style={{ position: 'fixed', top: 0, left: 0, right: 0, zIndex: 50, height: 76, display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '0 6%', background: 'rgba(6,6,8,0.9)', borderBottom: '1px solid var(--mm-line)' }}>
        <a href="/" style={{ display: 'flex', alignItems: 'center', gap: 10, fontWeight: 800, fontSize: '1.25rem', textDecoration: 'none' }}>
          <span className="mm-logo-badge" style={{ width: 34, height: 34 }}><Disc3 size={19} /></span>
          MyMusic
        </a>
        <div style={{ display: 'flex', gap: 18, alignItems: 'center' }}>
          {user ? <a href="/home" className="mm-btn-primary" style={{ padding: '10px 24px' }}>Open Dashboard <ArrowRight size={15} /></a> : (
            <>
              <a href="/login" style={{ color: 'var(--mm-dim)', fontWeight: 600, textDecoration: 'none', fontSize: '0.9rem' }}>Log in</a>
              <a href="/register" className="mm-btn-primary" style={{ padding: '10px 24px' }}>Get Started Free</a>
            </>
          )}
        </div>
      </nav>

      <header style={{ minHeight: '100vh', display: 'flex', alignItems: 'center', padding: '120px 6% 60px', position: 'relative', overflow: 'hidden' }}>
        <div style={{ maxWidth: 620, position: 'relative', zIndex: 2 }} className="mm-enter">
          <span className="mm-badge" style={{ marginBottom: 24 }}><Sparkles size={13} /> NEXT-GEN AUDIO EXPERIENCE</span>
          <h1 className="mm-title-gradient" style={{ fontSize: 'clamp(2.8rem, 6.5vw, 5rem)', marginBottom: 20 }}>Your rhythm,<br />your rules.</h1>
          <p style={{ color: 'var(--mm-dim)', fontSize: '1.05rem', lineHeight: 1.7, marginBottom: 36, maxWidth: 520 }}>
            Stream unlimited tracks, discover music by mood, sync lyrics in real time — all in a calm, distraction-free player.
          </p>
          <div style={{ display: 'flex', gap: 14, flexWrap: 'wrap' }}>
            <a href={user ? '/home' : '/register'} className="mm-btn-primary" style={{ padding: '15px 36px', fontSize: '1rem' }}>
              <Play size={17} /> {user ? 'Launch Player' : 'Start Listening'}
            </a>
            <a href="/search" className="mm-btn-ghost" style={{ padding: '15px 30px' }}><Search size={16} /> Explore Music</a>
          </div>
        </div>
        <div className="mm-hide-mobile" style={{ position: 'absolute', right: '6%', top: '50%', transform: 'translateY(-50%)', width: '38%', zIndex: 1 }}>
          <div className="mm-glass" style={{ borderRadius: 22, padding: 20 }}>
            <img src="https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?auto=format&fit=crop&q=80&w=1000" alt="Music" style={{ width: '100%', borderRadius: 14, display: 'block' }} />
            <div style={{ display: 'flex', alignItems: 'center', gap: 14, marginTop: 18 }}>
              <span className="mm-play-fab" style={{ width: 50, height: 50 }}><Play size={19} style={{ marginLeft: 2 }} /></span>
              <div>
                <div style={{ fontWeight: 800, fontSize: '0.95rem' }}>Midnight Drive</div>
                <div style={{ color: 'var(--mm-dim)', fontSize: '0.82rem' }}>Synthwave Collective</div>
              </div>
              <AudioLines size={20} style={{ marginLeft: 'auto', color: 'var(--mm-accent)' }} />
            </div>
          </div>
        </div>
      </header>

      <section style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(270px, 1fr))', gap: 16, padding: '0 6% 100px' }}>
        {[
          { icon: Radio, title: 'Unlimited Streaming', desc: 'Search and stream millions of tracks powered by online sources. No upload needed, just press play.' },
          { icon: AudioLines, title: 'Live Playback', desc: 'A focused player with queue, shuffle, repeat, and synced lyrics for every track.' },
          { icon: Compass, title: 'Mood Discovery', desc: 'Pick a vibe — Relax, Sad, Party or Focus — and get a curated mix instantly.' },
        ].map((f, i) => (
          <div key={i} className="mm-glass mm-enter" style={{ padding: 30, borderRadius: 20 }}>
            <div style={{ width: 52, height: 52, borderRadius: 15, display: 'grid', placeItems: 'center', background: 'var(--mm-accent)', marginBottom: 20 }}>
              <f.icon size={23} color="#fff" />
            </div>
            <h3 style={{ margin: '0 0 10px', fontSize: '1.05rem' }}>{f.title}</h3>
            <p style={{ color: 'var(--mm-dim)', lineHeight: 1.65, margin: 0, fontSize: '0.9rem' }}>{f.desc}</p>
          </div>
        ))}
      </section>

      <footer style={{ padding: '40px 6% 32px', borderTop: '1px solid var(--mm-line)', textAlign: 'center', color: 'var(--mm-faint)', fontSize: '0.83rem' }}>
        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: 9, fontWeight: 800, fontSize: '1.1rem', color: '#fff', marginBottom: 8 }}>
          <Disc3 size={19} color="#3b82f6" /> MyMusic
        </div>
        Crafted for music lovers • {new Date().getFullYear()}
      </footer>
    </div>
  );
}

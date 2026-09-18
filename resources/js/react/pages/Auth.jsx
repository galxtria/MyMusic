import { useEffect } from 'react';
import { Disc3, Lock, LogIn, Mail, ShieldCheck, User, UserPlus } from 'lucide-react';
import { clearPersistedPlayerState } from '../lib/player';

function Shell({ title, subtitle, children, footer }) {
  return (
    <div className="mm-auth-wrap" style={{ background: '#060608' }}>
      <div className="mm-auth-card">
        <a href="/" style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 10, textDecoration: 'none', marginBottom: 22 }}>
          <span className="mm-logo-badge" style={{ width: 52, height: 52 }}><Disc3 size={26} /></span>
          <span style={{ fontWeight: 800, fontSize: '1.4rem' }}>MyMusic</span>
        </a>
        <div style={{ textAlign: 'center', marginBottom: 24 }}>
          <h2 style={{ margin: '0 0 6px' }}>{title}</h2>
          <p style={{ color: 'var(--mm-dim)', fontSize: '0.9rem', margin: 0 }}>{subtitle}</p>
        </div>
        {children}
        <div style={{ textAlign: 'center', marginTop: 24, fontSize: '0.88rem', color: 'var(--mm-dim)' }}>{footer}</div>
      </div>
    </div>
  );
}

export function Login({ errors = {} }) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
  // Buang sisa antrean user sebelumnya agar login baru tidak auto-start lagu lama.
  useEffect(() => { clearPersistedPlayerState(); }, []);
  return (
    <Shell title="Welcome back" subtitle="Log in to continue your session." footer={<>No account yet? <a href="/register" style={{ color: 'var(--mm-sky)', fontWeight: 700 }}>Sign up</a></>}>
      <form action="/login" method="POST">
        <input type="hidden" name="_token" value={csrf} />
        <label className="mm-label">Email address</label>
        <div className="mm-field"><Mail size={16} /><input name="email" type="email" required className="mm-input" placeholder="you@example.com" /></div>
        {errors.email && <div style={{ color: '#f87171', fontSize: '0.8rem', marginBottom: 10 }}>{errors.email}</div>}
        <label className="mm-label">Password</label>
        <div className="mm-field"><Lock size={16} /><input name="password" type="password" required className="mm-input" placeholder="Your password" /></div>
        {errors.password && <div style={{ color: '#f87171', fontSize: '0.8rem', marginBottom: 10 }}>{errors.password}</div>}
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', margin: '6px 0 18px', fontSize: '0.85rem' }}>
          <label style={{ display: 'flex', gap: 8, alignItems: 'center', color: 'var(--mm-dim)' }}><input type="checkbox" name="remember" /> Remember me</label>
          <a href="/password/reset" style={{ color: 'var(--mm-sky)', fontWeight: 700, textDecoration: 'none' }}>Forgot password?</a>
        </div>
        <button type="submit" className="mm-btn-primary" style={{ width: '100%', justifyContent: 'center' }}><LogIn size={16} /> Log in</button>
      </form>
    </Shell>
  );
}

export function Register({ errors = {} }) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
  useEffect(() => { clearPersistedPlayerState(); }, []);
  return (
    <Shell title="Create your account" subtitle="Start your music journey today." footer={<>Already have an account? <a href="/login" style={{ color: 'var(--mm-sky)', fontWeight: 700 }}>Log in</a></>}>
      <form action="/register" method="POST">
        <input type="hidden" name="_token" value={csrf} />
        <label className="mm-label">Profile name</label>
        <div className="mm-field"><User size={16} /><input name="name" required className="mm-input" placeholder="Your name" /></div>
        {errors.name && <div style={{ color: '#f87171', fontSize: '0.8rem', marginBottom: 10 }}>{errors.name}</div>}
        <label className="mm-label">Email address</label>
        <div className="mm-field"><Mail size={16} /><input name="email" type="email" required className="mm-input" placeholder="you@example.com" /></div>
        {errors.email && <div style={{ color: '#f87171', fontSize: '0.8rem', marginBottom: 10 }}>{errors.email}</div>}
        <label className="mm-label">Password</label>
        <div className="mm-field"><Lock size={16} /><input name="password" type="password" required className="mm-input" placeholder="Min 8 characters" /></div>
        {errors.password && <div style={{ color: '#f87171', fontSize: '0.8rem', marginBottom: 10 }}>{errors.password}</div>}
        <label className="mm-label">Confirm password</label>
        <div className="mm-field"><ShieldCheck size={16} /><input name="password_confirmation" type="password" required className="mm-input" placeholder="Repeat password" /></div>
        <button type="submit" className="mm-btn-primary" style={{ width: '100%', justifyContent: 'center', marginTop: 8 }}><UserPlus size={16} /> Create account</button>
      </form>
    </Shell>
  );
}

import {
  AlarmClock, CircleAlert, Disc3, Heart, ListMusic, LoaderCircle, Play, Plus, SearchX, Sparkles,
} from 'lucide-react';
import { songCover } from '../lib/api';
import { usePlayer } from '../lib/player';

export function PageHeader({ badge, title, subtitle, action }) {
  return (
    <div className="mm-enter" style={{ marginBottom: 26 }}>
      {badge && <span className="mm-badge" style={{ marginBottom: 14 }}><Sparkles size={13} />{badge}</span>}
      <div style={{ display: 'flex', alignItems: 'flex-end', justifyContent: 'space-between', gap: 18, flexWrap: 'wrap' }}>
        <div>
          <h1 className="mm-title-gradient" style={{ fontSize: 'clamp(2.2rem, 4.5vw, 3.4rem)' }}>{title}</h1>
          {subtitle && <p style={{ color: 'var(--mm-dim)', fontWeight: 600, margin: '10px 0 0' }}>{subtitle}</p>}
        </div>
        {action}
      </div>
    </div>
  );
}

export function SectionHeader({ icon, title, linkText, linkHref }) {
  const Icon = icon;
  return (
    <div className="mm-section">
      {Icon && <Icon size={20} />}
      <span>{title}</span>
      {linkText && (
        <a href={linkHref} style={{ marginLeft: 'auto', fontSize: '0.78rem', letterSpacing: 1.5, color: 'var(--mm-dim)', textDecoration: 'none', fontWeight: 800 }}>
          {linkText}
        </a>
      )}
    </div>
  );
}

export function SongCard({ song, onPlay, action }) {
  const { likedIds } = usePlayer();
  const liked = likedIds.has(String(song.id));
  return (
    <div className="mm-song-card mm-enter" onClick={onPlay}>
      <div className="mm-cover-wrap">
        <img src={songCover(song)} alt={song.title} loading="lazy" onError={(e) => { e.currentTarget.src = '/images/default-cover.png'; }} />
        <div className="mm-card-play"><span><Play size={20} style={{ marginLeft: 2 }} /></span></div>
      </div>
      <div style={{ display: 'flex', gap: 8, alignItems: 'flex-start' }}>
        <div style={{ minWidth: 0, flex: 1 }}>
          <div className="mm-song-title">{song.title}</div>
          <div className="mm-song-artist">{song.artist}</div>
        </div>
        {liked && <Heart size={15} color="#3b82f6" fill="#3b82f6" style={{ flexShrink: 0, marginTop: 3 }} />}
        {action}
      </div>
    </div>
  );
}

export function SkeletonGrid({ count = 10 }) {
  return (
    <div className="mm-grid-songs">
      {Array.from({ length: count }).map((_, i) => (
        <div key={i} className="mm-glass" style={{ padding: 14 }}>
          <div className="mm-skeleton" style={{ aspectRatio: '1/1', marginBottom: 12 }} />
          <div className="mm-skeleton" style={{ height: 14, marginBottom: 8 }} />
          <div className="mm-skeleton" style={{ height: 12, width: '60%' }} />
        </div>
      ))}
    </div>
  );
}

export function EmptyState({ icon: Icon = SearchX, title, message, action }) {
  return (
    <div className="mm-empty mm-enter">
      <div className="mm-empty-icon"><Icon size={34} /></div>
      <h3 style={{ color: '#fff', margin: '0 0 8px' }}>{title}</h3>
      <p style={{ margin: '0 0 20px' }}>{message}</p>
      {action}
    </div>
  );
}

export function LoadingRow({ text = 'Loading tracks...' }) {
  return (
    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: 12, padding: 44, color: 'var(--mm-sky)', fontWeight: 700 }}>
      <LoaderCircle size={22} className="mm-spin" style={{ animation: 'mm-spin 1s linear infinite' }} />
      {text}
    </div>
  );
}

export function ErrorRow({ text = 'Failed to load. Please try again.' }) {
  return (
    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: 10, padding: 40, color: '#f87171', fontWeight: 700 }}>
      <CircleAlert size={20} />{text}
    </div>
  );
}

export function TrackRow({ song, position, meta, onPlay, trailing }) {
  return (
    <div className="mm-table-row" onClick={onPlay}>
      <div style={{ color: 'var(--mm-faint)', fontWeight: 800, fontFamily: 'monospace' }}>{position}</div>
      <div style={{ display: 'flex', alignItems: 'center', gap: 14, minWidth: 0 }}>
        <img src={songCover(song)} alt="" style={{ width: 46, height: 46, borderRadius: 10, objectFit: 'cover', flexShrink: 0 }} onError={(e) => { e.currentTarget.src = '/images/default-cover.png'; }} />
        <div style={{ minWidth: 0 }}>
          <div style={{ fontWeight: 800, whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>{song.title}</div>
          <div style={{ color: 'var(--mm-dim)', fontSize: '0.82rem', fontWeight: 600 }}>{song.artist}</div>
        </div>
      </div>
      <div className="mm-hide-mobile" style={{ color: 'var(--mm-dim)', fontWeight: 600, fontSize: '0.85rem' }}>{meta ?? song.genre ?? ''}</div>
      <div className="mm-hide-mobile" style={{ color: 'var(--mm-dim)', fontFamily: 'monospace', fontSize: '0.85rem', display: 'flex', alignItems: 'center', gap: 6 }}>
        <AlarmClock size={13} />{song.duration || '0:30'}
      </div>
      <div style={{ display: 'flex', justifyContent: 'flex-end', alignItems: 'center', gap: 8 }} onClick={(e) => e.stopPropagation()}>
        {trailing}
      </div>
    </div>
  );
}

export function MoodButton({ active, onClick, label, icon: Icon, gradient }) {
  return (
    <button className={`mm-mood-card ${active ? 'active' : ''}`} onClick={onClick} style={{ background: gradient, border: active ? '2px solid #fff' : undefined }}>
      <h3>{label}</h3>
      <Icon size={44} />
    </button>
  );
}

export function AddButton({ onClick, title = 'Add' }) {
  return (
    <button onClick={(e) => { e.stopPropagation(); onClick(); }} title={title} className="mm-icon-btn" style={{ width: 32, height: 32 }}>
      <Plus size={15} />
    </button>
  );
}

export function BrandMark() {
  return (
    <span style={{ display: 'inline-flex', alignItems: 'center', gap: 8, fontWeight: 800 }}>
      <Disc3 size={18} color="#3b82f6" />
      <ListMusic size={16} style={{ opacity: 0.6 }} />
    </span>
  );
}

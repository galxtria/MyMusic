import { Clock, Heart, ListMusic, Play } from 'lucide-react';
import { normalizeSong } from '../lib/api';
import { usePlayer } from '../lib/player';
import { EmptyState } from '../components/ui';
import { LikeButton } from '../components/AppShell';

export default function Favorites({ user, songs = [] }) {
  const p = usePlayer();
  const tracks = songs.map(normalizeSong);

  return (
    <div>
      <div className="mm-enter" style={{ display: 'flex', gap: 30, alignItems: 'flex-end', marginBottom: 28, flexWrap: 'wrap' }}>
        <div className="mm-glass" style={{ width: 190, height: 190, borderRadius: 22, display: 'grid', placeItems: 'center', padding: 24 }}>
          <Heart size={68} color="#fff" fill="#3b82f6" />
        </div>
        <div>
          <span className="mm-badge">PLAYLIST</span>
          <h1 className="mm-title-gradient" style={{ fontSize: 'clamp(2.6rem, 6vw, 4.6rem)', margin: '12px 0' }}>Liked Songs</h1>
          <p style={{ color: 'var(--mm-dim)', fontWeight: 600, margin: 0 }}>{user?.name} • {tracks.length} tracks</p>
        </div>
      </div>

      <button className="mm-btn-primary mm-enter" disabled={tracks.length === 0} onClick={() => p.loadQueue(tracks, 0)} style={{ marginBottom: 26, opacity: tracks.length === 0 ? 0.5 : 1 }}>
        <Play size={17} /> Play all
      </button>

      {tracks.length === 0 ? (
        <EmptyState icon={Heart} title="No liked songs yet" message="Tap the heart on any track and it will live here." action={<a className="mm-btn-primary" href="/home">Find music</a>} />
      ) : (
        <div className="mm-enter mm-fade-1">
          <div className="mm-table-head"><div>#</div><div>Title</div><div className="mm-hide-mobile">Genre</div><div className="mm-hide-mobile">Duration</div><div style={{ textAlign: 'right' }}><Clock size={14} /></div></div>
          {tracks.map((s, i) => (
            <div key={s.id} className="mm-table-row" onClick={() => p.loadQueue(tracks, i)}>
              <div style={{ color: 'var(--mm-faint)', fontWeight: 800, fontFamily: 'monospace' }}>{i + 1}</div>
              <div style={{ display: 'flex', alignItems: 'center', gap: 14, minWidth: 0 }}>
                <img src={s.cover} alt="" style={{ width: 46, height: 46, borderRadius: 10, objectFit: 'cover', flexShrink: 0 }} onError={(e) => { e.currentTarget.src = '/images/default-cover.png'; }} />
                <div style={{ minWidth: 0 }}>
                  <div style={{ fontWeight: 800, whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>{s.title}</div>
                  <div style={{ color: 'var(--mm-dim)', fontSize: '0.82rem' }}>{s.artist}</div>
                </div>
              </div>
              <div className="mm-hide-mobile" style={{ color: 'var(--mm-dim)', fontSize: '0.85rem' }}>{s.genre}</div>
              <div className="mm-hide-mobile" style={{ color: 'var(--mm-dim)', fontFamily: 'monospace', fontSize: '0.85rem' }}>{s.duration || '0:30'}</div>
              <div style={{ display: 'flex', justifyContent: 'flex-end', alignItems: 'center', gap: 6 }} onClick={(e) => e.stopPropagation()}>
                <LikeButton songId={s.id} />
                <span className="mm-icon-btn" style={{ width: 36, height: 36 }} onClick={() => p.loadQueue(tracks, i)}><Play size={14} style={{ marginLeft: 2 }} /></span>
              </div>
            </div>
          ))}
          <div style={{ display: 'flex', alignItems: 'center', gap: 8, color: 'var(--mm-faint)', fontSize: '0.8rem', marginTop: 22 }}>
            <ListMusic size={14} /> Queue position syncs automatically with the bottom player.
          </div>
        </div>
      )}
    </div>
  );
}

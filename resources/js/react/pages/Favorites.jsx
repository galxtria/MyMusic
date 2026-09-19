import { useMemo, useState } from 'react';
import { Clock, Heart, ListMusic, Play, Search as SearchIcon, Shuffle } from 'lucide-react';
import { normalizeSong } from '../lib/api';
import { usePlayer } from '../lib/player';
import { EmptyState, TrackRow } from '../components/ui';
import { LikeButton } from '../components/AppShell';

function toSeconds(dur) {
  if (!dur) return 0;
  const parts = String(dur).split(':').map(Number);
  if (parts.some((n) => Number.isNaN(n))) return 0;
  if (parts.length === 2) return parts[0] * 60 + parts[1];
  if (parts.length === 3) return parts[0] * 3600 + parts[1] * 60 + parts[2];
  return 0;
}

function totalLabel(tracks) {
  const secs = tracks.reduce((a, s) => a + toSeconds(s.duration), 0);
  if (!secs) return '';
  const h = Math.floor(secs / 3600);
  const m = Math.round((secs % 3600) / 60);
  return h > 0 ? `about ${h} hr ${m} min` : `about ${m} min`;
}

export default function Favorites({ user, songs = [] }) {
  const p = usePlayer();
  const tracks = useMemo(() => songs.map(normalizeSong), [songs]);
  const [filter, setFilter] = useState('');

  const shown = useMemo(() => {
    const q = filter.trim().toLowerCase();
    if (!q) return tracks;
    return tracks.filter((s) => `${s.title} ${s.artist}`.toLowerCase().includes(q));
  }, [tracks, filter]);

  const playAll = (shuffleFirst = false) => {
    if (!shown.length) return;
    if (shuffleFirst) {
      p.setShuffle(true);
      const order = [...shown].sort(() => Math.random() - 0.5);
      p.loadQueue(order, 0);
    } else {
      p.loadQueue(shown, 0);
    }
  };

  return (
    <div>
      <div className="mm-enter" style={{ display: 'flex', gap: 30, alignItems: 'flex-end', marginBottom: 24, flexWrap: 'wrap' }}>
        <div className="mm-liked-tile">
          <Heart size={84} color="#fff" fill="#fff" style={{ opacity: 0.95 }} />
        </div>
        <div style={{ flex: 1, minWidth: 260 }}>
          <span className="mm-badge"><Heart size={13} /> PLAYLIST • AUTO-UPDATED</span>
          <h1 className="mm-title-gradient" style={{ fontSize: 'clamp(2.6rem, 6vw, 4.6rem)', margin: '12px 0' }}>Liked Songs</h1>
          <p className="mm-stat-line">
            <strong>{user?.name}</strong> • {tracks.length} songs
            {totalLabel(tracks) && <span>• {totalLabel(tracks)}</span>}
          </p>
        </div>
      </div>

      {tracks.length > 0 && (
        <div className="mm-controls mm-enter">
          <button className="mm-play-fab" onClick={() => playAll(false)} title="Play all"><Play size={22} style={{ marginLeft: 3 }} /></button>
          <button className="mm-btn-ghost" onClick={() => playAll(true)} title="Shuffle play"><Shuffle size={15} /> Shuffle</button>
          <div className="mm-filter" style={{ marginLeft: 'auto' }}>
            <SearchIcon size={15} style={{ color: 'var(--mm-faint)', flexShrink: 0 }} />
            <input value={filter} onChange={(e) => setFilter(e.target.value)} placeholder="Filter liked songs..." />
          </div>
        </div>
      )}

      {tracks.length === 0 ? (
        <EmptyState icon={Heart} title="Songs you like will live here" message="Tap the heart on any track and it will appear in this collection." action={<a className="mm-btn-primary" href="/home">Find music</a>} />
      ) : shown.length === 0 ? (
        <EmptyState icon={SearchIcon} title="No matches" message={`Nothing liked matches "${filter}".`} />
      ) : (
        <div className="mm-enter mm-fade-1">
          <div className="mm-table-head"><div>#</div><div>Title</div><div className="mm-hide-mobile">Genre</div><div className="mm-hide-mobile">Duration</div><div style={{ textAlign: 'right' }}><Clock size={14} /></div></div>
          {shown.map((s, i) => (
            <TrackRow
              key={s.id} song={s} position={i + 1} meta={s.genre} onPlay={() => p.loadQueue(shown, i)}
              trailing={(
                <span style={{ display: 'inline-flex', alignItems: 'center', gap: 2 }}>
                  <LikeButton songId={s.id} />
                  <button className="mm-icon-btn" style={{ width: 36, height: 36 }} onClick={() => p.loadQueue(shown, i)} title="Play"><Play size={14} style={{ marginLeft: 2 }} /></button>
                </span>
              )}
            />
          ))}
          <div style={{ display: 'flex', alignItems: 'center', gap: 8, color: 'var(--mm-faint)', fontSize: '0.8rem', marginTop: 22 }}>
            <ListMusic size={14} /> Queue position syncs automatically with the bottom player.
          </div>
        </div>
      )}
    </div>
  );
}

import { BadgeCheck, Flame, Play } from 'lucide-react';
import { normalizeSong } from '../lib/api';
import { usePlayer } from '../lib/player';
import { SectionHeader, TrackRow } from '../components/ui';
import { LikeButton } from '../components/AppShell';

export default function Artist({ artistName, artistCover, songs = [] }) {
  const p = usePlayer();
  const tracks = songs.map(normalizeSong);

  return (
    <div>
      <div className="mm-hero mm-enter" style={{ marginBottom: 30 }}>
        <div style={{ width: 190, height: 190, flexShrink: 0 }}>
          <img src={artistCover} alt={artistName} style={{ width: '100%', height: '100%', borderRadius: '50%', objectFit: 'cover', border: '1px solid var(--mm-line)' }} onError={(e) => { e.currentTarget.src = '/images/default_artist.jpg'; }} />
        </div>
        <div>
          <span className="mm-badge"><BadgeCheck size={13} /> VERIFIED ARTIST</span>
          <h1 className="mm-title-gradient" style={{ fontSize: 'clamp(2.4rem, 5.5vw, 4.4rem)', margin: '12px 0' }}>{artistName}</h1>
          <p style={{ color: 'var(--mm-dim)', fontWeight: 600, margin: '0 0 20px' }}>{tracks.length} tracks in your library</p>
          <div style={{ display: 'flex', gap: 12, alignItems: 'center' }}>
            <button className="mm-play-fab" onClick={() => tracks.length && p.loadQueue(tracks, 0)}><Play size={22} style={{ marginLeft: 3 }} /></button>
            <button className="mm-btn-ghost">Follow</button>
          </div>
        </div>
      </div>

      <SectionHeader icon={Flame} title="Popular tracks" />
      {tracks.map((s, i) => (
        <TrackRow
          key={s.id}
          song={s}
          position={i + 1}
          meta={`${Math.floor(10000 + Math.random() * 800000).toLocaleString()} plays`}
          onPlay={() => p.loadQueue(tracks, i)}
          trailing={<LikeButton songId={s.id} />}
        />
      ))}
    </div>
  );
}

import { useEffect, useState } from 'react';
import { BadgeCheck, ListPlus, Play, Radio, Share2 } from 'lucide-react';
import { copyLink, fetchRadio, normalizeSong, toggleFavorite } from '../lib/api';
import { usePlayer } from '../lib/player';
import { LikeButton } from '../components/AppShell';
import { EmptyState, SongCard } from '../components/ui';

export default function Share({ song }) {
  const p = usePlayer();
  const track = normalizeSong(song);
  const [related, setRelated] = useState([]);
  const liked = p.likedIds.has(String(track.id));

  // Coba auto-play sekali saat dibuka via link share.
  // (Browser boleh menolak bila belum ada interaksi — user tinggal tekan Play.)
  useEffect(() => {
    if (!track.src) return;
    p.loadQueue([track], 0);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  useEffect(() => {
    if (track.id == null || String(track.id).startsWith('yt-')) return;
    fetchRadio(track.id)
      .then((d) => setRelated((d.results || []).map(normalizeSong).filter((s) => s.src)))
      .catch(() => {});
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const playRadio = async () => {
    if (related.length > 0) {
      p.loadQueue(related, 0);
      p.showToast(`Radio: ${track.title}`);
      return;
    }
    try {
      const d = await fetchRadio(track.id);
      const list = (d.results || []).map(normalizeSong).filter((s) => s.src);
      if (!list.length) {
        p.showToast('No radio tracks found');
        return;
      }
      setRelated(list);
      p.loadQueue(list, 0);
    } catch {
      p.showToast('Could not start radio');
    }
  };

  const onShare = () => copyLink(`${window.location.origin}/s/${track.id}`, p.showToast);

  const onLike = async () => {
    try {
      const d = await toggleFavorite(track.id);
      const isLiked = d.status === 'liked';
      p.markLiked(track.id, isLiked);
      p.showToast(isLiked ? 'Saved to your library' : 'Removed from your library');
    } catch {
      p.showToast('Could not update favorite');
    }
  };

  return (
    <div>
      <div className="mm-hero mm-enter" style={{ marginBottom: 28 }}>
        <img
          src={track.cover} alt={track.title}
          style={{ width: 220, height: 220, borderRadius: 20, objectFit: 'cover', border: '1px solid var(--mm-line)', flexShrink: 0 }}
          onError={(e) => { e.currentTarget.src = '/images/default-cover.png'; }}
        />
        <div style={{ flex: 1, minWidth: 260 }}>
          <span className="mm-badge"><BadgeCheck size={13} /> SHARED TRACK</span>
          <h1 className="mm-title-gradient" style={{ fontSize: 'clamp(2rem, 5vw, 3.8rem)', margin: '12px 0' }}>{track.title}</h1>
          <p style={{ margin: '0 0 20px', color: 'var(--mm-dim)', fontWeight: 600 }}>
            <a href={`/artist/${encodeURIComponent(track.artist)}`} style={{ color: '#fff', fontWeight: 800, textDecoration: 'none' }}>{track.artist}</a>
            {track.genre ? ` • ${track.genre}` : ''}
            {track.duration ? ` • ${track.duration}` : ''}
          </p>
          <div style={{ display: 'flex', gap: 12, alignItems: 'center', flexWrap: 'wrap' }}>
            <button className="mm-play-fab" onClick={() => p.loadQueue([track], 0)} title="Play"><Play size={22} style={{ marginLeft: 3 }} /></button>
            <button className="mm-btn-ghost" onClick={playRadio} title="Play radio"><Radio size={15} /> Radio</button>
            <button className="mm-btn-ghost" onClick={onLike} title="Save to library">
              <LikeButton songId={track.id} /> {liked ? 'Saved' : 'Save'}
            </button>
            <button className="mm-btn-ghost" onClick={() => p.setPlaylistModalSong(track.id)} title="Save to playlist"><ListPlus size={15} /> Playlist</button>
            <button className="mm-btn-ghost" onClick={onShare} title="Copy share link"><Share2 size={15} /> Share</button>
          </div>
        </div>
      </div>

      {related.length > 0 && (
        <>
          <div className="mm-section"><Radio size={18} style={{ color: 'var(--mm-teal)' }} /><span>More like this</span></div>
          <div className="mm-grid-songs">
            {related.map((s, i) => (
              <SongCard key={`rel-${s.id}-${i}`} song={s} onPlay={() => p.loadQueue(related, i)} />
            ))}
          </div>
        </>
      )}
      {related.length === 0 && (
        <EmptyState icon={Radio} title="No related tracks yet" message="Press play above to listen to this shared track." />
      )}
    </div>
  );
}

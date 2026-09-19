import { useState } from 'react';
import { CalendarDays, CopyPlus, Globe, ListMusic, Lock, Pencil, Play, Share2, Shuffle, Trash2, User } from 'lucide-react';
import { copyLink, normalizeSong, togglePlaylistPublic } from '../lib/api';
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

export default function PlaylistDetail({ playlist, user, isOwner = true, ownerName = '' }) {
  const p = usePlayer();
  const tracks = (playlist.songs || []).map(normalizeSong);
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
  const cover = playlist.cover_path ? `/${String(playlist.cover_path).replace(/^\//, '')}` : '/images/default_playlist.jpg';
  const [isPublic, setIsPublic] = useState(!!playlist.is_public);
  const [shareUrl, setShareUrl] = useState(playlist.share_url || (playlist.share_token ? `${window.location.origin}/p/${playlist.share_token}` : ''));
  const secs = tracks.reduce((a, s) => a + toSeconds(s.duration), 0);
  const durLabel = secs > 0 ? `about ${Math.floor(secs / 60)} min` : '';

  const playAll = (shuffleFirst = false) => {
    if (!tracks.length) return;
    if (shuffleFirst) {
      p.setShuffle(true);
      p.loadQueue([...tracks].sort(() => Math.random() - 0.5), 0);
    } else {
      p.loadQueue(tracks, 0);
    }
  };

  const onTogglePublic = async () => {
    try {
      const d = await togglePlaylistPublic(playlist.id);
      setIsPublic(!!d.is_public);
      if (d.share_url) setShareUrl(d.share_url);
      p.showToast(d.message);
    } catch {
      p.showToast('Could not change visibility');
    }
  };

  return (
    <div>
      <div className="mm-pl-hero mm-enter">
        <img src={cover} alt={playlist.name} className="mm-pl-cover" onError={(e) => { e.currentTarget.src = '/images/default_playlist.jpg'; }} />
        <div style={{ flex: 1, minWidth: 260 }}>
          <span className="mm-badge">
            <ListMusic size={13} /> {isOwner ? 'PERSONAL PLAYLIST' : 'SHARED PLAYLIST'}
          </span>
          <h1 className="mm-title-gradient" style={{ fontSize: 'clamp(2.2rem, 5.5vw, 4.2rem)', margin: '12px 0' }}>{playlist.name}</h1>
          {playlist.description && <p className="mm-pl-desc">{playlist.description}</p>}
          <p className="mm-stat-line">
            <span style={{ display: 'inline-flex', alignItems: 'center', gap: 6, color: '#fff', fontWeight: 800 }}><User size={14} /> {isOwner ? user?.name : ownerName}</span>
            <span>• {tracks.length} songs {durLabel && `• ${durLabel}`}</span>
            <span style={{ display: 'inline-flex', alignItems: 'center', gap: 6 }}><CalendarDays size={14} /> {playlist.created_at}</span>
            <span className={`mm-visibility ${isPublic ? 'pub' : 'priv'}`}>
              {isPublic ? <><Globe size={11} /> PUBLIC</> : <><Lock size={11} /> PRIVATE</>}
            </span>
          </p>
        </div>
      </div>

      <div className="mm-controls mm-enter">
        <button className="mm-play-fab" onClick={() => playAll(false)} title="Play all"><Play size={22} style={{ marginLeft: 3 }} /></button>
        {tracks.length > 0 && (
          <button className="mm-btn-ghost" onClick={() => playAll(true)} title="Shuffle play"><Shuffle size={15} /> Shuffle</button>
        )}
        {isPublic && shareUrl && (
          <button className="mm-btn-ghost" onClick={() => copyLink(shareUrl, p.showToast)} title="Copy share link"><Share2 size={15} /> Share</button>
        )}
        {!isOwner && (
          <form action={`/playlist/${playlist.id}/fork`} method="POST">
            <input type="hidden" name="_token" value={csrf} />
            <button className="mm-btn-ghost" type="submit" title="Save a copy to your library"><CopyPlus size={15} /> Fork to my library</button>
          </form>
        )}
        {isOwner && (
          <>
            <button className="mm-btn-ghost" onClick={onTogglePublic} title="Toggle public/private">
              {isPublic ? <><Lock size={15} /> Make private</> : <><Globe size={15} /> Make public</>}
            </button>
            <a className="mm-icon-btn" style={{ width: 44, height: 44 }} href={`/playlists/${playlist.id}/edit`} title="Edit playlist"><Pencil size={16} /></a>
            <form action={`/playlist/${playlist.id}`} method="POST" onSubmit={(e) => { if (!confirm('Delete this playlist?')) e.preventDefault(); }}>
              <input type="hidden" name="_token" value={csrf} />
              <input type="hidden" name="_method" value="DELETE" />
              <button className="mm-icon-btn" style={{ width: 44, height: 44, color: '#f87171' }} type="submit" title="Delete playlist"><Trash2 size={16} /></button>
            </form>
          </>
        )}
      </div>

      {tracks.length === 0 ? (
        <EmptyState icon={ListMusic} title="This playlist is empty" message="Add songs from Home or Search to fill it up." action={<a className="mm-btn-primary" href="/home">Discover music</a>} />
      ) : (
        <div className="mm-enter mm-fade-2">
          <div className="mm-table-head"><div>#</div><div>Title</div><div className="mm-hide-mobile">Artist</div><div className="mm-hide-mobile">Duration</div><div style={{ textAlign: 'right' }} /></div>
          {tracks.map((s, i) => (
            <TrackRow
              key={`${s.id}-${i}`} song={s} position={i + 1}
              meta={<span style={{ color: 'var(--mm-dim)' }}>{s.artist}</span>}
              onPlay={() => p.loadQueue(tracks, i)}
              trailing={(
                <span style={{ display: 'inline-flex', alignItems: 'center', gap: 2 }} onClick={(e) => e.stopPropagation()}>
                  <LikeButton songId={s.id} />
                  {isOwner && (
                    <form action={`/playlist/${playlist.id}/song/${s.id}`} method="POST">
                      <input type="hidden" name="_token" value={csrf} />
                      <input type="hidden" name="_method" value="DELETE" />
                      <button type="submit" className="mm-icon-btn" style={{ width: 34, height: 34 }} title="Remove from playlist"><Trash2 size={15} /></button>
                    </form>
                  )}
                </span>
              )}
            />
          ))}
        </div>
      )}
    </div>
  );
}

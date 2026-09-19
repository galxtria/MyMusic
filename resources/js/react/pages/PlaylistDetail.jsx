import { useState } from 'react';
import { CalendarDays, CopyPlus, Globe, ListMusic, Lock, Pencil, Play, Share2, Trash2, User } from 'lucide-react';
import { copyLink, normalizeSong, togglePlaylistPublic } from '../lib/api';
import { usePlayer } from '../lib/player';
import { EmptyState } from '../components/ui';

export default function PlaylistDetail({ playlist, user, isOwner = true, ownerName = '' }) {
  const p = usePlayer();
  const tracks = (playlist.songs || []).map(normalizeSong);
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
  const cover = playlist.cover_path ? `/${String(playlist.cover_path).replace(/^\//, '')}` : '/images/default_playlist.jpg';
  const [isPublic, setIsPublic] = useState(!!playlist.is_public);
  const [shareUrl, setShareUrl] = useState(playlist.share_url || (playlist.share_token ? `${window.location.origin}/p/${playlist.share_token}` : ''));

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
      <div className="mm-enter" style={{ display: 'flex', gap: 34, alignItems: 'flex-end', marginBottom: 28, flexWrap: 'wrap' }}>
        <img src={cover} alt={playlist.name} style={{ width: 220, height: 220, borderRadius: 20, objectFit: 'cover', border: '1px solid var(--mm-line)' }} onError={(e) => { e.currentTarget.src = '/images/default_playlist.jpg'; }} />
        <div style={{ flex: 1, minWidth: 260 }}>
          <span className="mm-badge"><ListMusic size={13} /> {isOwner ? 'PERSONAL PLAYLIST' : 'SHARED PLAYLIST'}</span>
          <h1 className="mm-title-gradient" style={{ fontSize: 'clamp(2.4rem, 5.5vw, 4.6rem)', margin: '12px 0' }}>{playlist.name}</h1>
          {playlist.description && <p style={{ color: 'var(--mm-dim)', margin: '0 0 10px', maxWidth: 560 }}>{playlist.description}</p>}
          <p style={{ color: 'var(--mm-dim)', fontWeight: 600, display: 'flex', alignItems: 'center', gap: 10, flexWrap: 'wrap', margin: 0 }}>
            <span style={{ display: 'inline-flex', alignItems: 'center', gap: 6, color: '#fff' }}><User size={14} /> {isOwner ? user?.name : ownerName}</span>
            • {tracks.length} tracks
            <span style={{ display: 'inline-flex', alignItems: 'center', gap: 6 }}><CalendarDays size={14} /> {playlist.created_at}</span>
            <span className="mm-chip mm-chip-sm" style={{ display: 'inline-flex', alignItems: 'center', gap: 5 }}>
              {isPublic ? <><Globe size={12} /> Public</> : <><Lock size={12} /> Private</>}
            </span>
          </p>
        </div>
      </div>

      <div className="mm-enter mm-fade-1" style={{ display: 'flex', gap: 12, marginBottom: 26, flexWrap: 'wrap', alignItems: 'center' }}>
        <button className="mm-play-fab" onClick={() => tracks.length && p.loadQueue(tracks, 0)} title="Play all"><Play size={22} style={{ marginLeft: 3 }} /></button>
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
            <a className="mm-icon-btn" style={{ width: 48, height: 48 }} href={`/playlists/${playlist.id}/edit`} title="Edit playlist"><Pencil size={17} /></a>
            <form action={`/playlist/${playlist.id}`} method="POST" onSubmit={(e) => { if (!confirm('Delete this playlist?')) e.preventDefault(); }}>
              <input type="hidden" name="_token" value={csrf} />
              <input type="hidden" name="_method" value="DELETE" />
              <button className="mm-icon-btn" style={{ width: 48, height: 48, color: '#f87171' }} type="submit" title="Delete playlist"><Trash2 size={17} /></button>
            </form>
          </>
        )}
      </div>

      {tracks.length === 0 ? (
        <EmptyState icon={ListMusic} title="This playlist is empty" message="Add songs from Home or Search to fill it up." action={<a className="mm-btn-primary" href="/home">Discover music</a>} />
      ) : (
        <div className="mm-enter mm-fade-2">
          <div className="mm-table-head"><div>#</div><div>Title</div><div className="mm-hide-mobile">Artist</div><div className="mm-hide-mobile">Duration</div><div style={{ textAlign: 'right' }}>{isOwner ? 'Remove' : ''}</div></div>
          {tracks.map((s, i) => (
            <div key={s.id} className="mm-table-row" onClick={() => p.loadQueue(tracks, i)}>
              <div style={{ color: 'var(--mm-faint)', fontWeight: 800, fontFamily: 'monospace' }}>{i + 1}</div>
              <div style={{ display: 'flex', alignItems: 'center', gap: 14, minWidth: 0 }}>
                <img src={s.cover} alt="" style={{ width: 46, height: 46, borderRadius: 10, objectFit: 'cover', flexShrink: 0 }} onError={(e) => { e.currentTarget.src = '/images/default-cover.png'; }} />
                <div style={{ fontWeight: 800, whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>{s.title}</div>
              </div>
              <div className="mm-hide-mobile" style={{ color: 'var(--mm-dim)' }}>{s.artist}</div>
              <div className="mm-hide-mobile" style={{ color: 'var(--mm-dim)', fontFamily: 'monospace' }}>{s.duration || '0:30'}</div>
              <div style={{ textAlign: 'right' }} onClick={(e) => e.stopPropagation()}>
                {isOwner && (
                  <form action={`/playlist/${playlist.id}/song/${s.id}`} method="POST">
                    <input type="hidden" name="_token" value={csrf} />
                    <input type="hidden" name="_method" value="DELETE" />
                    <button type="submit" className="mm-icon-btn" style={{ width: 34, height: 34 }} title="Remove from playlist"><Trash2 size={15} /></button>
                  </form>
                )}
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

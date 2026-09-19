import { useMemo, useState } from 'react';
import { CopyPlus, EllipsisVertical, Globe, Heart, ListMusic, Lock, Pencil, Play, Plus, Search as SearchIcon, Share2, Trash2 } from 'lucide-react';
import { copyLink } from '../lib/api';
import { usePlayer } from '../lib/player';
import { EmptyState, PageHeader } from '../components/ui';

function coverOf(pl) {
  if (!pl?.cover_path) return '/images/default_playlist.jpg';
  return `/${String(pl.cover_path).replace(/^\//, '')}`;
}

function PlaylistCard({ pl, menuOpen, onMenu, onShare, csrf }) {
  return (
    <div className="mm-song-card mm-lib-card mm-enter">
      <div style={{ position: 'absolute', top: 22, right: 22, zIndex: 5 }}>
        <button className="mm-icon-btn" style={{ width: 34, height: 34, background: 'rgba(0,0,0,0.55)' }} onClick={onMenu} title="Options">
          <EllipsisVertical size={15} />
        </button>
        {menuOpen && (
          <div className="mm-glass" style={{ position: 'absolute', right: 0, top: 42, minWidth: 200, padding: 8, zIndex: 20 }}>
            <a href={`/playlists/${pl.id}/edit`} className="mm-nav-link"><Pencil size={15} /> Edit playlist</a>
            {pl.share_url && (
              <button className="mm-nav-link" style={{ width: '100%', background: 'none', border: 0, cursor: 'pointer' }} onClick={onShare}>
                <Share2 size={15} /> Copy share link
              </button>
            )}
            <form action={`/playlist/${pl.id}`} method="POST" onSubmit={(e) => { if (!confirm('Delete this playlist?')) e.preventDefault(); }}>
              <input type="hidden" name="_token" value={csrf} />
              <input type="hidden" name="_method" value="DELETE" />
              <button type="submit" className="mm-nav-link" style={{ width: '100%', background: 'none', border: 0, cursor: 'pointer', color: '#f87171' }}>
                <Trash2 size={15} /> Delete
              </button>
            </form>
          </div>
        )}
      </div>
      <a href={`/playlist/${pl.id}`} style={{ textDecoration: 'none', display: 'flex', flexDirection: 'column', height: '100%' }}>
        <div className="mm-cover-wrap">
          <img src={coverOf(pl)} alt={pl.name} loading="lazy" onError={(e) => { e.currentTarget.src = '/images/default_playlist.jpg'; }} />
          <div className="mm-card-play"><span><Play size={20} style={{ marginLeft: 2 }} /></span></div>
        </div>
        <h4 style={{ margin: '0 0 2px', display: 'flex', alignItems: 'center', gap: 8, fontSize: '0.98rem' }}>
          <ListMusic size={15} style={{ color: 'var(--mm-teal)', flexShrink: 0 }} />
          <span style={{ whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>{pl.name}</span>
        </h4>
        <p style={{ margin: 0, color: 'var(--mm-dim)', fontSize: '0.8rem', fontWeight: 600 }}>{pl.songs_count ?? 0} tracks</p>
        <div className="mm-lib-badges">
          {pl.is_public
            ? <span className="mm-visibility pub"><Globe size={11} /> PUBLIC</span>
            : <span className="mm-visibility priv"><Lock size={11} /> PRIVATE</span>}
        </div>
      </a>
    </div>
  );
}

export default function Library({ playlists = [], favoritesCount = 0, publicPlaylists = [] }) {
  const p = usePlayer();
  const [openMenu, setOpenMenu] = useState(null);
  const [filter, setFilter] = useState('');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

  const mine = useMemo(() => {
    const q = filter.trim().toLowerCase();
    if (!q) return playlists;
    return playlists.filter((pl) => (pl.name || '').toLowerCase().includes(q));
  }, [playlists, filter]);

  return (
    <div>
      <PageHeader
        badge="COLLECTION" title="Your library"
        subtitle={`${playlists.length} playlists • ${favoritesCount} liked tracks`}
        action={(
          <div className="mm-filter">
            <SearchIcon size={15} style={{ color: 'var(--mm-faint)', flexShrink: 0 }} />
            <input value={filter} onChange={(e) => setFilter(e.target.value)} placeholder="Filter playlists..." />
          </div>
        )}
      />

      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(200px, 1fr))', gap: 16 }}>
        <a href="/favorites" className="mm-song-card mm-enter" style={{ textDecoration: 'none', minHeight: 250, display: 'flex', flexDirection: 'column', justifyContent: 'flex-end', padding: 24, position: 'relative', overflow: 'hidden', background: '#16202e', borderRadius: 16 }}>
          <Heart size={110} style={{ position: 'absolute', right: -18, top: -18, opacity: 0.22, transform: 'rotate(18deg)' }} color="#3b82f6" fill="#3b82f6" />
          <span className="mm-visibility pub" style={{ alignSelf: 'flex-start', marginBottom: 'auto' }}><Heart size={11} /> AUTO PLAYLIST</span>
          <h2 style={{ margin: '0 0 6px', fontSize: '1.5rem' }}>Liked Songs</h2>
          <p style={{ margin: 0, color: 'var(--mm-dim)', fontWeight: 600, fontSize: '0.86rem' }}>{favoritesCount} tracks</p>
        </a>

        <a href="/create" className="mm-song-card mm-enter" style={{ textDecoration: 'none', minHeight: 250, display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', borderStyle: 'dashed', gap: 10, color: 'var(--mm-dim)' }}>
          <span className="mm-card-play" style={{ opacity: 1, position: 'static' }}><span><Plus size={22} /></span></span>
          <span style={{ fontWeight: 800 }}>New Playlist</span>
          <span style={{ fontSize: '0.75rem' }}>Name, cover & description</span>
        </a>

        {mine.map((pl) => (
          <PlaylistCard
            key={pl.id} pl={pl} csrf={csrf}
            menuOpen={openMenu === pl.id}
            onMenu={() => setOpenMenu(openMenu === pl.id ? null : pl.id)}
            onShare={() => { copyLink(pl.share_url, p.showToast); setOpenMenu(null); }}
          />
        ))}
      </div>

      {mine.length === 0 && filter && (
        <EmptyState icon={SearchIcon} title="No playlists match" message={`Nothing named "${filter}" in your library.`} />
      )}

      {publicPlaylists.length > 0 && (
        <>
          <div className="mm-section" style={{ marginTop: 30 }}>
            <Globe size={19} style={{ color: 'var(--mm-teal)' }} />
            <span>Discover public playlists</span>
            <span style={{ color: 'var(--mm-faint)', fontSize: '0.78rem', fontWeight: 700 }}>FROM OTHER LISTENERS</span>
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(200px, 1fr))', gap: 16 }}>
            {publicPlaylists.map((pl) => (
              <div key={`pub-${pl.id}`} className="mm-song-card mm-enter">
                <a href={pl.share_token ? `/p/${pl.share_token}` : `/playlist/${pl.id}`} style={{ textDecoration: 'none', display: 'flex', flexDirection: 'column', height: '100%' }}>
                  <div className="mm-cover-wrap">
                    <img src={coverOf(pl)} alt={pl.name} loading="lazy" onError={(e) => { e.currentTarget.src = '/images/default_playlist.jpg'; }} />
                    <div className="mm-card-play"><span><Play size={20} style={{ marginLeft: 2 }} /></span></div>
                  </div>
                  <h4 style={{ margin: '0 0 4px', fontSize: '0.95rem', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>{pl.name}</h4>
                  <p style={{ margin: 0, color: 'var(--mm-dim)', fontSize: '0.8rem', fontWeight: 600 }}>by {pl.owner} • {pl.songs_count} tracks</p>
                </a>
                <form action={`/playlist/${pl.id}/fork`} method="POST" style={{ marginTop: 10 }}>
                  <input type="hidden" name="_token" value={csrf} />
                  <button className="mm-btn-ghost" style={{ width: '100%', justifyContent: 'center', fontSize: '0.78rem' }} type="submit"><CopyPlus size={14} /> Fork</button>
                </form>
              </div>
            ))}
          </div>
        </>
      )}
    </div>
  );
}

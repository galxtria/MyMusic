import { EllipsisVertical, Heart, ListMusic, Pencil, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { PageHeader } from '../components/ui';

export default function Library({ playlists = [], favoritesCount = 0 }) {
  const [openMenu, setOpenMenu] = useState(null);
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

  return (
    <div>
      <PageHeader badge="COLLECTION" title="Your library" subtitle={`${playlists.length} playlists • ${favoritesCount} liked tracks`} />
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(200px, 1fr))', gap: 16 }}>
        <a href="/favorites" className="mm-glass" style={{ textDecoration: 'none', minHeight: 250, display: 'flex', flexDirection: 'column', justifyContent: 'flex-end', padding: 24, position: 'relative', overflow: 'hidden' }}>
          <Heart size={60} style={{ position: 'absolute', right: 14, top: 14, opacity: 0.16 }} color="#3b82f6" fill="#3b82f6" />
          <h2 style={{ margin: '0 0 6px', fontSize: '1.5rem' }}>Liked Songs</h2>
          <p style={{ margin: 0, color: 'var(--mm-dim)', fontWeight: 600, fontSize: '0.86rem' }}>{favoritesCount} tracks</p>
        </a>

        <a href="/create" className="mm-song-card" style={{ textDecoration: 'none', minHeight: 250, display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', borderStyle: 'dashed', gap: 10, color: 'var(--mm-dim)' }}>
          <Plus size={36} />
          <span style={{ fontWeight: 800 }}>New Playlist</span>
        </a>

        {playlists.map((pl) => (
          <div key={pl.id} className="mm-song-card" style={{ height: '100%' }}>
            <div style={{ position: 'absolute', top: 22, right: 22, zIndex: 5 }}>
              <button className="mm-icon-btn" style={{ width: 34, height: 34, background: 'rgba(0,0,0,0.55)' }} onClick={() => setOpenMenu(openMenu === pl.id ? null : pl.id)}>
                <EllipsisVertical size={15} />
              </button>
              {openMenu === pl.id && (
                <div className="mm-glass" style={{ position: 'absolute', right: 0, top: 42, minWidth: 180, padding: 8, zIndex: 20 }}>
                  <a href={`/playlists/${pl.id}/edit`} className="mm-nav-link"><Pencil size={15} /> Edit playlist</a>
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
                <img src={pl.cover_path ? `/${String(pl.cover_path).replace(/^\//, '')}` : '/images/default_playlist.jpg'} alt={pl.name} onError={(e) => { e.currentTarget.src = '/images/default_playlist.jpg'; }} />
              </div>
              <h4 style={{ margin: '0 0 4px', display: 'flex', alignItems: 'center', gap: 8 }}><ListMusic size={15} style={{ color: 'var(--mm-sky)' }} /> {pl.name}</h4>
              <p style={{ margin: 0, color: 'var(--mm-dim)', fontSize: '0.82rem', fontWeight: 600 }}>{pl.songs_count ?? pl.songs?.length ?? 0} tracks</p>
            </a>
          </div>
        ))}
      </div>
    </div>
  );
}

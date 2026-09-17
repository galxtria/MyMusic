import { useState } from 'react';
import { Compass, LibraryBig, Search as SearchIcon, Users } from 'lucide-react';
import { normalizeSong } from '../lib/api';
import { usePlayer } from '../lib/player';
import { EmptyState, PageHeader, SectionHeader, SongCard } from '../components/ui';

const GENRES = ['Pop', 'Rock', 'Jazz', 'Indie', 'K-Pop', 'Lo-Fi', 'Electronic', 'Acoustic'];

export default function Search({ initialQuery = '', localSongs = [], localArtists = [] }) {
  const p = usePlayer();
  const [query, setQuery] = useState(initialQuery);
  const locals = localSongs.map(normalizeSong);
  const searched = (initialQuery || '').trim() !== '';

  const submit = (e) => {
    e.preventDefault();
    if (!query.trim()) return;
    window.location.href = `/search?q=${encodeURIComponent(query.trim())}`;
  };

  const searchGenre = (g) => {
    window.location.href = `/search?q=${encodeURIComponent(g)}`;
  };

  return (
    <div>
      <PageHeader badge="EXPLORE" title="Search your library" subtitle="Semua hasil di bawah ini adalah lagu lokal yang tersimpan di server." />
      <form onSubmit={submit} className="mm-search-bar mm-enter">
        <SearchIcon size={19} style={{ color: 'var(--mm-faint)', flexShrink: 0 }} />
        <input value={query} onChange={(e) => setQuery(e.target.value)} placeholder="Search songs, artists, albums..." autoFocus />
        <button type="submit" className="mm-search-btn"><SearchIcon size={19} /></button>
      </form>

      {!searched && (
        <>
          <SectionHeader icon={Compass} title="Browse genres" />
          <div className="mm-mood-grid mm-enter">
            {GENRES.map((g) => (
              <button key={g} className="mm-mood-card" onClick={() => searchGenre(g)}>
                <h3>{g}</h3>
                <Compass size={30} style={{ color: 'var(--mm-faint)' }} />
              </button>
            ))}
          </div>
        </>
      )}

      {searched && (
        <div style={{ marginTop: 8 }}>
          {localArtists.length > 0 && (
            <>
              <SectionHeader icon={Users} title="Artists in library" />
              <div className="mm-grid-songs">
                {localArtists.map((a) => (
                  <a key={a.artist} href={`/artist/${encodeURIComponent(a.artist)}`} className="mm-song-card" style={{ textDecoration: 'none', textAlign: 'center' }}>
                    <div style={{ width: 104, height: 104, borderRadius: '50%', overflow: 'hidden', margin: '6px auto 12px', border: '1px solid var(--mm-line)' }}>
                      <img src={a.artwork_url || a.album_art || '/images/default_artist.jpg'} alt={a.artist} style={{ width: '100%', height: '100%', objectFit: 'cover' }} onError={(e) => { e.currentTarget.src = '/images/default_artist.jpg'; }} />
                    </div>
                    <div style={{ fontWeight: 800 }}>{a.artist}</div>
                    <div style={{ fontSize: '0.72rem', color: 'var(--mm-teal)', letterSpacing: 1.5, fontWeight: 800 }}>ARTIST</div>
                  </a>
                ))}
              </div>
            </>
          )}
          <SectionHeader icon={LibraryBig} title={`Songs in library${locals.length > 0 ? ` (${locals.length})` : ''}`} />
          {locals.length === 0
            ? <EmptyState title="No local results" message="Belum ada lagu yang cocok. Minta admin mengimpor via halaman Import Music." />
            : <div className="mm-grid-songs">{locals.map((s, i) => <SongCard key={s.id} song={s} onPlay={() => p.loadQueue(locals, i)} />)}</div>}
        </div>
      )}
    </div>
  );
}

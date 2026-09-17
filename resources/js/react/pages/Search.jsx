import { useEffect, useState } from 'react';
import { Compass, Database, Globe, LibraryBig, Plus, Search as SearchIcon, Users } from 'lucide-react';
import { normalizeSong, searchOnline, addYouTubeToLibrary } from '../lib/api';
import { usePlayer } from '../lib/player';
import { EmptyState, LoadingRow, PageHeader, SectionHeader, SongCard, ErrorRow } from '../components/ui';

const GENRES = ['Pop', 'Rock', 'Jazz', 'Indie', 'K-Pop', 'Lo-Fi', 'Electronic', 'Acoustic'];

export default function Search({ initialQuery = '', localSongs = [], localArtists = [] }) {
  const p = usePlayer();
  const [query, setQuery] = useState(initialQuery);
  const [tab, setTab] = useState('online');
  const [online, setOnline] = useState([]);
  const [state, setState] = useState(initialQuery ? 'loading' : 'idle');
  const locals = localSongs.map(normalizeSong);

  const runOnline = async (q) => {
    const text = (q ?? query).trim();
    if (!text) return;
    setState('loading');
    try {
      const data = await searchOnline(text);
      setOnline((data.results || []).map((t) => normalizeSong({
        id: `yt-${(t.url || '').split('?v=')[1] || t.url}`,
        title: t.title, artist: t.uploaderName, cover: t.thumbnail,
        src: `/api/stream-audio?title=${encodeURIComponent(t.title || '')}&artist=${encodeURIComponent(t.uploaderName || '')}`,
        raw: t,
      })));
      setState('done');
    } catch {
      setState('error');
    }
  };

  useEffect(() => {
    if (initialQuery) runOnline(initialQuery);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const submit = (e) => {
    e.preventDefault();
    if (!query.trim()) return;
    window.history.replaceState(null, '', `/search?q=${encodeURIComponent(query.trim())}`);
    if (tab === 'local') window.location.href = `/search?q=${encodeURIComponent(query.trim())}`;
    else runOnline(query);
  };

  return (
    <div>
      <PageHeader badge="EXPLORE" title="Search library and beyond" subtitle="Local collection plus live online results in one place." />
      <form onSubmit={submit} className="mm-search-bar mm-enter">
        <SearchIcon size={19} style={{ color: 'var(--mm-faint)', flexShrink: 0 }} />
        <input value={query} onChange={(e) => setQuery(e.target.value)} placeholder="Search songs, artists, albums..." autoFocus />
        <button type="submit" className="mm-search-btn"><SearchIcon size={19} /></button>
      </form>

      <div style={{ display: 'flex', gap: 10, justifyContent: 'center', margin: '24px 0 8px' }}>
        <button className={`mm-chip ${tab === 'online' ? 'active' : ''}`} onClick={() => setTab('online')}><Globe size={15} /> Online search</button>
        <button className={`mm-chip ${tab === 'local' ? 'active' : ''}`} onClick={() => setTab('local')}><Database size={15} /> My library</button>
      </div>

      {tab === 'local' && (
        <div>
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
                    <div style={{ fontSize: '0.72rem', color: 'var(--mm-sky)', letterSpacing: 1.5, fontWeight: 800 }}>ARTIST</div>
                  </a>
                ))}
              </div>
            </>
          )}
          <SectionHeader icon={LibraryBig} title="Songs in library" />
          {locals.length === 0
            ? <EmptyState title="No local results" message="Switch to the online tab to find it instantly." />
            : <div className="mm-grid-songs">{locals.map((s, i) => <SongCard key={s.id} song={s} onPlay={() => p.loadQueue(locals, i)} />)}</div>}
        </div>
      )}

      {tab === 'online' && (
        <div>
          {state === 'idle' && (
            <>
              <SectionHeader icon={Compass} title="Browse genres" />
              <div className="mm-mood-grid">
                {GENRES.map((g) => (
                  <button key={g} className="mm-mood-card" onClick={() => { setQuery(g); runOnline(g); }}>
                    <h3>{g}</h3>
                    <Compass size={30} style={{ color: 'var(--mm-faint)' }} />
                  </button>
                ))}
              </div>
            </>
          )}
          {state === 'loading' && <LoadingRow text="Searching online..." />}
          {state === 'error' && <ErrorRow />}
          {state === 'done' && online.length === 0 && <EmptyState title="No results found online" message="Try different keywords or check spelling." />}
          {state === 'done' && online.length > 0 && (
            <>
              <SectionHeader icon={Globe} title={`Online results (${online.length})`} />
              <div className="mm-grid-songs">
                {online.map((t, i) => (
                  <SongCard
                    key={t.id + i} song={t} onPlay={() => p.loadQueue(online, i)}
                    action={(
                      <button
                        className="mm-icon-btn" style={{ width: 30, height: 30 }} title="Add to library"
                        onClick={async (e) => {
                          e.stopPropagation();
                          try {
                            await addYouTubeToLibrary({ url: t.raw?.url ?? t.id, title: t.title, uploaderName: t.artist, thumbnail: t.cover, duration: 0 });
                            p.showToast('Added to your library');
                          } catch { p.showToast('Could not add to library'); }
                        }}
                      >
                        <Plus size={14} />
                      </button>
                    )}
                  />
                ))}
              </div>
            </>
          )}
        </div>
      )}
    </div>
  );
}

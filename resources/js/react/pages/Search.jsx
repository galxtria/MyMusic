import { useState } from 'react';
import {
  Coffee, Compass, Disc3, Flame, Headphones, Heart, LibraryBig, Mic, Moon,
  Music2, Search as SearchIcon, Sparkles, Star, Users, X, Zap,
} from 'lucide-react';
import { normalizeSong } from '../lib/api';
import { usePlayer } from '../lib/player';
import { EmptyState, PageHeader, SectionHeader, TrackRow } from '../components/ui';
import { LikeButton } from '../components/AppShell';

const GENRES = [
  { name: 'Pop', color: '#8d67ab', icon: Star, tag: 'Charts & hits' },
  { name: 'Rock', color: '#c2410c', icon: Zap, tag: 'Loud guitars' },
  { name: 'Jazz', color: '#0e7490', icon: Moon, tag: 'Late night' },
  { name: 'Indie', color: '#4d7c0f', icon: Music2, tag: 'Fresh finds' },
  { name: 'K-Pop', color: '#db2777', icon: Heart, tag: 'Seoul vibes' },
  { name: 'Lo-Fi', color: '#57534e', icon: Coffee, tag: 'Study beats' },
  { name: 'Electronic', color: '#6d28d9', icon: Headphones, tag: 'Synths & bass' },
  { name: 'Acoustic', color: '#b45309', icon: Mic, tag: 'Stripped down' },
];

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
      <PageHeader badge="EXPLORE" title="What do you want to listen to?" subtitle="Search the server library, or pick a lane below." />
      <form onSubmit={submit} className="mm-search-hero mm-enter">
        <SearchIcon size={20} style={{ color: 'var(--mm-faint)', flexShrink: 0 }} />
        <input value={query} onChange={(e) => setQuery(e.target.value)} placeholder="Songs, artists, genres..." autoFocus={!searched} />
        {query && (
          <button type="button" className="mm-search-clear" title="Clear" onClick={() => { setQuery(''); window.location.href = '/search'; }}>
            <X size={17} />
          </button>
        )}
        <button type="submit" className="mm-search-go" title="Search"><SearchIcon size={19} /></button>
      </form>

      {!searched && (
        <>
          <SectionHeader icon={Compass} title="Browse categories" />
          <div className="mm-genre-grid mm-enter">
            {GENRES.map((g) => (
              <button key={g.name} className="mm-genre-tile" style={{ background: g.color }} onClick={() => searchGenre(g.name)}>
                <h3>{g.name}</h3>
                <small>{g.tag}</small>
                <g.icon size={76} color="#fff" />
              </button>
            ))}
          </div>

          {locals.length > 0 && (
            <>
              <SectionHeader icon={Flame} title="From your library" linkText="OPEN LIBRARY" linkHref="/library" />
              <div className="mm-glass mm-enter" style={{ padding: 8 }}>
                {locals.slice(0, 5).map((s, i) => (
                  <TrackRow
                    key={s.id} song={s} position={i + 1} onPlay={() => p.loadQueue(locals, i)}
                    trailing={<LikeButton songId={s.id} />}
                  />
                ))}
              </div>
            </>
          )}
        </>
      )}

      {searched && (
        <div style={{ marginTop: 8 }}>
          <div className="mm-section mm-enter">
            <SearchIcon size={18} style={{ color: 'var(--mm-accent)' }} />
            <span>Results for &ldquo;{initialQuery}&rdquo;</span>
            <span style={{ color: 'var(--mm-faint)', fontSize: '0.8rem', fontWeight: 700 }}>
              {localArtists.length} artists • {locals.length} songs
            </span>
          </div>
          {localArtists.length > 0 && (
            <>
              <SectionHeader icon={Users} title="Artists" />
              <div className="mm-grid-songs">
                {localArtists.map((a) => (
                  <a
                    key={a.artist} href={`/artist/${encodeURIComponent(a.artist)}`} className="mm-artist-card mm-enter"
                    onClick={(e) => {
                      // Putar langsung tanpa pindah halaman bila klik tombol play.
                      if (e.target.closest('.mm-card-play')) {
                        e.preventDefault();
                        window.location.href = `/artist/${encodeURIComponent(a.artist)}`;
                      }
                    }}
                  >
                    <div className="mm-artist-face">
                      <img
                        src={a.artist_image || a.artwork_url || a.album_art || '/images/default_artist.jpg'}
                        alt={a.artist} loading="lazy"
                        onError={(e) => { e.currentTarget.src = '/images/default_artist.jpg'; }}
                      />
                    </div>
                    <div style={{ fontWeight: 800, whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>{a.artist}</div>
                    <div style={{ fontSize: '0.68rem', color: 'var(--mm-teal)', letterSpacing: 2, fontWeight: 800, marginTop: 4 }}>ARTIST</div>
                  </a>
                ))}
              </div>
            </>
          )}
          <SectionHeader icon={LibraryBig} title={`Songs${locals.length > 0 ? ` (${locals.length})` : ''}`} />
          {locals.length === 0
            ? <EmptyState title="No matches in the library" message="Try another keyword, or ask an admin to import it via Import Music." action={<a className="mm-btn-primary" href="/search">Browse categories</a>} />
            : (
              <div className="mm-glass mm-enter" style={{ padding: 8 }}>
                {locals.map((s, i) => (
                  <TrackRow
                    key={s.id} song={s} position={i + 1} onPlay={() => p.loadQueue(locals, i)}
                    trailing={<LikeButton songId={s.id} />}
                  />
                ))}
              </div>
            )}
        </div>
      )}

      <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginTop: 26, color: 'var(--mm-faint)', fontSize: '0.8rem' }}>
        <Disc3 size={15} /> <Sparkles size={13} /> Online results (YouTube) live on the Home page trending rows.
      </div>
    </div>
  );
}

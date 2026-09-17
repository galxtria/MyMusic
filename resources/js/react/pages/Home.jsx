import { useMemo, useRef, useState } from 'react';
import { ChevronLeft, ChevronRight, LoaderCircle, Play, Plus } from 'lucide-react';
import { addYouTubeToLibrary, moodOnline, normalizeSong } from '../lib/api';
import { usePlayer } from '../lib/player';
import { EmptyState, LoadingRow, SongCard, ErrorRow } from '../components/ui';

function onlineToSong(t) {
  const idPart = (t.url || '').split('?v=')[1] || t.url || t.id;
  return normalizeSong({
    id: `yt-${idPart}`,
    title: t.title,
    artist: t.uploaderName,
    cover: t.thumbnail,
    src: `/api/stream-audio?title=${encodeURIComponent(t.title || '')}&artist=${encodeURIComponent(t.uploaderName || '')}`,
    lyrics: '',
    duration: '',
  });
}

const CATEGORIES = [
  { label: 'All', mood: null },
  { label: 'Relax', mood: 'santai' },
  { label: 'Sad', mood: 'sedih' },
  { label: 'Party', mood: 'energetik' },
  { label: 'Focus', mood: 'fokus' },
  { label: 'Energetic', mood: 'energetik' },
  { label: 'Jazz', q: 'Jazz' },
  { label: 'Pop', q: 'Pop' },
  { label: 'Rock', q: 'Rock' },
  { label: 'Lo-Fi', q: 'Lo-Fi' },
];

function RowChevrons({ targetRef }) {
  const scroll = (dir) => targetRef.current?.scrollBy({ left: dir * 480, behavior: 'smooth' });
  return (
    <span className="rt-chevs">
      <button className="rt-chev" onClick={() => scroll(-1)} title="Scroll left"><ChevronLeft size={15} /></button>
      <button className="rt-chev" onClick={() => scroll(1)} title="Scroll right"><ChevronRight size={15} /></button>
    </span>
  );
}

function pageWindow(current, last) {
  if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1);
  const set = new Set([1, last, current - 1, current, current + 1]);
  const nums = [...set].filter((n) => n >= 1 && n <= last).sort((a, b) => a - b);
  const out = [];
  nums.forEach((n, i) => {
    if (i > 0 && n - nums[i - 1] > 1) out.push('…');
    out.push(n);
  });
  return out;
}

function CollectionPagination({ pag, loading, onGo }) {
  if (!pag || pag.lastPage <= 1) return null;
  return (
    <div className="mm-pag">
      <button className="mm-pag-num" disabled={pag.current <= 1 || loading} onClick={() => onGo(pag.current - 1)} title="Previous page">
        <ChevronLeft size={16} />
      </button>
      {pageWindow(pag.current, pag.lastPage).map((n, i) => n === '…'
        ? <span key={`gap-${i}`} className="mm-pag-gap">…</span>
        : (
          <button
            key={n}
            className={`mm-pag-num ${n === pag.current ? 'active' : ''}`}
            disabled={loading}
            onClick={() => onGo(n)}
          >
            {n}
          </button>
        ))}
      <button className="mm-pag-num" disabled={pag.current >= pag.lastPage || loading} onClick={() => onGo(pag.current + 1)} title="Next page">
        {loading ? <LoaderCircle size={16} className="mm-spin" /> : <ChevronRight size={16} />}
      </button>
      <span className="mm-pag-info">Page {pag.current} of {pag.lastPage}</span>
    </div>
  );
}

export default function Home({ songs = [], trending = [], artists = [], pagination }) {
  const p = usePlayer();
  const [mood, setMood] = useState(null);
  const [moodTracks, setMoodTracks] = useState([]);
  const [moodState, setMoodState] = useState('idle');
  const heroRef = useRef(null);
  const popularRef = useRef(null);
  const catRef = useRef(null);

  const localSongs = useMemo(() => songs.map(normalizeSong), [songs]);
  const trendingSongs = useMemo(() => trending.map(normalizeSong), [trending]);
  const heroSongs = useMemo(() => [...localSongs].slice(0, 8), [localSongs]);
  const popular = trendingSongs.length > 0 ? trendingSongs : localSongs;

  // Collection grid has its own AJAX pagination — navigating pages only
  // re-renders this section, hero / categories / popular stay untouched.
  const [collection, setCollection] = useState(() => songs.map(normalizeSong));
  const [pag, setPag] = useState(pagination);
  const [colLoading, setColLoading] = useState(false);
  const collectionRef = useRef(null);

  const loadCollectionPage = async (pageNum) => {
    if (!pageNum || colLoading || (pag && pageNum === pag.current)) return;
    setColLoading(true);
    try {
      const res = await fetch(`/api/collection?page=${pageNum}`, { headers: { Accept: 'application/json' } });
      if (!res.ok) throw new Error('bad response');
      const data = await res.json();
      setCollection((data.data || []).map(normalizeSong));
      setPag({ current: data.current_page, lastPage: data.last_page });
      collectionRef.current?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } catch {
      p.showToast('Could not load that page');
    } finally {
      setColLoading(false);
    }
  };

  const playList = (list, i) => p.loadQueue(list, i);

  const pickMood = async (key) => {
    if (!key) {
      setMood(null); setMoodTracks([]); setMoodState('idle');
      return;
    }
    if (mood === key) {
      setMood(null); setMoodTracks([]); setMoodState('idle');
      return;
    }
    setMood(key); setMoodState('loading');
    try {
      const data = await moodOnline(key);
      setMoodTracks((data.results || []).map(onlineToSong));
      setMoodState('done');
    } catch {
      setMoodState('error');
    }
  };

  return (
    <div>
      {/* ===== hero carousel ===== */}
      {heroSongs.length > 0 && (
        <div className="rt-hero-row mm-enter" ref={heroRef}>
          {heroSongs.map((s, i) => (
            <div
              key={s.id}
              className={`rt-hero-card ${i === 0 ? 'rt-hero-lead' : ''}`}
              onClick={() => playList(heroSongs, i)}
            >
              <img src={s.cover} alt={s.title} loading={i > 2 ? 'lazy' : undefined} onError={(e) => { e.currentTarget.src = '/images/default-cover.png'; }} />
              {i === 0 && (
                <div className="rt-hero-info">
                  <div style={{ minWidth: 0 }}>
                    <div className="rt-hero-title">{s.title}</div>
                    <div className="rt-hero-artist">{s.artist}</div>
                  </div>
                  <button
                    className="rt-play-dim"
                    style={{ flexShrink: 0 }}
                    onClick={(e) => { e.stopPropagation(); playList(heroSongs, 0); }}
                    title="Play"
                  >
                    <Play size={20} style={{ marginLeft: 2 }} />
                  </button>
                </div>
              )}
            </div>
          ))}
        </div>
      )}

      {/* ===== categories ===== */}
      <div className="mm-section mm-enter">
        <span>Select Categories</span>
        <RowChevrons targetRef={catRef} />
      </div>
      <div className="rt-cat-row mm-enter" ref={catRef}>
        {CATEGORIES.map((c) => {
          const isActive = (c.mood == null && mood == null) || (c.mood != null && mood === c.mood);
          if (c.q) {
            return <a key={c.label} href={`/search?q=${encodeURIComponent(c.q)}`} className="mm-chip">{c.label}</a>;
          }
          return (
            <button key={c.label} className={`mm-chip ${isActive ? 'active' : ''}`} onClick={() => pickMood(c.mood)}>
              {c.label}
            </button>
          );
        })}
      </div>

      {/* ===== mood results ===== */}
      {moodState === 'loading' && <LoadingRow text="Finding the perfect tracks..." />}
      {moodState === 'error' && <ErrorRow />}
      {moodState === 'done' && (
        moodTracks.length === 0
          ? <EmptyState title="No tracks for this mood" message="Try another vibe in a moment." />
          : (
            <>
              <div className="mm-section"><span>For your mood</span></div>
              <div className="mm-grid-songs">
                {moodTracks.map((t, i) => (
                  <SongCard
                    key={t.id + i} song={t} onPlay={() => playList(moodTracks, i)}
                    action={(
                      <button
                        className="mm-icon-btn" style={{ width: 30, height: 30 }}
                        title="Add to library"
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
          )
      )}

      {/* ===== popular songs ===== */}
      {popular.length > 0 && (
        <>
          <div className="mm-section mm-enter">
            <span>Popular songs</span>
            <RowChevrons targetRef={popularRef} />
          </div>
          <div className="rt-row mm-enter" ref={popularRef}>
            {popular.map((s, i) => (
              <SongCard
                key={`${s.id}-${i}`} song={s} onPlay={() => playList(popular, i)}
                action={<button className="mm-icon-btn" style={{ width: 30, height: 30 }} onClick={(e) => { e.stopPropagation(); p.setPlaylistModalSong(s.id); }} title="Save to playlist"><Plus size={14} /></button>}
              />
            ))}
          </div>
        </>
      )}

      {/* ===== artists ===== */}
      {artists.length > 0 && (
        <>
          <div className="mm-section"><span>Top artists</span></div>
          <div className="rt-row">
            {artists.map((a) => (
              <a key={a.artist} href={`/artist/${encodeURIComponent(a.artist)}`} style={{ textDecoration: 'none', textAlign: 'center', flex: '0 0 auto' }}>
                <img
                  src={a.cover || a.artwork_url || a.album_art || '/images/default_artist.jpg'}
                  alt={a.artist}
                  style={{ width: 92, height: 92, borderRadius: '50%', objectFit: 'cover', display: 'block', margin: '0 auto 10px', flexShrink: 0 }}
                  onError={(e) => { e.currentTarget.src = '/images/default_artist.jpg'; }}
                />
                <div style={{ fontSize: '0.8rem', fontWeight: 700, maxWidth: 100, whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>
                  {a.artist}
                </div>
              </a>
            ))}
          </div>
        </>
      )}

      {/* ===== collection (paginated in place) ===== */}
      <div className="mm-section" ref={collectionRef} style={{ scrollMarginTop: 12 }}>
        <span>Your collection</span>
        <a href="/search" style={{ marginLeft: 'auto', fontSize: '0.76rem', letterSpacing: 1.4, color: 'var(--mm-dim)', textDecoration: 'none', fontWeight: 800 }}>VIEW ALL</a>
      </div>
      {collection.length === 0
        ? <EmptyState title="Your library is empty" message="Search online and add your first tracks." action={<a className="mm-btn-primary" href="/search">Discover music</a>} />
        : (
          <>
            <div className="mm-grid-songs" style={colLoading ? { opacity: 0.45, pointerEvents: 'none', transition: 'opacity .25s' } : undefined}>
              {collection.map((s, i) => (
                <SongCard
                  key={s.id}
                  song={s}
                  onPlay={() => playList(collection, i)}
                  action={<button className="mm-icon-btn" style={{ width: 30, height: 30 }} onClick={(e) => { e.stopPropagation(); p.setPlaylistModalSong(s.id); }} title="Save to playlist"><Plus size={14} /></button>}
                />
              ))}
            </div>
            <CollectionPagination pag={pag} loading={colLoading} onGo={loadCollectionPage} />
          </>
        )}
      <div style={{ height: 10 }} />
    </div>
  );
}

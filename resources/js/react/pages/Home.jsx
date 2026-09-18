import { useEffect, useMemo, useRef, useState } from 'react';
import { ChevronLeft, ChevronRight, Heart, History, LoaderCircle, Play, Plus, TrendingUp } from 'lucide-react';
import { addYouTubeToLibrary, normalizeSong, toggleFavorite } from '../lib/api';
import { usePlayer } from '../lib/player';
import { EmptyState, SongCard } from '../components/ui';

// Mood dipetakan ke genre lokal (case-insensitive).
const MOOD_GENRES = {
  fokus: ['lofi', 'lo-fi', 'ambient', 'acoustic', 'classical', 'jazz'],
  energetik: ['electronic', 'pop', 'rock', 'hip hop', 'k-pop', 'metal', 'phonk', 'dangdut', 'alternative rock', 'indie rock', 'grunge', 'edm'],
  santai: ['acoustic', 'jazz', 'soul', 'r&b', 'reggae', 'folk', 'city pop', 'indie', 'pop'],
  sedih: ['blues', 'soul', 'ambient', 'acoustic', 'folk', 'indie'],
};

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

export default function Home({ songs = [], trending = [], artists = [], pagination, mostPlayed = [], recentlyPlayed = [], myCollection = [] }) {
  const p = usePlayer();
  const [mood, setMood] = useState(null);
  const [moodTracks, setMoodTracks] = useState([]);
  const heroRef = useRef(null);
  const popularRef = useRef(null);
  const catRef = useRef(null);
  // Metadata API mentah per videoId (normalizeSong membuang field tak dikenal
  // saat masuk antrean, jadi simpan terpisah untuk auto-stub like/simpan).
  const rawByVid = useRef({});

  // ===== Trending Online: lagu API langsung tampil + bisa di-play =====
  const [onlineTrending, setOnlineTrending] = useState([]);
  const [onlineState, setOnlineState] = useState('idle');

  const onlineToSong = (t) => {
    const vid = (t.url || '').split('?v=')[1] || t.url || '';
    const s = normalizeSong({
      id: `yt-${vid}`,
      title: t.title,
      artist: t.uploaderName,
      cover: t.thumbnail,
      src: `/api/stream-audio?id=${encodeURIComponent(vid)}`,
      lyrics: '',
      duration: '',
    });
    s.vid = vid;
    s.raw = t; // metadata API asli untuk auto-stub saat di-play/like/simpan
    return s;
  };

  useEffect(() => {
    let cancelled = false;
    setOnlineState('loading');
    fetch('/api/youtube/mood/popular', { headers: { Accept: 'application/json' } })
      .then((r) => r.json().then((d) => ({ ok: r.ok, d })))
      .then(({ ok, d }) => {
        if (cancelled) return;
        if (!ok) throw new Error('bad response');
        const list = (d.results || []).map(onlineToSong);
        rawByVid.current = {};
        (d.results || []).forEach((t) => {
          const vid = (t.url || '').split('?v=')[1] || t.url || '';
          if (vid) rawByVid.current[vid] = t;
        });
        setOnlineTrending(list);
        setOnlineState('done');
      })
      .catch(() => { if (!cancelled) setOnlineState('error'); });
    return () => { cancelled = true; };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  // Pastikan lagu online punya baris DB (stub metadata) agar bisa di-like / disimpan.
  const ensureOnlineStub = async (t) => {
    const raw = rawByVid.current[t.vid] || t.raw;
    if (!raw) throw new Error('no metadata');
    const data = await addYouTubeToLibrary(raw);
    const songId = data.song?.id;
    if (!songId) throw new Error('no id');
    return songId;
  };

  const likeOnline = async (t) => {
    try {
      const songId = await ensureOnlineStub(t);
      const fav = await toggleFavorite(songId);
      const liked = fav.status === 'liked';
      p.markLiked(songId, liked);
      p.relinkQueueItem(t.id, { id: songId });
      setOnlineTrending((list) => list.map((x) => (x.vid === t.vid ? { ...x, id: songId } : x)));
      p.showToast(liked ? 'Saved to your library' : 'Removed from your library');
    } catch {
      p.showToast('Could not save track');
    }
  };

  const saveOnline = async (t) => {
    try {
      const songId = await ensureOnlineStub(t);
      p.relinkQueueItem(t.id, { id: songId });
      setOnlineTrending((list) => list.map((x) => (x.vid === t.vid ? { ...x, id: songId } : x)));
      p.setPlaylistModalSong(songId);
    } catch {
      p.showToast('Could not save track');
    }
  };

  const localSongs = useMemo(() => songs.map(normalizeSong), [songs]);
  const trendingSongs = useMemo(() => trending.map(normalizeSong), [trending]);
  const heroSongs = useMemo(() => [...localSongs].slice(0, 8), [localSongs]);
  const popular = trendingSongs.length > 0 ? trendingSongs : localSongs;
  // Satu section: isi dari API trending, fallback ke acak lokal bila API mati/kosong.
  const popularSongs = onlineState === 'done' && onlineTrending.length > 0 ? onlineTrending : popular;
  const mostPlayedSongs = useMemo(() => mostPlayed.map(normalizeSong), [mostPlayed]);
  const recentSongs = useMemo(() => recentlyPlayed.map(normalizeSong), [recentlyPlayed]);
  const mySongs = useMemo(() => myCollection.map(normalizeSong), [myCollection]);
  const recentRef = useRef(null);
  const mostRef = useRef(null);
  const mineRef = useRef(null);

  const cardPlaylistAction = (s) => (
    <button className="mm-icon-btn" style={{ width: 30, height: 30 }} onClick={(e) => { e.stopPropagation(); p.setPlaylistModalSong(s.id); }} title="Save to playlist">
      <Plus size={14} />
    </button>
  );

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

  const pickMood = (key) => {
    // Filter koleksi lokal berdasarkan genre — tanpa API, tanpa reload.
    if (!key || mood === key) {
      setMood(null);
      setMoodTracks([]);
      return;
    }
    setMood(key);
    const genres = MOOD_GENRES[key] || [];
    const pool = [...localSongs, ...trendingSongs];
    const seen = new Set();
    const matched = pool.filter((s) => {
      const g = (s.genre || '').toLowerCase();
      if (!genres.includes(g) || seen.has(s.id)) return false;
      seen.add(s.id);
      return true;
    });
    setMoodTracks(matched.slice(0, 15));
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

      {/* ===== mood results (koleksi lokal) ===== */}
      {mood && (
        moodTracks.length === 0
          ? <EmptyState title="No tracks for this mood" message="Belum ada lagu lokal dengan vibe ini. Minta admin mengimpor genre yang cocok." />
          : (
            <>
              <div className="mm-section"><span>For your mood</span></div>
              <div className="mm-grid-songs">
                {moodTracks.map((t, i) => (
                  <SongCard
                    key={t.id} song={t} onPlay={() => playList(moodTracks, i)}
                    action={<button className="mm-icon-btn" style={{ width: 30, height: 30 }} onClick={(e) => { e.stopPropagation(); p.setPlaylistModalSong(t.id); }} title="Save to playlist"><Plus size={14} /></button>}
                  />
                ))}
              </div>
            </>
          )
      )}

      {/* ===== recently played ===== */}
      {recentSongs.length > 0 && (
        <>
          <div className="mm-section mm-enter">
            <History size={19} style={{ color: 'var(--mm-teal)' }} />
            <span>Recently played</span>
            <RowChevrons targetRef={recentRef} />
          </div>
          <div className="rt-row mm-enter" ref={recentRef}>
            {recentSongs.map((s, i) => (
              <SongCard key={`r-${s.id}-${i}`} song={s} onPlay={() => playList(recentSongs, i)} action={cardPlaylistAction(s)} />
            ))}
          </div>
        </>
      )}

      {/* ===== popular songs (API trending, fallback lokal) ===== */}
      {popularSongs.length > 0 && (
        <>
          <div className="mm-section mm-enter">
            <span>Popular songs</span>
            <RowChevrons targetRef={popularRef} />
          </div>
          <div className="rt-row mm-enter" ref={popularRef}>
            {popularSongs.map((s, i) => {
              const isOnline = String(s.id).startsWith('yt-');
              const liked = p.likedIds.has(String(s.id));
              return (
                <SongCard
                  key={`${s.id}-${i}`} song={s} onPlay={() => playList(popularSongs, i)}
                  action={isOnline ? (
                    <span style={{ display: 'inline-flex', alignItems: 'center', gap: 2 }}>
                      <button
                        onClick={(e) => { e.stopPropagation(); likeOnline(s); }}
                        title="Save to library"
                        style={{ background: 'none', border: 0, cursor: 'pointer', padding: 4 }}
                      >
                        <Heart size={15} color={liked ? '#3b82f6' : '#64748f'} fill={liked ? '#3b82f6' : 'none'} />
                      </button>
                      <button className="mm-icon-btn" style={{ width: 30, height: 30 }} onClick={(e) => { e.stopPropagation(); saveOnline(s); }} title="Save to playlist">
                        <Plus size={14} />
                      </button>
                    </span>
                  ) : (
                    <button className="mm-icon-btn" style={{ width: 30, height: 30 }} onClick={(e) => { e.stopPropagation(); p.setPlaylistModalSong(s.id); }} title="Save to playlist"><Plus size={14} /></button>
                  )}
                />
              );
            })}
          </div>
        </>
      )}

      {/* ===== most played ===== */}
      {mostPlayedSongs.length > 0 && (
        <>
          <div className="mm-section mm-enter">
            <TrendingUp size={19} style={{ color: 'var(--mm-accent)' }} />
            <span>Most played</span>
            <RowChevrons targetRef={mostRef} />
          </div>
          <div className="rt-row mm-enter" ref={mostRef}>
            {mostPlayedSongs.map((s, i) => (
              <SongCard key={`m-${s.id}-${i}`} song={s} onPlay={() => playList(mostPlayedSongs, i)} action={cardPlaylistAction(s)} />
            ))}
          </div>
        </>
      )}

      {/* ===== your collection (hanya milik user; kosong untuk user baru) ===== */}
      <div className="mm-section mm-enter">
        <Heart size={19} style={{ color: 'var(--mm-accent)' }} />
        <span>Your collection</span>
        {mySongs.length > 0 && <RowChevrons targetRef={mineRef} />}
        <a href="/library" style={{ fontSize: '0.76rem', letterSpacing: 1.4, color: 'var(--mm-dim)', textDecoration: 'none', fontWeight: 800 }}>OPEN LIBRARY</a>
      </div>
      {mySongs.length === 0 ? (
        <div className="mm-glass mm-enter" style={{ padding: '30px 26px', display: 'flex', alignItems: 'center', gap: 18, flexWrap: 'wrap' }}>
          <span className="mm-empty-icon" style={{ margin: 0, width: 60, height: 60 }}><Heart size={24} /></span>
          <div style={{ flex: 1, minWidth: 220 }}>
            <div style={{ fontWeight: 800, fontSize: '1.02rem', marginBottom: 4 }}>Your collection is empty</div>
            <div style={{ color: 'var(--mm-dim)', fontSize: '0.87rem' }}>Lagu yang kamu like dan simpan ke playlist akan muncul di sini.</div>
          </div>
          <a href="/search" className="mm-btn-primary" style={{ padding: '11px 26px' }}>Find music</a>
        </div>
      ) : (
        <div className="rt-row mm-enter" ref={mineRef}>
          {mySongs.map((s, i) => (
            <SongCard key={`mine-${s.id}-${i}`} song={s} onPlay={() => playList(mySongs, i)} action={cardPlaylistAction(s)} />
          ))}
        </div>
      )}

      {/* ===== browse all music (seluruh lagu di sistem, paginasi di tempat) ===== */}
      <div className="mm-section" ref={collectionRef} style={{ scrollMarginTop: 12 }}>
        <span>Browse all music</span>
        <a href="/search" style={{ marginLeft: 'auto', fontSize: '0.76rem', letterSpacing: 1.4, color: 'var(--mm-dim)', textDecoration: 'none', fontWeight: 800 }}>VIEW ALL</a>
      </div>
      {collection.length === 0
        ? <EmptyState title="No music yet" message="Belum ada lagu di sistem. Admin bisa menambahkan via Import Music." />
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

import { createContext, useCallback, useContext, useEffect, useMemo, useRef, useState } from 'react';
import { addYouTubeToLibrary, checkFavorite, csrfToken, fetchLyrics, normalizeSong, parseLrc } from './api';

const PlayerContext = createContext(null);

export const PLAYER_STORAGE_KEY = 'mm-player-state-v1';

export function clearPersistedPlayerState() {
  try { localStorage.removeItem(PLAYER_STORAGE_KEY); } catch {}
  try { sessionStorage.removeItem(PLAYER_STORAGE_KEY); } catch {}
}

export function PlayerProvider({ children, initialFavorites = [], user = null }) {
  const audioRef = useRef(null);
  const [queue, setQueue] = useState([]);
  const [index, setIndex] = useState(-1);
  const [isPlaying, setIsPlaying] = useState(false);
  const [shuffle, setShuffle] = useState(false);
  // repeatMode: 'off' | 'all' | 'one'
  const [repeatMode, setRepeatMode] = useState(() => {
    try { return localStorage.getItem('mm-repeat') || 'off'; } catch { return 'off'; }
  });
  // Radio autoplay saat antrean habis
  const [autoplay, setAutoplay] = useState(() => {
    try {
      const v = localStorage.getItem('mm-autoplay');
      return v === null ? true : v === '1';
    } catch { return true; }
  });
  const [queueOpen, setQueueOpen] = useState(false);
  // Sleep timer: menit tersisa (null = mati)
  const [sleepLeft, setSleepLeft] = useState(null);
  const sleepTimerRef = useRef(null);
  const [progress, setProgress] = useState(0);
  const [duration, setDuration] = useState(0);
  const [currentTime, setCurrentTime] = useState(0);
  const [volume, setVolume] = useState(() => {
    try {
      const v = parseFloat(localStorage.getItem('mm-volume'));
      return Number.isFinite(v) && v >= 0 && v <= 1 ? v : 0.7;
    } catch { return 0.7; }
  });
  const [toast, setToast] = useState('');
  const [lyricsOpen, setLyricsOpen] = useState(false);
  const [lyrics, setLyrics] = useState([]);
  const [lyricsLoading, setLyricsLoading] = useState(false);
  const [playlistModalSong, setPlaylistModalSong] = useState(null);
  const [likedIds, setLikedIds] = useState(new Set(initialFavorites.map(String)));
  const [isFavorite, setIsFavorite] = useState(false);

  const current = queue[index] ?? null;
  const toastTimer = useRef(null);

  const userKey = useMemo(() => {
    if (!user) return 'guest';
    return String(user.id ?? user.email ?? `${user.name ?? ''}|${user.role ?? ''}`);
  }, [user]);

  const autoplayRef = useRef(false);

  const showToast = useCallback((msg) => {
    setToast(msg);
    clearTimeout(toastTimer.current);
    toastTimer.current = setTimeout(() => setToast(''), 2800);
  }, []);

  const loadQueue = useCallback((songs, startIndex = 0) => {
    autoplayRef.current = true;
    setQueue(songs.map(normalizeSong));
    setIndex(startIndex);
  }, []);

  const playAt = useCallback((i) => {
    autoplayRef.current = true;
    setIndex((prev) => {
      if (queue.length === 0) return prev;
      const n = ((i % queue.length) + queue.length) % queue.length;
      return n;
    });
  }, [queue.length]);

  const next = useCallback(() => {
    if (queue.length === 0) return;
    autoplayRef.current = true;
    if (shuffle) return setIndex(Math.floor(Math.random() * queue.length));
    playAt(index + 1);
  }, [index, playAt, queue.length, shuffle]);

  const prev = useCallback(() => {
    const el = audioRef.current;
    if (el && el.currentTime > 3) {
      el.currentTime = 0;
      return;
    }
    autoplayRef.current = true;
    playAt(index - 1);
  }, [index, playAt]);

  const toggle = useCallback(() => {
    const el = audioRef.current;
    if (!el) return;
    if (!current) return;
    if (el.paused) el.play().catch(() => {});
    else el.pause();
  }, [current]);

  // repeat boolean legacy (agar komponen lama tetap jalan): true = 'all'
  const repeat = repeatMode !== 'off';
  const setRepeat = useCallback((v) => {
    setRepeatMode((prev) => {
      const nextMode = typeof v === 'function' ? (v(prev !== 'off') ? 'all' : 'off') : (v ? 'all' : 'off');
      try { localStorage.setItem('mm-repeat', nextMode); } catch {}
      return nextMode;
    });
  }, []);
  const cycleRepeatMode = useCallback(() => {
    setRepeatMode((prev) => {
      const n = prev === 'off' ? 'all' : prev === 'all' ? 'one' : 'off';
      try { localStorage.setItem('mm-repeat', n); } catch {}
      return n;
    });
  }, []);

  const toggleAutoplay = useCallback(() => {
    setAutoplay((v) => {
      try { localStorage.setItem('mm-autoplay', v ? '0' : '1'); } catch {}
      return !v;
    });
  }, []);

  // ---- Queue management ----
  const removeFromQueue = useCallback((i) => {
    setQueue((q) => {
      const next_ = q.filter((_, idx) => idx !== i);
      return next_;
    });
    setIndex((prev) => {
      if (i < prev) return prev - 1;
      if (i === prev) return prev; // current dihapus: index menunjuk lagu berikutnya
      return prev;
    });
  }, []);

  const clearQueue = useCallback(() => {
    try {
      audioRef.current?.pause();
    } catch {}
    setQueue([]);
    setIndex(-1);
    setIsPlaying(false);
    setQueueOpen(false);
  }, []);

  const moveQueueItem = useCallback((from, to) => {
    setQueue((q) => {
      if (from < 0 || to < 0 || from >= q.length || to >= q.length) return q;
      const copy = [...q];
      const [item] = copy.splice(from, 1);
      copy.splice(to, 0, item);
      return copy;
    });
    setIndex((prev) => {
      if (prev === from) return to;
      if (from < prev && to >= prev) return prev - 1;
      if (from > prev && to <= prev) return prev + 1;
      return prev;
    });
  }, []);

  const playNext = useCallback((song) => {
    const s = normalizeSong(song);
    setQueue((q) => {
      const copy = [...q];
      copy.splice(index + 1, 0, s);
      return copy;
    });
    showToast('Will play next');
  }, [index, showToast]);

  // ---- Sleep timer ----
  const setSleepTimer = useCallback((mins) => {
    if (sleepTimerRef.current) clearTimeout(sleepTimerRef.current);
    if (!mins) {
      setSleepLeft(null);
      return;
    }
    setSleepLeft(mins);
    showToast(`Sleep timer: ${mins} min`);
    sleepTimerRef.current = setTimeout(() => {
      try { audioRef.current?.pause(); } catch {}
      setIsPlaying(false);
      setSleepLeft(null);
      showToast('Sleep timer ended — paused');
    }, mins * 60 * 1000);
  }, [showToast]);

  useEffect(() => () => { if (sleepTimerRef.current) clearTimeout(sleepTimerRef.current); }, []);

  // ---- Ended handler: repeat-one / repeat-all / autoplay radio ----
  const handleEnded = useCallback(() => {
    const el = audioRef.current;
    if (repeatMode === 'one') {
      if (el) { el.currentTime = 0; el.play().catch(() => {}); }
      return;
    }
    const isLast = index >= queue.length - 1;
    if (!isLast) {
      next();
      return;
    }
    if (repeatMode === 'all' && queue.length > 0) {
      autoplayRef.current = true;
      setIndex(0);
      return;
    }
    // Autoplay radio: cari lagu se-genre/se-artis (tanpa tambalan acak).
    if (autoplay && current?.id != null && !String(current.id).startsWith('yt-') && !Number.isNaN(Number(current.id))) {
      fetch(`/api/radio/${current.id}`, { headers: { Accept: 'application/json' } })
        .then((r) => r.json())
        .then((d) => {
          const list = (d.results || []).map(normalizeSong).filter((s) => s.src);
          if (list.length === 0) return;
          autoplayRef.current = true;
          setQueue((q) => [...q, ...list]);
          setIndex(queue.length); // mulai dari radio pertama
          showToast(d.seed_genre ? `Radio autoplay: ${d.seed_genre}` : 'Radio autoplay — keep listening');
        })
        .catch(() => {});
      return;
    }
    setIsPlaying(false);
  }, [repeatMode, index, queue.length, next, autoplay, current]);

  // Load song into audio element when index changes.
  useEffect(() => {
    const el = audioRef.current;
    if (!el || !current) return;
    if (el.getAttribute('src') !== current.src) el.src = current.src;
    if (!autoplayRef.current) {
      setIsPlaying(false);
      return;
    }
    el.play().then(() => setIsPlaying(true)).catch(() => setIsPlaying(false));
  }, [current]);

  useEffect(() => {
    if (audioRef.current) audioRef.current.volume = volume;
    try { localStorage.setItem('mm-volume', String(volume)); } catch {}
  }, [volume]);

  // Favorite status + lyrics per track.
  // Urutan lirik: bawaan antrean -> /api/lyrics/{id} (DB, sekali per lagu)
  // -> lrclib live. Hasil DB di-cache ke item antrean agar tak fetch ulang.
  const dbLyricsFetched = useRef(new Set());
  useEffect(() => {
    if (!current) return;
    setIsFavorite(likedIds.has(String(current.id)));
    const numericId = current.id != null && String(current.id).startsWith('yt-') === false && !Number.isNaN(Number(current.id))
      ? Number(current.id) : null;
    if (numericId != null) {
      checkFavorite(current.id).then((d) => setIsFavorite(!!d.is_favorite)).catch(() => {});
    }
    const fromQueue = parseLrc(current.lyrics || '');
    if (fromQueue.length > 0) {
      setLyrics(fromQueue);
      return;
    }
    if (numericId != null && !dbLyricsFetched.current.has(String(numericId))) {
      dbLyricsFetched.current.add(String(numericId));
      setLyricsLoading(true);
      fetch(`/api/lyrics/${numericId}`, { headers: { Accept: 'application/json' } })
        .then((r) => r.json())
        .then((d) => {
          const parsed = parseLrc(d.lyrics || '');
          if (parsed.length > 0) {
            setLyrics(parsed);
            relinkQueueItem(current.id, { lyrics: d.lyrics });
            return true;
          }
          return false;
        })
        .catch(() => false)
        .then((hit) => {
          if (hit) {
            setLyricsLoading(false);
            return;
          }
          fetchLiveLyrics();
        });
      return;
    }
    setLyricsLoading(true);
    fetchLiveLyrics();

    function fetchLiveLyrics() {
      fetchLyrics(current.title, current.artist)
      .then((d) => {
        let synced = d.syncedLyrics ? parseLrc(d.syncedLyrics) : [];
        try {
          const elDur = audioRef.current?.duration;
          const libDur = Number(d.duration);
          if (synced.length > 0 && isFinite(elDur) && elDur > 0 && isFinite(libDur) && libDur > 0) {
            if (Math.abs(libDur - elDur) > 20) synced = [];
          }
        } catch {}
        if (synced.length > 0) setLyrics(synced);
        else if (d.plainLyrics) setLyrics(d.plainLyrics.split('\n').filter(Boolean).map((txt, i) => ({ t: i * 5, txt })));
        else setLyrics([]);
      })
      .catch(() => setLyrics([]))
      .finally(() => setLyricsLoading(false));
    }
  }, [current, likedIds]);

  const markLiked = useCallback((songId, liked) => {
    setLikedIds((prev) => {
      const copy = new Set(prev);
      if (liked) copy.add(String(songId));
      else copy.delete(String(songId));
      return copy;
    });
  }, []);

  const countedForRef = useRef(null);
  useEffect(() => {
    if (!current || !isPlaying) return;
    const key = String(current.id);
    if (countedForRef.current === key) return;
    const postPlay = (numId) => {
      countedForRef.current = String(numId);
      fetch(`/api/play/${numId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json' },
      }).catch(() => {});
    };
    const id = current.id;
    if (typeof id === 'string' && id.startsWith('yt-')) {
      const raw = current.raw;
      const piped = raw && raw.url ? raw : raw && raw.raw;
      if (!piped || !piped.url) return;
      countedForRef.current = key;
      addYouTubeToLibrary(piped)
        .then((d) => {
          const songId = d.song?.id;
          if (!songId) return;
          relinkQueueItem(key, { id: songId });
          postPlay(songId);
        })
        .catch(() => {});
      return;
    }
    if (id == null || Number.isNaN(Number(id))) return;
    postPlay(id);
  }, [current, isPlaying]);

  const resumeTimeRef = useRef(null);
  const consumeResumeTime = useCallback(() => {
    const t = resumeTimeRef.current;
    resumeTimeRef.current = null;
    return t;
  }, []);

  const persistState = useCallback(() => {
    try {
      if (typeof window !== 'undefined' && window.__mm_logging_out) return;
      const el = audioRef.current;
      const slim = queue.map((s) => {
        const { raw, ...rest } = s;
        const piped = raw && raw.url ? raw : raw && raw.raw;
        if (piped && piped.url) {
          return { ...rest, raw: { url: piped.url, title: piped.title, uploaderName: piped.uploaderName, thumbnail: piped.thumbnail, duration: piped.duration } };
        }
        return rest;
      });
      localStorage.setItem(PLAYER_STORAGE_KEY, JSON.stringify({
        queue: slim,
        index,
        time: el && isFinite(el.currentTime) ? el.currentTime : 0,
        userKey,
      }));
    } catch {}
  }, [queue, index, userKey]);

  useEffect(() => {
    try {
      const rawState = localStorage.getItem(PLAYER_STORAGE_KEY);
      if (!rawState) return;
      const data = JSON.parse(rawState);
      if (!Array.isArray(data.queue) || data.queue.length === 0) return;
      if (data.userKey && data.userKey !== userKey) {
        try { localStorage.removeItem(PLAYER_STORAGE_KEY); } catch {}
        return;
      }
      const idx = Number(data.index);
      if (!Number.isInteger(idx) || idx < 0 || idx >= data.queue.length) return;
      autoplayRef.current = false;
      setQueue(data.queue.map(normalizeSong));
      setIndex(idx);
      setIsPlaying(false);
      if (typeof data.time === 'number' && data.time > 0) resumeTimeRef.current = data.time;
    } catch {}
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  useEffect(() => {
    window.addEventListener('beforeunload', persistState);
    const timer = setInterval(persistState, 10000);
    return () => {
      window.removeEventListener('beforeunload', persistState);
      clearInterval(timer);
    };
  }, [persistState]);

  const OFFSET_KEY = 'mm-lyrics-offset-v1';
  const relinkQueueItem = useCallback((oldId, patch) => {
    setQueue((q) => q.map((s) => (String(s.id) === String(oldId) ? { ...s, ...patch } : s)));
    if (patch && patch.id != null && String(patch.id) !== String(oldId)) {
      try {
        const all = JSON.parse(localStorage.getItem(OFFSET_KEY) || '{}');
        if (all[String(oldId)] !== undefined && all[String(patch.id)] === undefined) {
          all[String(patch.id)] = all[String(oldId)];
          localStorage.setItem(OFFSET_KEY, JSON.stringify(all));
        }
      } catch {}
    }
  }, []);

  const [lyricsOffset, setLyricsOffset] = useState(0);
  useEffect(() => {
    if (!current) return;
    try {
      const all = JSON.parse(localStorage.getItem(OFFSET_KEY) || '{}');
      const v = Number(all[String(current.id)]);
      setLyricsOffset(Number.isFinite(v) ? Math.max(-10, Math.min(10, v)) : 0);
    } catch { setLyricsOffset(0); }
  }, [current]);
  const shiftLyricsOffset = useCallback((delta) => {
    setLyricsOffset((prev) => {
      const n = Math.max(-10, Math.min(10, Math.round((prev + delta) * 10) / 10));
      try {
        const all = JSON.parse(localStorage.getItem(OFFSET_KEY) || '{}');
        all[String(current?.id ?? '')] = n;
        localStorage.setItem(OFFSET_KEY, JSON.stringify(all));
      } catch {}
      return n;
    });
  }, [current]);
  const resetLyricsOffset = useCallback(() => {
    try {
      const all = JSON.parse(localStorage.getItem(OFFSET_KEY) || '{}');
      delete all[String(current?.id ?? '')];
      localStorage.setItem(OFFSET_KEY, JSON.stringify(all));
    } catch {}
    setLyricsOffset(0);
  }, [current]);

  const value = useMemo(() => ({
    audioRef, queue, index, current, isPlaying, setIsPlaying,
    shuffle, setShuffle, repeat, setRepeat, repeatMode, setRepeatMode, cycleRepeatMode,
    autoplay, toggleAutoplay,
    queueOpen, setQueueOpen, removeFromQueue, clearQueue, moveQueueItem, playNext,
    sleepLeft, setSleepTimer, handleEnded,
    progress, setProgress, duration, setDuration, currentTime, setCurrentTime,
    volume, setVolume, toast, showToast, lyricsOpen, setLyricsOpen,
    lyrics, lyricsLoading, lyricsOffset, shiftLyricsOffset, resetLyricsOffset,
    playlistModalSong, setPlaylistModalSong,
    likedIds, isFavorite, markLiked, relinkQueueItem, consumeResumeTime, loadQueue, playAt, next, prev, toggle,
  }), [queue, index, current, isPlaying, shuffle, repeat, repeatMode, autoplay, queueOpen, sleepLeft, progress, duration, currentTime, volume, toast, showToast, lyricsOpen, lyrics, lyricsLoading, lyricsOffset, shiftLyricsOffset, resetLyricsOffset, playlistModalSong, likedIds, isFavorite, markLiked, relinkQueueItem, consumeResumeTime, loadQueue, playAt, next, prev, toggle, removeFromQueue, clearQueue, moveQueueItem, playNext, setSleepTimer, handleEnded, setRepeat, cycleRepeatMode, toggleAutoplay]);

  return <PlayerContext.Provider value={value}>{children}</PlayerContext.Provider>;
}

export function usePlayer() {
  const ctx = useContext(PlayerContext);
  if (!ctx) throw new Error('usePlayer must be used inside PlayerProvider');
  return ctx;
}

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
  const [repeat, setRepeat] = useState(false);
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

  // Identitas user untuk scope persisted state (cegah bocor antrean admin -> user).
  // Props user saat ini hanya {name, role}; pakai id/email bila tersedia (future-proof).
  const userKey = useMemo(() => {
    if (!user) return 'guest';
    return String(user.id ?? user.email ?? `${user.name ?? ''}|${user.role ?? ''}`);
  }, [user]);

  // Restore TIDAK boleh auto-play. Hanya aksi eksplisit user (klik lagu,
  // next/prev) yang boleh memutar. Ini cegah "baru login langsung start musik".
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

  // Load song into audio element when index changes.
  // Hasil restore (autoplayRef=false) hanya menyiapkan src + posisi, TANPA play.
  // Aksi eksplisit user (loadQueue/playAt/next/prev) set autoplayRef=true -> play.
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

  // Volume (diterapkan ke audio + disimpan agar tidak reset pindah halaman).
  useEffect(() => {
    if (audioRef.current) audioRef.current.volume = volume;
    try { localStorage.setItem('mm-volume', String(volume)); } catch {}
  }, [volume]);

  // Favorite status + lyrics per track
  useEffect(() => {
    if (!current) return;
    setIsFavorite(likedIds.has(String(current.id)));
    if (current.id != null && String(current.id).startsWith('yt-') === false && !Number.isNaN(Number(current.id))) {
      checkFavorite(current.id).then((d) => setIsFavorite(!!d.is_favorite)).catch(() => {});
    }
    const fromDb = parseLrc(current.lyrics || '');
    if (fromDb.length > 0) {
      setLyrics(fromDb);
      return;
    }
    setLyricsLoading(true);
    fetchLyrics(current.title, current.artist)
      .then((d) => {
        if (d.syncedLyrics) setLyrics(parseLrc(d.syncedLyrics));
        else if (d.plainLyrics) setLyrics(d.plainLyrics.split('\n').filter(Boolean).map((txt, i) => ({ t: i * 5, txt })));
        else setLyrics([]);
      })
      .catch(() => setLyrics([]))
      .finally(() => setLyricsLoading(false));
  }, [current, likedIds]);

  const markLiked = useCallback((songId, liked) => {
    setLikedIds((prev) => {
      const copy = new Set(prev);
      if (liked) copy.add(String(songId));
      else copy.delete(String(songId));
      return copy;
    });
  }, []);

  // Catat play ke server (fire-and-forget).
  // Hanya dihitung saat audio benar-benar berbunyi (bukan restore/paused).
  // Lagu online (yt-) dibuatkan stub DB dulu agar masuk riwayat + most played,
  // lalu item antrean di-relink ke id numerik.
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
      // raw bisa bersarang (hasil remap normalizeSong) atau kompak (hasil restore).
      const raw = current.raw;
      const piped = raw && raw.url ? raw : raw && raw.raw;
      if (!piped || !piped.url) return;
      countedForRef.current = key; // kunci segera, cegah hit ganda selama async
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

  // Simpan + pulihkan antrean agar musik survive pindah halaman.
  const resumeTimeRef = useRef(null);
  const consumeResumeTime = useCallback(() => {
    const t = resumeTimeRef.current;
    resumeTimeRef.current = null;
    return t;
  }, []);

  const persistState = useCallback(() => {
    try {
      // Jangan simpan ulang saat proses logout (mencegah state ditulis balik
      // setelah dikosongkan oleh handler logout).
      if (typeof window !== 'undefined' && window.__mm_logging_out) return;
      const el = audioRef.current;
      // eslint-disable-next-line no-unused-vars
      const slim = queue.map((s) => {
        const { raw, ...rest } = s;
        // Simpan metadata API versi kompak agar lagu online hasil restore
        // tetap bisa dibuatkan stub (untuk statistik).
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
      // Beda user (mis. admin logout -> login sebagai user) -> buang antrean lama.
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

  // Ganti identitas item antrean (mis. lagu online yt-xxx -> id DB numerik
  // setelah dibuatkan stub) tanpa mengganggu pemutaran.
  const relinkQueueItem = useCallback((oldId, patch) => {
    setQueue((q) => q.map((s) => (String(s.id) === String(oldId) ? { ...s, ...patch } : s)));
  }, []);

  const value = useMemo(() => ({
    audioRef, queue, index, current, isPlaying, setIsPlaying,
    shuffle, setShuffle, repeat, setRepeat,
    progress, setProgress, duration, setDuration, currentTime, setCurrentTime,
    volume, setVolume, toast, showToast, lyricsOpen, setLyricsOpen,
    lyrics, lyricsLoading, playlistModalSong, setPlaylistModalSong,
    likedIds, isFavorite, markLiked, relinkQueueItem, consumeResumeTime, loadQueue, playAt, next, prev, toggle,
  }), [queue, index, current, isPlaying, shuffle, repeat, progress, duration, currentTime, volume, toast, showToast, lyricsOpen, lyrics, lyricsLoading, playlistModalSong, likedIds, isFavorite, markLiked, relinkQueueItem, consumeResumeTime, loadQueue, playAt, next, prev, toggle]);

  return <PlayerContext.Provider value={value}>{children}</PlayerContext.Provider>;
}

export function usePlayer() {
  const ctx = useContext(PlayerContext);
  if (!ctx) throw new Error('usePlayer must be used inside PlayerProvider');
  return ctx;
}

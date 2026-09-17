import { createContext, useCallback, useContext, useEffect, useMemo, useRef, useState } from 'react';
import { checkFavorite, fetchLyrics, normalizeSong, parseLrc } from './api';

const PlayerContext = createContext(null);

export function PlayerProvider({ children, initialFavorites = [] }) {
  const audioRef = useRef(null);
  const [queue, setQueue] = useState([]);
  const [index, setIndex] = useState(-1);
  const [isPlaying, setIsPlaying] = useState(false);
  const [shuffle, setShuffle] = useState(false);
  const [repeat, setRepeat] = useState(false);
  const [progress, setProgress] = useState(0);
  const [duration, setDuration] = useState(0);
  const [currentTime, setCurrentTime] = useState(0);
  const [volume, setVolume] = useState(0.7);
  const [toast, setToast] = useState('');
  const [lyricsOpen, setLyricsOpen] = useState(false);
  const [lyrics, setLyrics] = useState([]);
  const [lyricsLoading, setLyricsLoading] = useState(false);
  const [playlistModalSong, setPlaylistModalSong] = useState(null);
  const [likedIds, setLikedIds] = useState(new Set(initialFavorites.map(String)));
  const [isFavorite, setIsFavorite] = useState(false);

  const current = queue[index] ?? null;
  const toastTimer = useRef(null);

  const showToast = useCallback((msg) => {
    setToast(msg);
    clearTimeout(toastTimer.current);
    toastTimer.current = setTimeout(() => setToast(''), 2800);
  }, []);

  const loadQueue = useCallback((songs, startIndex = 0) => {
    setQueue(songs.map(normalizeSong));
    setIndex(startIndex);
  }, []);

  const playAt = useCallback((i) => {
    setIndex((prev) => {
      if (queue.length === 0) return prev;
      const n = ((i % queue.length) + queue.length) % queue.length;
      return n;
    });
  }, [queue.length]);

  const next = useCallback(() => {
    if (queue.length === 0) return;
    if (shuffle) return setIndex(Math.floor(Math.random() * queue.length));
    playAt(index + 1);
  }, [index, playAt, queue.length, shuffle]);

  const prev = useCallback(() => {
    const el = audioRef.current;
    if (el && el.currentTime > 3) {
      el.currentTime = 0;
      return;
    }
    playAt(index - 1);
  }, [index, playAt]);

  const toggle = useCallback(() => {
    const el = audioRef.current;
    if (!el) return;
    if (!current) return;
    if (el.paused) el.play().catch(() => {});
    else el.pause();
  }, [current]);

  // Load song into audio element when index changes
  useEffect(() => {
    const el = audioRef.current;
    if (!el || !current) return;
    el.src = current.src;
    el.play().then(() => setIsPlaying(true)).catch(() => setIsPlaying(false));
  }, [current]);

  // Volume
  useEffect(() => {
    if (audioRef.current) audioRef.current.volume = volume;
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
    likedIds, isFavorite, markLiked, relinkQueueItem, loadQueue, playAt, next, prev, toggle,
  }), [queue, index, current, isPlaying, shuffle, repeat, progress, duration, currentTime, volume, toast, showToast, lyricsOpen, lyrics, lyricsLoading, playlistModalSong, likedIds, isFavorite, markLiked, relinkQueueItem, loadQueue, playAt, next, prev, toggle]);

  return <PlayerContext.Provider value={value}>{children}</PlayerContext.Provider>;
}

export function usePlayer() {
  const ctx = useContext(PlayerContext);
  if (!ctx) throw new Error('usePlayer must be used inside PlayerProvider');
  return ctx;
}

export function csrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

export function songSrc(s) {
  if (!s) return '';
  if (s.src) return s.src;
  if (s.preview_url) return s.preview_url;
  // File lokal diutamakan: lagu hasil import punya file_path DAN youtube_id,
  // dan file lokal selalu bisa diputar sedangkan stream-audio (yt-dlp) flaky.
  const fp = s.file_path ?? '';
  if (fp) {
    const base = fp.split('/').pop();
    return `/stream-music/${encodeURIComponent(base)}`;
  }
  if (s.youtube_id) return `/api/stream-audio?id=${encodeURIComponent(s.youtube_id)}`;
  return '';
}

export function songCover(s) {
  if (!s) return '/images/default-cover.png';
  return s.cover || s.artwork_url || s.album_art || s.artist_image || '/images/default-cover.png';
}

export function normalizeSong(s) {
  return {
    id: s.id,
    title: s.title ?? 'Unknown Title',
    artist: s.artist ?? 'Unknown Artist',
    genre: s.genre ?? '',
    duration: s.duration ?? '',
    cover: songCover(s),
    src: songSrc(s),
    lyrics: s.lyrics ?? '',
    youtube_id: s.youtube_id ?? null,
    raw: s,
  };
}

async function jsonOrThrow(res) {
  const data = await res.json().catch(() => ({}));
  if (!res.ok) throw new Error(data.message || `Request failed (${res.status})`);
  return data;
}

export async function toggleFavorite(songId) {
  const res = await fetch(`/favorites/toggle/${songId}`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json' },
  });
  return jsonOrThrow(res);
}

export async function checkFavorite(songId) {
  const res = await fetch(`/favorites/check/${songId}?t=${Date.now()}`, { headers: { Accept: 'application/json' } });
  return jsonOrThrow(res);
}

export async function toggleHeroPin(songId) {
  const res = await fetch(`/hero-pins/toggle/${songId}`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json' },
  });
  return jsonOrThrow(res);
}

export async function addSongToPlaylist(playlistId, songId) {
  const res = await fetch('/playlist/add-song', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json' },
    body: JSON.stringify({ playlist_id: playlistId, song_id: songId }),
  });
  return jsonOrThrow(res);
}

export async function searchOnline(query) {
  const res = await fetch(`/api/youtube/search?q=${encodeURIComponent(query)}`, { headers: { Accept: 'application/json' } });
  return jsonOrThrow(res);
}

export async function moodOnline(mood) {
  const res = await fetch(`/api/youtube/mood/${encodeURIComponent(mood)}`, { headers: { Accept: 'application/json' } });
  return jsonOrThrow(res);
}

export async function addYouTubeToLibrary(track) {
  const videoId = track.url?.split('?v=')[1] ?? track.youtube_id ?? track.id;
  const res = await fetch('/api/youtube/add-to-library', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json' },
    body: JSON.stringify({
      videoId,
      title: track.title,
      uploaderName: track.uploaderName || track.artist,
      thumbnail: track.thumbnail || track.cover,
      duration: track.duration || 0,
    }),
  });
  return jsonOrThrow(res);
}

export async function fetchLyrics(title, artist) {
  const clean = (v) => (v ?? '').replace(/\[.*?\]|\(.*?\)/g, '').replace(/ - topic/i, '').trim();
  const res = await fetch(`https://lrclib.net/api/get?artist_name=${encodeURIComponent(clean(artist))}&track_name=${encodeURIComponent(clean(title))}`);
  if (!res.ok) throw new Error('lyrics-not-found');
  return res.json();
}

export function parseLrc(lrc) {
  if (!lrc) return [];
  // Beberapa file LRC punya tag offset global, mis. [offset:+500] (milidetik).
  // Tanpa ini seluruh lirik geser setengah detik atau lebih.
  let offsetMs = 0;
  const off = /\[offset:\s*([+-]?\d+)\]/i.exec(lrc);
  if (off) offsetMs = parseInt(off[1], 10) || 0;
  const reg = /\[(\d{1,3}):(\d{1,2})(?:[.:](\d{1,3}))?\]/;
  return lrc
    .split('\n')
    .map((line) => {
      const m = reg.exec(line);
      if (!m) return null;
      const t = parseFloat(m[1]) * 60 + parseFloat(m[2]) + (m[3] ? parseFloat(`0.${m[3]}`) : 0) + offsetMs / 1000;
      const txt = line.replace(reg, '').trim();
      return txt ? { t, txt } : null;
    })
    .filter(Boolean)
    .sort((a, b) => a.t - b.t);
}

export function formatTime(s) {
  if (!isFinite(s) || s == null) return '0:00';
  const m = Math.floor(s / 60);
  const sec = Math.floor(s % 60);
  return `${m}:${sec < 10 ? '0' : ''}${sec}`;
}

export async function togglePlaylistPublic(playlistId) {
  const res = await fetch(`/playlist/${playlistId}/toggle-public`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json' },
  });
  return jsonOrThrow(res);
}

export async function forkPlaylist(playlistId) {
  const res = await fetch(`/playlist/${playlistId}/fork`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json' },
  });
  return res;
}

export async function fetchRadio(songId) {
  const res = await fetch(`/api/radio/${songId}`, { headers: { Accept: 'application/json' } });
  return jsonOrThrow(res);
}

export async function fetchRecommendations() {
  const res = await fetch('/api/recommendations', { headers: { Accept: 'application/json' } });
  return jsonOrThrow(res);
}

export async function toggleArtistFollow(artistName) {
  const res = await fetch(`/artist/${encodeURIComponent(artistName)}/follow`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json' },
  });
  return jsonOrThrow(res);
}

export function copyLink(url, showToast) {
  const done = () => showToast && showToast('Link copied to clipboard');
  if (navigator.clipboard?.writeText) {
    navigator.clipboard.writeText(url).then(done).catch(() => fallbackCopy(url, showToast));
  } else fallbackCopy(url, showToast);
}

function fallbackCopy(url, showToast) {
  try {
    const ta = document.createElement('textarea');
    ta.value = url;
    document.body.appendChild(ta);
    ta.select();
    document.execCommand('copy');
    document.body.removeChild(ta);
    showToast && showToast('Link copied to clipboard');
  } catch {
    showToast && showToast(url);
  }
}

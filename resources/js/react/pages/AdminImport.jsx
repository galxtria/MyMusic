import { useState } from 'react';
import { Check, CloudDownload, ListMusic, LoaderCircle, Play, Search as SearchIcon } from 'lucide-react';
import { GENRES } from './Admin';
import { normalizeSong } from '../lib/api';
import { usePlayer } from '../lib/player';
import { EmptyState, LoadingRow, PageHeader, ErrorRow } from '../components/ui';

function fmtSecs(s) {
  const n = parseInt(s, 10) || 0;
  return `${Math.floor(n / 60)}:${String(n % 60).padStart(2, '0')}`;
}

// Hasil API langsung bisa diputar (stream on-demand) tanpa harus diimpor dulu.
function apiTrackToSong(t) {
  const vid = (t.url || '').split('?v=')[1] || t.url || '';
  const s = normalizeSong({
    id: `yt-${vid}`,
    title: t.title,
    artist: t.uploaderName,
    cover: t.thumbnail,
    src: `/api/stream-audio?id=${encodeURIComponent(vid)}`,
    lyrics: '',
    duration: fmtSecs(t.duration),
  });
  s.vid = vid;
  s.raw = t; // metadata API asli untuk auto-stub saat di-play
  return s;
}

export default function AdminImport() {
  const p = usePlayer();
  const [query, setQuery] = useState('');
  const [genre, setGenre] = useState('Pop');
  const [results, setResults] = useState([]);
  const [state, setState] = useState('idle');
  const [importingId, setImportingId] = useState(null);
  const [importedIds, setImportedIds] = useState(new Set());
  const [notice, setNotice] = useState('');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

  const runSearch = async (e) => {
    e?.preventDefault();
    const q = query.trim();
    if (!q || state === 'loading') return;
    setState('loading');
    setNotice('');
    try {
      const res = await fetch(`/api/youtube/search?q=${encodeURIComponent(q)}`, { headers: { Accept: 'application/json' } });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(data.message || 'search failed');
      setResults(data.results || []);
      setState('done');
    } catch (err) {
      setState('error');
    }
  };

  const videoIdOf = (t) => (t.url || '').split('?v=')[1] || t.url || '';

  const playResult = (index) => {
    p.loadQueue(results.map(apiTrackToSong), index);
    p.showToast('Streaming dari API...');
  };

  const doImport = async (t) => {
    const videoId = videoIdOf(t);
    if (!videoId || importingId) return;
    setImportingId(videoId);
    try {
      const res = await fetch('/admin/tools/import', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
        body: JSON.stringify({
          videoId,
          title: t.title,
          uploaderName: t.uploaderName,
          thumbnail: t.thumbnail,
          duration: parseInt(t.duration, 10) || 0,
          genre,
        }),
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(data.message || 'import failed');
      setNotice(data.message || 'Berhasil diimpor ke library!');
      setImportedIds((prev) => new Set(prev).add(videoId));
    } catch (err) {
      setNotice(err.message || 'Gagal mengimpor. Coba lagi.');
    } finally {
      setImportingId(null);
    }
  };

  return (
    <div>
      <PageHeader
        badge="ADMIN"
        title="Import music"
        subtitle="Cari via API, lalu unduh audio full + cover ke server sebagai lagu lokal."
        action={<a href="/admin/songs" className="mm-btn-ghost"><ListMusic size={15} /> Back to songs</a>}
      />

      <form onSubmit={runSearch} className="mm-enter" style={{ display: 'flex', gap: 10, marginBottom: 14, flexWrap: 'wrap' }}>
        <div className="mm-admin-search" style={{ flex: 1, minWidth: 240 }}>
          <SearchIcon size={17} style={{ color: 'var(--mm-faint)', flexShrink: 0 }} />
          <input value={query} onChange={(e) => setQuery(e.target.value)} placeholder="Cari judul atau artis..." autoComplete="off" />
        </div>
        <select value={genre} onChange={(e) => setGenre(e.target.value)} className="mm-select" style={{ width: 180 }} title="Genre untuk lagu yang diimpor">
          {GENRES.map((g) => <option key={g} value={g}>{g}</option>)}
        </select>
        <button type="submit" className="mm-btn-primary" disabled={state === 'loading'}>
          {state === 'loading' ? <LoaderCircle size={16} className="mm-spin" /> : <SearchIcon size={16} />} Search API
        </button>
      </form>

      {notice && (
        <div className="mm-glass mm-enter" style={{ padding: '12px 18px', marginBottom: 16, fontWeight: 700, fontSize: '0.88rem' }}>
          {notice}
        </div>
      )}

      {state === 'idle' && (
        <EmptyState
          icon={CloudDownload}
          title="Belum ada pencarian"
          message="Ketik kata kunci lalu Search API. Hasil import tersimpan sebagai file lokal (audio + cover) sehingga user memutar tanpa tergantung API."
        />
      )}
      {state === 'loading' && <LoadingRow text="Mencari di API..." />}
      {state === 'error' && <ErrorRow text="API tidak bisa dihubungi. Coba lagi nanti." />}
      {state === 'done' && results.length === 0 && (
        <EmptyState icon={SearchIcon} title="Tidak ada hasil" message="Coba kata kunci lain." />
      )}

      {state === 'done' && results.length > 0 && (
        <div className="mm-glass mm-enter" style={{ padding: 8, overflowX: 'auto' }}>
          <table className="mm-admin-table" style={{ minWidth: 720 }}>
            <thead><tr><th>THUMBNAIL</th><th>TRACK</th><th>DURATION</th><th style={{ textAlign: 'right' }}>PLAY / IMPORT</th></tr></thead>
            <tbody>
              {results.map((t, i) => {
                const vid = videoIdOf(t);
                const done = importedIds.has(vid);
                const busy = importingId === vid;
                return (
                  <tr key={`${vid}-${i}`}>
                    <td>
                      <img src={t.thumbnail} alt="" style={{ width: 92, height: 52, borderRadius: 8, objectFit: 'cover', display: 'block' }} onError={(e) => { e.currentTarget.src = '/images/default-cover.png'; }} />
                    </td>
                    <td>
                      <div style={{ fontWeight: 800 }}>{t.title}</div>
                      <div style={{ color: 'var(--mm-dim)', fontSize: '0.82rem' }}>{t.uploaderName}</div>
                    </td>
                    <td style={{ color: 'var(--mm-dim)', fontFamily: 'monospace', fontSize: '0.85rem' }}>{fmtSecs(t.duration)}</td>
                    <td>
                      <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 8, alignItems: 'center' }}>
                        <button className="mm-icon-btn" style={{ width: 36, height: 36 }} title="Putar langsung dari API" onClick={() => playResult(i)}>
                          <Play size={15} style={{ marginLeft: 2 }} />
                        </button>
                        {done ? (
                          <span className="mm-badge-role mm-role-user"><Check size={12} /> IMPORTED</span>
                        ) : (
                          <button className="mm-btn-primary" style={{ padding: '9px 20px', fontSize: '0.82rem' }} disabled={busy} onClick={() => doImport(t)}>
                            {busy ? <LoaderCircle size={14} className="mm-spin" /> : <CloudDownload size={14} />}
                            {busy ? 'Downloading...' : 'Import'}
                          </button>
                        )}
                      </div>
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
          <p style={{ color: 'var(--mm-faint)', fontSize: '0.78rem', padding: '4px 16px 10px', margin: 0 }}>
            Ikon play = streaming langsung dari API (bisa dicoba sebelum import). Import mengunduh audio full (~10–60 detik per lagu, butuh cookies.txt jika YouTube memblokir).
          </p>
        </div>
      )}
    </div>
  );
}

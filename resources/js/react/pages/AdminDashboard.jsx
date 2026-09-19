import { BarChart3, CalendarDays, Disc3, Globe, ListMusic, Lock, Music, Radio, Share2, CopyPlus, Trash2, Pencil, Play, User, Users } from 'lucide-react';
import { PageHeader } from '../components/ui';

function Bar({ label, value, max }) {
  const pct = max > 0 ? Math.round((value / max) * 100) : 0;
  return (
    <div style={{ display: 'flex', alignItems: 'center', gap: 10, marginBottom: 7 }}>
      <span style={{ width: 52, fontSize: '0.7rem', color: 'var(--mm-faint)', fontWeight: 700 }}>{label}</span>
      <div style={{ flex: 1, height: 10, borderRadius: 99, background: 'rgba(255,255,255,0.06)', overflow: 'hidden' }}>
        <div style={{ width: `${pct}%`, height: '100%', background: 'var(--mm-accent)', borderRadius: 99 }} />
      </div>
      <span style={{ width: 40, textAlign: 'right', fontSize: '0.75rem', fontWeight: 800 }}>{value}</span>
    </div>
  );
}

export default function AdminDashboard({ stats = {}, playsPerDay = {}, topSongs = [], topUsers = [], genreDist = [], dupArtists = [] }) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
  const days = Object.entries(playsPerDay || {});
  const maxPlays = Math.max(1, ...days.map(([, v]) => v));
  const maxGenre = Math.max(1, ...(genreDist || []).map((g) => g.c));

  return (
    <div>
      <PageHeader badge="ADMIN" title="Dashboard" subtitle="Platform overview — plays, users, and catalog health." />

      <div className="mm-stats mm-enter">
        {[
          { value: stats.totalPlays ?? 0, label: 'TOTAL PLAYS', icon: Play },
          { value: stats.totalSongs ?? 0, label: 'TOTAL SONGS', icon: Music },
          { value: stats.totalUsers ?? 0, label: 'USERS', icon: Users },
          { value: stats.totalPlaylists ?? 0, label: 'PLAYLISTS', icon: ListMusic },
        ].map((s, i) => (
          <div key={i} className="mm-stat">
            <div className="mm-stat-value">{s.value}</div>
            <div className="mm-stat-label">{s.label}</div>
            <s.icon size={36} className="mm-stat-icon" />
          </div>
        ))}
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))', gap: 14, marginTop: 4 }}>
        <div className="mm-glass mm-enter" style={{ padding: 22 }}>
          <h3 style={{ margin: '0 0 14px', fontSize: '0.95rem', display: 'flex', alignItems: 'center', gap: 8 }}><BarChart3 size={16} /> Plays — last 14 days</h3>
          {days.map(([d, v]) => (
            <Bar key={d} label={d.slice(5)} value={v} max={maxPlays} />
          ))}
        </div>

        <div className="mm-glass mm-enter" style={{ padding: 22 }}>
          <h3 style={{ margin: '0 0 14px', fontSize: '0.95rem', display: 'flex', alignItems: 'center', gap: 8 }}><Disc3 size={16} /> Top tracks</h3>
          {(topSongs || []).map((s, i) => (
            <div key={s.id} style={{ display: 'flex', alignItems: 'center', gap: 12, padding: '7px 0', borderBottom: '1px solid var(--mm-line)' }}>
              <span style={{ fontFamily: 'monospace', color: 'var(--mm-faint)', fontWeight: 800 }}>#{i + 1}</span>
              <img src={s.artwork_url || s.album_art || '/images/default-cover.png'} alt="" style={{ width: 38, height: 38, borderRadius: 9, objectFit: 'cover' }} onError={(e) => { e.currentTarget.src = '/images/default-cover.png'; }} />
              <div style={{ flex: 1, minWidth: 0 }}>
                <div style={{ fontWeight: 700, fontSize: '0.85rem', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>{s.title}</div>
                <div style={{ color: 'var(--mm-dim)', fontSize: '0.75rem' }}>{s.artist}</div>
              </div>
              <span style={{ fontSize: '0.78rem', fontWeight: 800, color: 'var(--mm-accent)' }}>{s.play_count} plays</span>
            </div>
          ))}
          {(!topSongs || topSongs.length === 0) && <p style={{ color: 'var(--mm-dim)', fontSize: '0.85rem' }}>No plays recorded yet.</p>}
        </div>
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))', gap: 14, marginTop: 14 }}>
        <div className="mm-glass mm-enter" style={{ padding: 22 }}>
          <h3 style={{ margin: '0 0 14px', fontSize: '0.95rem', display: 'flex', alignItems: 'center', gap: 8 }}><Users size={16} /> Most active users</h3>
          {(topUsers || []).map((u) => (
            <div key={u.id} style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '7px 0', borderBottom: '1px solid var(--mm-line)' }}>
              <span className="mm-avatar" style={{ width: 34, height: 34 }}>{(u.name || 'U').slice(0, 1).toUpperCase()}</span>
              <div style={{ flex: 1, minWidth: 0 }}>
                <div style={{ fontWeight: 700, fontSize: '0.85rem' }}>{u.name}</div>
                <div style={{ color: 'var(--mm-faint)', fontSize: '0.72rem', fontFamily: 'monospace' }}>{u.email}</div>
              </div>
              <span style={{ fontSize: '0.78rem', fontWeight: 800 }}>{u.plays ?? 0} plays</span>
            </div>
          ))}
        </div>

        <div className="mm-glass mm-enter" style={{ padding: 22 }}>
          <h3 style={{ margin: '0 0 14px', fontSize: '0.95rem', display: 'flex', alignItems: 'center', gap: 8 }}><Radio size={16} /> Genre by plays</h3>
          {(genreDist || []).map((g) => (
            <Bar key={g.genre || '-'} label={(g.genre || '-').slice(0, 8)} value={g.c} max={maxGenre} />
          ))}
          <p style={{ color: 'var(--mm-faint)', fontSize: '0.72rem', margin: '10px 0 0' }}>Berdasarkan total plays per genre. K-Pop & Pop dihitung terpisah.</p>
          <div style={{ display: 'flex', gap: 10, marginTop: 16, flexWrap: 'wrap' }}>
            <a href="/admin/songs" className="mm-btn-ghost"><Music size={14} /> Manage songs</a>
            <a href="/admin/users" className="mm-btn-ghost"><Users size={14} /> Manage users</a>
          </div>
        </div>
      </div>

      <div className="mm-glass mm-enter" style={{ padding: 22, marginTop: 14 }}>
        <h3 style={{ margin: '0 0 6px', fontSize: '0.95rem', display: 'flex', alignItems: 'center', gap: 8 }}><Users size={16} /> Duplicate artists</h3>
        <p style={{ color: 'var(--mm-faint)', fontSize: '0.78rem', margin: '0 0 14px' }}>
          Varian ejaan yang sama secara case-insensitive (mis. Cortis vs CORTIS). Import baru otomatis memakai ejaan yang sudah ada; yang telanjur dobel gabungkan di sini.
        </p>
        {dupArtists.length === 0 && <p style={{ color: 'var(--mm-dim)', fontSize: '0.85rem', margin: 0 }}>Bersih — tidak ada artis ganda.</p>}
        {dupArtists.map((g) => (
          <div key={g.key} style={{ display: 'flex', alignItems: 'center', gap: 12, padding: '9px 0', borderBottom: '1px solid var(--mm-line)', flexWrap: 'wrap' }}>
            <div style={{ flex: 1, minWidth: 200 }}>
              <div style={{ fontWeight: 800 }}>{g.variants.join('  vs  ')}</div>
              <div style={{ color: 'var(--mm-dim)', fontSize: '0.78rem' }}>{g.songs} songs • canonical: {g.canonical}</div>
            </div>
            <form action="/admin/artists/merge" method="POST" onSubmit={(e) => { if (!confirm(`Merge ${g.variants.join(' + ')} into "${g.canonical}"?`)) e.preventDefault(); }}>
              <input type="hidden" name="_token" value={csrf} />
              <input type="hidden" name="canonical" value={g.canonical} />
              <button className="mm-btn-primary" style={{ padding: '9px 20px', fontSize: '0.8rem' }} type="submit">Merge to {g.canonical}</button>
            </form>
          </div>
        ))}
      </div>
    </div>
  );
}

// Re-export agar tree tetap kompatibel bila ada yang import dari file ini
export { BarChart3, CalendarDays, ListMusic, Lock, Globe, Share2, CopyPlus, Trash2, Pencil, Play, User };

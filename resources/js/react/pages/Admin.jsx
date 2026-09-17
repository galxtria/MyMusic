import { useState } from 'react';
import {
  ArrowLeft, CalendarDays, CircleX, CloudUpload, ListMusic, Mic, Music, Pencil,
  Plus, Search as SearchIcon, ShieldCheck, Tags, Trash2, Upload, UserPlus, Users,
} from 'lucide-react';
import { EmptyState, PageHeader } from '../components/ui';

export const GENRES = [
  'Acoustic', 'Alternative Rock', 'Ambient', 'Blues', 'Britpop', 'City Pop', 'Classical',
  'Dangdut', 'Dream Pop', 'Electronic', 'Folk', 'Grunge', 'Hip Hop', 'Indie', 'Indie Rock',
  'Jazz', 'K-Pop', 'Lofi', 'Metal', 'Phonk', 'Pop', 'Post-Punk', 'R&B', 'Reggae',
  'Shoegaze', 'Soul', 'Synthwave',
];

function Stats({ items }) {
  return (
    <div className="mm-stats mm-enter">
      {items.map((s, i) => (
        <div key={i} className="mm-stat">
          <div className="mm-stat-glow" style={{ background: s.gradient }} />
          <div className="mm-stat-value">{s.value}</div>
          <div className="mm-stat-label">{s.label}</div>
          <s.icon size={40} className="mm-stat-icon" />
        </div>
      ))}
    </div>
  );
}

function Pagination({ pagination }) {
  if (!pagination || pagination.lastPage <= 1) return null;
  return (
    <div className="mm-pagination">
      {pagination.prev
        ? <a className="mm-btn-ghost" style={{ padding: '10px 22px' }} href={pagination.prev}>Previous</a>
        : <span className="mm-btn-ghost" style={{ padding: '10px 22px', opacity: 0.35 }}>Previous</span>}
      <span style={{ color: 'var(--mm-faint)', fontWeight: 700, fontSize: '0.85rem' }}>
        Page {pagination.current} of {pagination.lastPage}
        {pagination.total != null && ` • ${pagination.total} total`}
      </span>
      {pagination.next
        ? <a className="mm-btn-ghost" style={{ padding: '10px 22px' }} href={pagination.next}>Next</a>
        : <span className="mm-btn-ghost" style={{ padding: '10px 22px', opacity: 0.35 }}>Next</span>}
    </div>
  );
}

/* ================= SONGS INDEX ================= */

export function AdminSongs({ songs = [], stats = {}, pagination = null, search = '' }) {
  return (
    <div>
      <PageHeader
        badge="ADMIN"
        title="Library manager"
        subtitle="Your music content control center."
        action={(
          <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
            <span className="mm-avatar">{(stats.adminName || 'A').slice(0, 1).toUpperCase()}</span>
            <div>
              <div style={{ fontWeight: 800, fontSize: '0.9rem' }}>{stats.adminName}</div>
              <div style={{ color: 'var(--mm-faint)', fontSize: '0.75rem' }}>Administrator</div>
            </div>
          </div>
        )}
      />

      <Stats
        items={[
          { value: stats.totalSongs ?? songs.length, label: 'TOTAL SONGS', icon: Music, gradient: 'linear-gradient(45deg,#3b82f6,#60a5fa)' },
          { value: stats.genreCount ?? '-', label: 'UNIQUE GENRES', icon: Tags, gradient: 'linear-gradient(45deg,#ec4899,#8b5cf6)' },
          { value: stats.artistCount ?? '-', label: 'ARTISTS', icon: Mic, gradient: 'linear-gradient(45deg,#10b981,#3b82f6)' },
        ]}
      />

      <div className="mm-enter" style={{ display: 'flex', gap: 12, marginBottom: 18, flexWrap: 'wrap' }}>
        <form action="/admin/songs" method="GET" style={{ flex: 1, minWidth: 240 }}>
          <div className="mm-admin-search">
            <SearchIcon size={17} style={{ color: 'var(--mm-faint)', flexShrink: 0 }} />
            <input name="search" defaultValue={search} placeholder="Search title, artist, or genre..." />
            {search && (
              <a href="/admin/songs" title="Clear search" style={{ color: 'var(--mm-faint)', display: 'grid', placeItems: 'center' }}>
                <CircleX size={17} />
              </a>
            )}
          </div>
        </form>
        <a href="/admin/songs/create" className="mm-btn-primary"><Plus size={16} /> Upload song</a>
      </div>

      {songs.length === 0 ? (
        <EmptyState
          icon={Music}
          title={search ? 'No songs match your search' : 'No songs in the library yet'}
          message={search ? 'Try different keywords or clear the search.' : 'Upload your first track to get started.'}
          action={search
            ? <a className="mm-btn-ghost" href="/admin/songs">Clear search</a>
            : <a className="mm-btn-primary" href="/admin/songs/create"><Upload size={15} /> Upload track</a>}
        />
      ) : (
        <div className="mm-glass mm-enter" style={{ padding: 8, overflowX: 'auto' }}>
          <table className="mm-admin-table" style={{ minWidth: 760 }}>
            <thead><tr><th>COVER</th><th>SONG INFO</th><th>GENRE</th><th>DURATION</th><th>UPLOADED</th><th style={{ textAlign: 'right' }}>ACTIONS</th></tr></thead>
            <tbody>
              {songs.map((s) => (
                <tr key={s.id}>
                  <td><img src={s.artwork_url || s.album_art || '/images/default-cover.png'} alt="" style={{ width: 46, height: 46, borderRadius: 10, objectFit: 'cover', display: 'block' }} onError={(e) => { e.currentTarget.src = '/images/default-cover.png'; }} /></td>
                  <td>
                    <div style={{ fontWeight: 800 }}>{s.title}</div>
                    <div style={{ color: 'var(--mm-dim)', fontSize: '0.82rem' }}>{s.artist}</div>
                  </td>
                  <td>{s.genre ? <span className="mm-genre-badge">{s.genre}</span> : <span style={{ color: 'var(--mm-faint)' }}>-</span>}</td>
                  <td style={{ color: 'var(--mm-dim)', fontFamily: 'monospace', fontSize: '0.85rem' }}>{s.duration || '--:--'}</td>
                  <td style={{ color: 'var(--mm-dim)', fontSize: '0.82rem', whiteSpace: 'nowrap' }}>{s.uploaded || ''}</td>
                  <td>
                    <AdminSongActions id={s.id} />
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
      <Pagination pagination={pagination} />
    </div>
  );
}

function AdminSongActions({ id }) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
  return (
    <div style={{ display: 'flex', gap: 8, justifyContent: 'flex-end' }}>
      <a className="mm-icon-btn" style={{ width: 36, height: 36, color: '#fbbf24', borderColor: 'rgba(245,158,11,0.35)' }} href={`/admin/songs/${id}/edit`} title="Edit"><Pencil size={14} /></a>
      <form action={`/admin/songs/${id}`} method="POST" onSubmit={(e) => { if (!confirm('Delete this song?')) e.preventDefault(); }}>
        <input type="hidden" name="_token" value={csrf} />
        <input type="hidden" name="_method" value="DELETE" />
        <button className="mm-icon-btn" style={{ width: 36, height: 36, color: '#f87171', borderColor: 'rgba(239,68,68,0.35)' }} type="submit" title="Delete"><Trash2 size={14} /></button>
      </form>
    </div>
  );
}

/* ================= SONG FORM ================= */

export function AdminSongForm({ song = null, errors = {} }) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
  const isEdit = !!song;
  const [coverPreview, setCoverPreview] = useState('');
  const currentCover = song?.artwork_url || song?.album_art || '';

  return (
    <div style={{ maxWidth: 920 }}>
      <PageHeader badge="ADMIN" title={isEdit ? 'Edit track' : 'Upload track'} subtitle={isEdit ? song.title : 'Add a new song to the catalog.'} />
      <form action={isEdit ? `/admin/songs/${song.id}` : '/admin/songs'} method="POST" encType="multipart/form-data" className="mm-glass mm-enter" style={{ padding: 30 }}>
        <input type="hidden" name="_token" value={csrf} />
        {isEdit && <input type="hidden" name="_method" value="PUT" />}
        <div style={{ display: 'grid', gridTemplateColumns: '260px 1fr', gap: 28 }}>
          <div>
            <label className="mm-label">COVER ART {!isEdit && '(REQUIRED)'}</label>
            <label style={{ display: 'block', width: '100%', aspectRatio: '1/1', borderRadius: 18, overflow: 'hidden', cursor: 'pointer', border: '1px dashed var(--mm-line-2)', background: 'rgba(0,0,0,0.3)', position: 'relative' }}>
              <input type="file" name="image" accept="image/*" required={!isEdit} hidden onChange={(e) => { const f = e.target.files?.[0]; if (f) setCoverPreview(URL.createObjectURL(f)); }} />
              {(coverPreview || currentCover)
                ? <img src={coverPreview || currentCover} alt="Cover" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                : <span style={{ position: 'absolute', inset: 0, display: 'grid', placeItems: 'center', color: 'var(--mm-dim)', textAlign: 'center', padding: 16 }}>
                  <span><CloudUpload size={30} /><div style={{ fontWeight: 800, color: '#fff', marginTop: 8 }}>Choose cover</div><div style={{ fontSize: '0.75rem' }}>JPG / PNG, max 5MB</div></span>
                </span>}
            </label>
            {errors.image && <div style={{ color: '#f87171', fontSize: '0.8rem', marginTop: 6 }}>{errors.image}</div>}
            <div style={{ marginTop: 16 }}>
              <label className="mm-label">AUDIO FILE (MP3) {!isEdit && '(REQUIRED)'}</label>
              <input type="file" name="audio" accept=".mp3,audio/mpeg" required={!isEdit} className="mm-file" />
              {isEdit && song?.file_path && <div style={{ fontSize: '0.75rem', color: 'var(--mm-faint)', marginTop: 6 }}>Current: {String(song.file_path).split('/').pop()}</div>}
              {errors.audio && <div style={{ color: '#f87171', fontSize: '0.8rem', marginTop: 6 }}>{errors.audio}</div>}
            </div>
            <div style={{ marginTop: 16 }}>
              <label className="mm-label">ARTIST PHOTO (OPTIONAL)</label>
              <input type="file" name="artist_photo" accept="image/*" className="mm-file" />
            </div>
          </div>

          <div>
            <label className="mm-label">TRACK TITLE</label>
            <div className="mm-field" style={{ marginBottom: 14 }}>
              <input name="title" defaultValue={song?.title ?? ''} required className="mm-input" style={{ paddingLeft: 14, fontSize: '1.15rem', fontWeight: 800 }} placeholder="Enter song title..." />
            </div>
            {errors.title && <div style={{ color: '#f87171', fontSize: '0.8rem', marginBottom: 10 }}>{errors.title}</div>}
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
              <div>
                <label className="mm-label">ARTIST NAME</label>
                <div className="mm-field"><input name="artist" defaultValue={song?.artist ?? ''} required className="mm-input" style={{ paddingLeft: 14 }} placeholder="Artist name" /></div>
                {errors.artist && <div style={{ color: '#f87171', fontSize: '0.8rem', marginBottom: 10 }}>{errors.artist}</div>}
              </div>
              <div>
                <label className="mm-label">GENRE</label>
                <select name="genre" defaultValue={song?.genre ?? ''} required className="mm-select">
                  <option value="" disabled>Select genre...</option>
                  {GENRES.map((g) => <option key={g} value={g}>{g}</option>)}
                </select>
                {errors.genre && <div style={{ color: '#f87171', fontSize: '0.8rem', marginTop: 6 }}>{errors.genre}</div>}
              </div>
            </div>
            <div style={{ marginTop: 2 }}>
              <label className="mm-label">DURATION</label>
              <div className="mm-field"><input name="duration" defaultValue={song?.duration ?? ''} required className="mm-input" style={{ paddingLeft: 14, fontFamily: 'monospace' }} placeholder="03:45" /></div>
              {errors.duration && <div style={{ color: '#f87171', fontSize: '0.8rem', marginBottom: 10 }}>{errors.duration}</div>}
            </div>
            <div style={{ marginTop: 2 }}>
              <label className="mm-label">LYRICS (LRC FORMAT PREFERRED)</label>
              <textarea name="lyrics" defaultValue={song?.lyrics ?? ''} rows={9} className="mm-textarea-code" placeholder={'[00:10.00] First line...\n[00:15.00] Second line...'} />
            </div>
          </div>
        </div>
        <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 12, marginTop: 26 }}>
          <a href="/admin/songs" className="mm-btn-ghost"><ArrowLeft size={15} /> Cancel</a>
          <button type="submit" className="mm-btn-primary"><CloudUpload size={16} /> {isEdit ? 'Save changes' : 'Upload track'}</button>
        </div>
      </form>
    </div>
  );
}

/* ================= USERS ================= */

export function AdminUsers({ users = [], stats = {}, pagination = null, search = '' }) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
  return (
    <div>
      <PageHeader badge="ADMIN" title="User management" subtitle="Control access and administrator roles." />

      <Stats
        items={[
          { value: stats.totalUsers ?? users.length, label: 'REGISTERED USERS', icon: Users, gradient: 'linear-gradient(45deg,#3b82f6,#60a5fa)' },
          { value: stats.adminCount ?? '-', label: 'ADMINISTRATORS', icon: ShieldCheck, gradient: 'linear-gradient(45deg,#f43f5e,#fb7185)' },
          { value: `+${stats.newToday ?? 0}`, label: 'NEW TODAY', icon: UserPlus, gradient: 'linear-gradient(45deg,#10b981,#3b82f6)' },
        ]}
      />

      <form action="/admin/users" method="GET" className="mm-enter" style={{ marginBottom: 18 }}>
        <div className="mm-admin-search">
          <SearchIcon size={17} style={{ color: 'var(--mm-faint)', flexShrink: 0 }} />
          <input name="search" defaultValue={search} placeholder="Search name or email..." />
          {search && <a href="/admin/users" title="Clear search" style={{ color: 'var(--mm-faint)', display: 'grid', placeItems: 'center' }}><CircleX size={17} /></a>}
        </div>
      </form>

      {users.length === 0 ? (
        <EmptyState icon={Users} title="No users found" message={search ? 'Try different keywords.' : 'No registered users yet.'} />
      ) : (
        <div className="mm-glass mm-enter" style={{ padding: 8, overflowX: 'auto' }}>
          <table className="mm-admin-table" style={{ minWidth: 760 }}>
            <thead><tr><th>AVATAR</th><th>USER DETAILS</th><th>EMAIL</th><th>ROLE</th><th>JOINED</th><th style={{ textAlign: 'right' }}>ACTIONS</th></tr></thead>
            <tbody>
              {users.map((u) => (
                <tr key={u.id}>
                  <td><span className="mm-avatar" style={{ width: 42, height: 42 }}>{(u.name || 'U').slice(0, 1).toUpperCase()}</span></td>
                  <td style={{ fontWeight: 800 }}>{u.name}</td>
                  <td style={{ color: 'var(--mm-dim)', fontFamily: 'monospace', fontSize: '0.82rem' }}>{u.email}</td>
                  <td>
                    <span className={`mm-badge-role ${u.role === 'admin' ? 'mm-role-admin' : 'mm-role-user'}`}>
                      <ShieldCheck size={12} /> {u.role.toUpperCase()}
                    </span>
                  </td>
                  <td style={{ color: 'var(--mm-dim)', fontSize: '0.82rem', whiteSpace: 'nowrap' }}>
                    <span style={{ display: 'inline-flex', alignItems: 'center', gap: 6 }}><CalendarDays size={13} /> {u.joined || ''}</span>
                  </td>
                  <td>
                    <div style={{ display: 'flex', gap: 8, justifyContent: 'flex-end' }}>
                      <form action={`/admin/users/${u.id}/role`} method="POST" onSubmit={(e) => { if (!confirm(`Change ${u.name} to ${u.role === 'admin' ? 'user' : 'admin'}?`)) e.preventDefault(); }}>
                        <input type="hidden" name="_token" value={csrf} />
                        <input type="hidden" name="_method" value="PATCH" />
                        <button className="mm-btn-ghost" style={{ padding: '8px 16px', fontSize: '0.78rem', whiteSpace: 'nowrap' }} type="submit" title="Toggle role">
                          Make {u.role === 'admin' ? 'user' : 'admin'}
                        </button>
                      </form>
                      <form action={`/admin/users/${u.id}`} method="POST" onSubmit={(e) => { if (!confirm(`Delete user ${u.name}?`)) e.preventDefault(); }}>
                        <input type="hidden" name="_token" value={csrf} />
                        <input type="hidden" name="_method" value="DELETE" />
                        <button className="mm-icon-btn" style={{ width: 36, height: 36, color: '#f87171', borderColor: 'rgba(239,68,68,0.35)' }} type="submit" title="Delete user"><Trash2 size={14} /></button>
                      </form>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
      <Pagination pagination={pagination} />
      <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginTop: 22 }}>
        <a className="mm-btn-ghost" href="/admin/songs"><ListMusic size={15} /> Back to songs</a>
      </div>
    </div>
  );
}

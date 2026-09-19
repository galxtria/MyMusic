import { useState } from 'react';
import { CalendarDays, Clock, Flame, Heart, ImagePlus, ListMusic, Mic, Trash2, TrendingUp, UserRound } from 'lucide-react';
import { normalizeSong } from '../lib/api';
import { usePlayer } from '../lib/player';
import { EmptyState, PageHeader, SongCard } from '../components/ui';

export default function Profile({ user, stats = {}, topSongs = [], topSongPlays = {}, topArtists = [], recentRows = [], recentSongs = [], followedArtists = [] }) {
  const p = usePlayer();
  const [preview, setPreview] = useState('');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
  const avatarUrl = user?.avatar_url || '';
  const tops = topSongs.map(normalizeSong);
  const recents = recentSongs.map ? recentSongs.map(normalizeSong) : [];
  const recentById = Object.fromEntries(recents.map((s) => [String(s.id), s]));

  return (
    <div>
      <PageHeader badge="PROFILE" title={user?.name || 'Your profile'} subtitle={`${user?.email || ''} • joined ${user?.created_at || ''}`} />

      <div className="mm-enter" style={{ display: 'flex', gap: 22, flexWrap: 'wrap', marginBottom: 22 }}>
        <div className="mm-glass" style={{ padding: 22, display: 'flex', gap: 20, alignItems: 'center', flex: 1, minWidth: 300 }}>
          {avatarUrl
            ? <img src={avatarUrl} alt="" style={{ width: 92, height: 92, borderRadius: '50%', objectFit: 'cover', border: '1px solid var(--mm-line)' }} onError={(e) => { e.currentTarget.style.display = 'none'; }} />
            : <span className="mm-avatar" style={{ width: 92, height: 92, fontSize: '2rem' }}>{(user?.name || 'U').slice(0, 1).toUpperCase()}</span>}
          <div>
            <div style={{ fontWeight: 800, fontSize: '1.3rem' }}>{user?.name}</div>
            <div style={{ color: 'var(--mm-dim)', fontSize: '0.85rem' }}>{stats.totalPlays ?? 0} plays • ~{stats.minutes ?? 0} min • {stats.likesCount ?? 0} liked • {stats.playlistCount ?? 0} playlists</div>
            <div style={{ display: 'flex', gap: 8, marginTop: 10, flexWrap: 'wrap' }}>
              {(followedArtists || []).slice(0, 5).map((a) => (
                <a key={a} href={`/artist/${encodeURIComponent(a)}`} className="mm-chip mm-chip-sm">{a}</a>
              ))}
              {(!followedArtists || followedArtists.length === 0) && <span style={{ color: 'var(--mm-faint)', fontSize: '0.8rem' }}>No followed artists yet</span>}
            </div>
          </div>
        </div>

        <form action="/profile" method="POST" encType="multipart/form-data" className="mm-glass" style={{ padding: 22, flex: 1, minWidth: 300 }}>
          <input type="hidden" name="_token" value={csrf} />
          <input type="hidden" name="_method" value="PUT" />
          <h3 style={{ margin: '0 0 12px', fontSize: '1rem', display: 'flex', alignItems: 'center', gap: 8 }}><UserRound size={16} /> Edit profile</h3>
          <label className="mm-label">DISPLAY NAME</label>
          <input name="name" defaultValue={user?.name} required className="mm-input" style={{ paddingLeft: 14, marginBottom: 12 }} />
          <label className="mm-label">AVATAR (JPG/PNG, max 2MB)</label>
          <div style={{ display: 'flex', gap: 12, alignItems: 'center' }}>
            <label className="mm-btn-ghost" style={{ cursor: 'pointer' }}>
              <ImagePlus size={15} /> Choose photo
              <input type="file" name="avatar" accept="image/*" hidden onChange={(e) => { const f = e.target.files?.[0]; if (f) setPreview(URL.createObjectURL(f)); }} />
            </label>
            {(preview || avatarUrl) && <img src={preview || avatarUrl} alt="" style={{ width: 44, height: 44, borderRadius: '50%', objectFit: 'cover' }} />}
            {avatarUrl && <label style={{ fontSize: '0.78rem', color: 'var(--mm-dim)', display: 'flex', gap: 6, alignItems: 'center' }}><input type="checkbox" name="remove_avatar" value="1" /> Remove</label>}
            <button type="submit" className="mm-btn-primary" style={{ marginLeft: 'auto', padding: '9px 20px' }}>Save</button>
          </div>
        </form>
      </div>

      {tops.length > 0 && (
        <>
          <div className="mm-section"><Flame size={18} style={{ color: 'var(--mm-accent)' }} /><span>Your top tracks (30 days)</span></div>
          <div className="rt-row mm-enter">
            {tops.map((s, i) => (
              <div key={s.id} className="rt-pop-wrap">
                <span className="rt-pop-rank">#{i + 1}</span>
                {topSongPlays?.[s.id] != null && <span className="rt-pop-plays">{topSongPlays[s.id]} plays</span>}
                <SongCard song={s} onPlay={() => p.loadQueue(tops, i)} />
              </div>
            ))}
          </div>
        </>
      )}

      {topArtists.length > 0 && (
        <>
          <div className="mm-section"><Mic size={18} style={{ color: 'var(--mm-teal)' }} /><span>Your top artists</span></div>
          <div style={{ display: 'flex', gap: 10, flexWrap: 'wrap', marginBottom: 8 }}>
            {topArtists.map((a) => (
              <a key={a.artist} href={`/artist/${encodeURIComponent(a.artist)}`} className="mm-glass" style={{ textDecoration: 'none', padding: '12px 18px', display: 'flex', gap: 10, alignItems: 'center' }}>
                <TrendingUp size={15} style={{ color: 'var(--mm-accent)' }} />
                <span style={{ fontWeight: 800 }}>{a.artist}</span>
                <span style={{ color: 'var(--mm-dim)', fontSize: '0.78rem' }}>{a.plays} plays</span>
              </a>
            ))}
          </div>
        </>
      )}

      <div className="mm-section">
        <Clock size={18} style={{ color: 'var(--mm-dim)' }} /><span>Recently played</span>
        {recentRows.length > 0 && (
          <form action="/profile/history" method="POST" style={{ marginLeft: 'auto' }} onSubmit={(e) => { if (!confirm('Clear all listening history?')) e.preventDefault(); }}>
            <input type="hidden" name="_token" value={csrf} />
            <input type="hidden" name="_method" value="DELETE" />
            <button type="submit" className="mm-btn-ghost" style={{ padding: '7px 14px', fontSize: '0.75rem', color: '#f87171' }}><Trash2 size={13} /> Clear history</button>
          </form>
        )}
      </div>
      {recentRows.length === 0
        ? <EmptyState icon={Clock} title="No history yet" message="Play some music and it will show up here." />
        : (
          <div className="mm-glass" style={{ padding: 8 }}>
            {recentRows.map((r, i) => {
              const s = recentById[String(r.song_id)];
              if (!s) return null;
              return (
                <div key={i} className="mm-table-row" onClick={() => p.loadQueue(recents.filter((x) => recentById[String(x.id)]), recents.findIndex((x) => String(x.id) === String(r.song_id)))}>
                  <img src={s.cover} alt="" style={{ width: 42, height: 42, borderRadius: 9, objectFit: 'cover' }} onError={(e) => { e.currentTarget.src = '/images/default-cover.png'; }} />
                  <div style={{ fontWeight: 700 }}>{s.title}</div>
                  <div className="mm-hide-mobile" style={{ color: 'var(--mm-dim)' }}>{s.artist}</div>
                  <div className="mm-hide-mobile" />
                  <div style={{ textAlign: 'right', color: 'var(--mm-faint)', fontSize: '0.78rem', display: 'flex', alignItems: 'center', gap: 6, justifyContent: 'flex-end' }}>
                    <CalendarDays size={13} /> {r.played_at}
                  </div>
                </div>
              );
            })}
          </div>
        )}
      <div style={{ display: 'flex', gap: 10, marginTop: 18 }}>
        <a href="/favorites" className="mm-btn-ghost"><Heart size={15} /> Liked songs ({stats.likesCount ?? 0})</a>
        <a href="/library" className="mm-btn-ghost"><ListMusic size={15} /> Library ({stats.playlistCount ?? 0} playlists)</a>
      </div>
    </div>
  );
}

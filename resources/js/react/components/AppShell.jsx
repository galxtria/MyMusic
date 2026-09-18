import { useEffect, useRef, useState } from 'react';
import {
  ChevronDown, ChevronRight, Disc3, Download, FolderPlus, Heart, House, ListMusic, LogOut, Menu,
  Mic, Pause, Play, Repeat, Settings, Shuffle, SkipBack, SkipForward, Volume2, X,
} from 'lucide-react';
import { addSongToPlaylist, formatTime, toggleFavorite } from '../lib/api';
import { clearPersistedPlayerState, usePlayer } from '../lib/player';

function doLogout(formId) {
  // Tandai logout agar persist tidak menulis balik, hentikan audio,
  // dan buang antrean tersimpan supaya user berikutnya mulai bersih.
  try {
    window.__mm_logging_out = true;
    clearPersistedPlayerState();
    document.querySelectorAll('audio').forEach((a) => { try { a.pause(); a.removeAttribute('src'); a.load?.(); } catch {} });
  } catch {}
  document.getElementById(formId)?.submit();
}

function NavItem({ href, active, icon: Icon, label }) {
  return (
    <a href={href} className={`mm-nav-link ${active ? 'active' : ''}`}>
      <Icon size={19} />
      <span style={{ flex: 1 }}>{label}</span>
    </a>
  );
}

function coverOf(pl) {
  if (!pl?.cover_path) return '/images/default_playlist.jpg';
  return `/${String(pl.cover_path).replace(/^\//, '')}`;
}

function Sidebar({ page, isAdmin, playlists, open, onClose }) {
  const [plOpen, setPlOpen] = useState(true);
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

  return (
    <>
      {open && <div onClick={onClose} style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.6)', zIndex: 39 }} />}
      <aside className={`mm-sidebar ${open ? 'open' : ''}`}>
        <a href="/home" className="mm-logo">
          <span className="mm-logo-badge"><Disc3 size={20} /></span>
          <span className="mm-logo-name">MyMusic</span>
        </a>

        <NavItem href="/home" active={page === 'home'} icon={House} label="Home" />
        <NavItem href="/search" active={page === 'search'} icon={LayoutGridIcon} label="Categories" />
        <NavItem href="/library" active={page === 'library' || page === 'create'} icon={ListMusic} label="Your Library" />

        <button className="mm-nav-link" style={{ width: '100%', background: 'none', border: 0, cursor: 'pointer' }} onClick={() => setPlOpen((v) => !v)}>
          <FolderPlus size={19} />
          <span style={{ flex: 1, textAlign: 'left' }}>Playlists</span>
          <ChevronDown size={15} style={{ transform: plOpen ? undefined : 'rotate(-90deg)', transition: 'transform .2s', color: 'var(--mm-faint)' }} />
        </button>
        {plOpen && (
          <div style={{ display: 'flex', flexDirection: 'column', gap: 2, marginTop: 2 }}>
            <a href="/favorites" className={`mm-nav-link ${page === 'favorites' ? 'active' : ''}`} style={{ paddingLeft: 14 }}>
              <Heart size={16} />
              <span style={{ flex: 1 }}>Liked Songs</span>
            </a>
            {(playlists ?? []).slice(0, 8).map((pl) => (
              <a key={pl.id} href={`/playlist/${pl.id}`} className="mm-nav-link" style={{ paddingLeft: 14 }}>
                <img src={coverOf(pl)} alt="" className="mm-playlist-thumb" onError={(e) => { e.currentTarget.src = '/images/default_playlist.jpg'; }} />
                <span style={{ flex: 1, whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>{pl.name}</span>
              </a>
            ))}
            <a href="/create" className="mm-nav-link" style={{ paddingLeft: 14, color: 'var(--mm-faint)' }}>
              <ChevronRight size={15} />
              <span style={{ flex: 1 }}>New playlist</span>
            </a>
          </div>
        )}

        {isAdmin && (
          <>
            <div className="mm-nav-label">ADMIN</div>
            <NavItem href="/admin/songs" active={page === 'admin'} icon={Settings} label="Manage Songs" />
            <NavItem href="/admin/tools/import" active={page === 'admin-import'} icon={Download} label="Import Music" />
          </>
        )}

        <form id="sidebar-logout-form" action="/logout" method="POST" style={{ display: 'none' }}>
          <input type="hidden" name="_token" value={csrf} />
        </form>
        <button
          className="mm-logout-btn"
          onClick={() => doLogout('sidebar-logout-form')}
        >
          <LogOut size={19} /> Logout
        </button>
      </aside>
    </>
  );
}

function LayoutGridIcon(props) {
  return (
    <svg width={props.size ?? 19} height={props.size ?? 19} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
      <rect width="7" height="7" x="3" y="3" rx="1" />
      <rect width="7" height="7" x="14" y="3" rx="1" />
      <rect width="7" height="7" x="14" y="14" rx="1" />
      <rect width="7" height="7" x="3" y="14" rx="1" />
    </svg>
  );
}

function TopBar({ user, isAdmin, page, onMenu }) {
  const [open, setOpen] = useState(false);
  // Profile hanya muncul di dashboard:
  // - user biasa -> hanya di dashboard user (page === 'home')
  // - admin -> hanya di dashboard admin (page diawali 'admin')
  const showProfile = user
    ? (isAdmin ? String(page || '').startsWith('admin') : page === 'home')
    : false;
  return (
    <div className="mm-topbar">
      <button className="mm-icon-btn" onClick={onMenu} id="mm-menu-btn" title="Open menu">
        <Menu size={18} />
      </button>
      <div className="mm-user-cluster">
        {user ? (
          showProfile ? (
            <div style={{ position: 'relative' }}>
              <button className="mm-user-chip" onClick={() => setOpen((v) => !v)}>
                <span className="mm-avatar">{(user.name || 'U').slice(0, 1).toUpperCase()}</span>
                <span style={{ textAlign: 'left', lineHeight: 1.25 }}>
                  <span style={{ display: 'block' }}>{user.name}</span>
                  <span className="mm-premium-tag">Premium</span>
                </span>
              </button>
              {open && (
                <div className="mm-modal" style={{ position: 'absolute', right: 0, top: 52, minWidth: 220, padding: 8, zIndex: 80 }}>
                  {isAdmin && <a href="/admin/songs" className="mm-nav-link"><Settings size={16} /> Admin Panel</a>}
                  <a href="/library" className="mm-nav-link"><ListMusic size={16} /> Your Library</a>
                  <a
                    href="/logout"
                    className="mm-nav-link"
                    style={{ color: '#fb7185' }}
                    onClick={(e) => { e.preventDefault(); doLogout('logout-form'); }}
                  >
                    <LogOut size={16} /> Log out
                  </a>
                </div>
              )}
            </div>
          ) : null
        ) : (
          <>
            <a href="/login" style={{ color: 'var(--mm-dim)', fontWeight: 700, textDecoration: 'none', fontSize: '0.88rem' }}>Log in</a>
            <a href="/register" className="mm-btn-primary" style={{ padding: '10px 22px' }}>Sign up</a>
          </>
        )}
      </div>
    </div>
  );
}

function PlayerBar() {
  const p = usePlayer();
  const { current } = p;

  const audioEl = (
    <audio
      ref={p.audioRef}
      preload="metadata"
      onTimeUpdate={(e) => {
        const el = e.currentTarget;
        if (isFinite(el.duration) && el.duration > 0) {
          p.setProgress((el.currentTime / el.duration) * 1000);
          p.setCurrentTime(el.currentTime);
        }
      }}
        onLoadedMetadata={(e) => {
          const el = e.currentTarget;
          if (isFinite(el.duration)) p.setDuration(el.duration);
          const rt = p.consumeResumeTime ? p.consumeResumeTime() : null;
          if (rt && isFinite(el.duration) && rt < el.duration) {
            try { el.currentTime = rt; } catch {}
          }
        }}
      onEnded={() => { if (p.repeat) { const el = p.audioRef.current; if (el) { el.currentTime = 0; el.play().catch(() => {}); } } else p.next(); }}
      onPlay={() => p.setIsPlaying(true)}
      onPause={() => p.setIsPlaying(false)}
      style={{ display: 'none' }}
    />
  );

  // The bar root element NEVER unmounts / changes type — otherwise React would
  // destroy the <audio> DOM node and stop playback. Hiding is done with CSS only.
  const hidden = !current || p.lyricsOpen;

  return (
    <div className="mm-player" style={hidden ? { display: 'none' } : undefined}>
      {audioEl}

      <div className="mm-pb-left">
        {current?.cover
          ? <img src={current.cover} className="mm-player-cover" alt="" onError={(e) => { e.currentTarget.style.display = 'none'; }} />
          : <div className="mm-player-cover" style={{ display: 'grid', placeItems: 'center', background: 'var(--mm-panel-2)' }}><Disc3 size={22} style={{ color: 'var(--mm-faint)' }} /></div>}
        <div style={{ minWidth: 0, flex: 1 }}>
          <div style={{ fontWeight: 800, fontSize: '0.86rem', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>
            {current ? current.title : 'Not playing'}
          </div>
          <div style={{ color: 'var(--mm-dim)', fontSize: '0.76rem', fontWeight: 500 }}>
            {current ? current.artist : 'Pick a song to start'}
          </div>
        </div>
        <span className="mm-only-mobile">{current && <LikeButton songId={current.id} size={17} />}</span>
        <button className="mm-icon-btn mm-only-mobile-btn" onClick={() => current && p.setLyricsOpen(true)} title="Lyrics" style={{ width: 34, height: 34 }}><Mic size={15} /></button>
        <button className="mm-play-fab mm-only-mobile-btn" onClick={p.toggle} title="Play / Pause" disabled={!current}>
          {p.isPlaying ? <Pause size={17} /> : <Play size={17} style={{ marginLeft: 2 }} />}
        </button>
      </div>

      <div className="mm-pb-center">
        <div className="mm-pb-transport">
          <button onClick={p.prev} title="Previous" style={ghostBtn}><SkipBack size={17} /></button>
          <button className="mm-play-fab" onClick={p.toggle} title="Play / Pause" disabled={!current} style={!current ? { opacity: 0.45 } : undefined}>
            {p.isPlaying ? <Pause size={18} /> : <Play size={18} style={{ marginLeft: 2 }} />}
          </button>
          <button onClick={p.next} title="Next" style={ghostBtn}><SkipForward size={17} /></button>
        </div>
        <div className="mm-pb-progress">
          <span style={timeStyle}>{formatTime(p.currentTime)}</span>
          <input
            type="range" min={0} max={1000} value={Math.round(p.progress)} className="mm-range" style={{ flex: 1 }}
            onChange={(e) => {
              const el = p.audioRef.current;
              const v = Number(e.target.value);
              p.setProgress(v);
              if (el && isFinite(el.duration)) el.currentTime = (v / 1000) * el.duration;
            }}
          />
          <span style={timeStyle}>{formatTime(p.duration)}</span>
        </div>
      </div>

      <div className="mm-pb-right mm-hide-mobile">
        {current && <LikeButton songId={current.id} size={17} />}
        <button onClick={() => p.setShuffle((v) => !v)} title="Shuffle" style={ghostBtn}>
          <Shuffle size={16} color={p.shuffle ? '#fff' : undefined} opacity={p.shuffle ? 1 : 0.55} />
        </button>
        <button onClick={() => p.setRepeat((v) => !v)} title="Repeat" style={ghostBtn}>
          <Repeat size={16} color={p.repeat ? '#fff' : undefined} opacity={p.repeat ? 1 : 0.55} />
        </button>
        <button onClick={() => current && p.setLyricsOpen(true)} title="Lyrics" style={ghostBtn}><Mic size={16} /></button>
        <Volume2 size={16} style={{ color: 'var(--mm-faint)' }} />
        <input type="range" min={0} max={1} step={0.01} value={p.volume} className="mm-range" style={{ width: 72 }} onChange={(e) => p.setVolume(Number(e.target.value))} />
      </div>
    </div>
  );
}

const ghostBtn = { background: 'none', border: 0, color: 'var(--mm-dim)', cursor: 'pointer', padding: 4, display: 'grid', placeItems: 'center' };
const timeStyle = { fontSize: '0.68rem', color: 'var(--mm-faint)', fontWeight: 700, fontVariantNumeric: 'tabular-nums' };

export function LikeButton({ songId, size = 17 }) {
  const p = usePlayer();
  const liked = p.likedIds.has(String(songId));
  return (
    <button
      onClick={async (e) => {
        e.stopPropagation();
        try {
          const data = await toggleFavorite(songId);
          const isLiked = data.status === 'liked';
          p.markLiked(songId, isLiked);
          p.showToast(isLiked ? 'Saved to your library' : 'Removed from your library');
        } catch {
          p.showToast('Could not update favorite');
        }
      }}
      title="Save to library"
      style={{ background: 'none', border: 0, cursor: 'pointer', padding: 4, display: 'grid', placeItems: 'center' }}
    >
      <Heart size={size} color={liked ? '#3b82f6' : '#8a8a94'} fill={liked ? '#3b82f6' : 'none'} />
    </button>
  );
}

function LyricsOverlay() {
  const p = usePlayer();
  const bodyRef = useRef(null);
  const activeIdx = p.lyrics.findLastIndex((l) => p.currentTime >= l.t);
  const remaining = Math.max(0, (p.duration || 0) - (p.currentTime || 0));

  // ESC closes, background page scroll is locked while open.
  useEffect(() => {
    if (!p.lyricsOpen) return;
    const onKey = (e) => { if (e.key === 'Escape') p.setLyricsOpen(false); };
    document.addEventListener('keydown', onKey);
    const prev = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
    return () => {
      document.removeEventListener('keydown', onKey);
      document.body.style.overflow = prev;
    };
  }, [p.lyricsOpen]);

  const seek = (e) => {
    const el = p.audioRef.current;
    const v = Number(e.target.value);
    p.setProgress(v);
    if (el && isFinite(el.duration)) el.currentTime = (v / 1000) * el.duration;
  };

  return (
    <div className={`mm-lyrics ${p.lyricsOpen ? 'show' : ''}`}>
      <div className="mm-lyrics-bg" style={p.current?.cover ? { backgroundImage: `url("${p.current.cover}")` } : undefined} />
      <div className="mm-lyrics-shade" />
      <button className="mm-lyrics-close" onClick={() => p.setLyricsOpen(false)} title="Close lyrics">
        <X size={24} />
      </button>
      <div className="mm-lyrics-inner">
        <div className="mm-lp-left">
          {p.current?.cover && (
            <img src={p.current.cover} alt="" className="mm-lp-cover" onError={(e) => { e.currentTarget.style.display = 'none'; }} />
          )}
          <div className="mm-lp-head">
            <div className="mm-lp-title">{p.current?.title || 'No track'}</div>
            <div className="mm-lp-sub">{p.current ? `${p.current.artist}${p.current.genre ? ` — ${p.current.genre}` : ''}` : ''}</div>
          </div>
          <div className="mm-lp-times">
            <span>{formatTime(p.currentTime)}</span>
            <span>-{formatTime(remaining)}</span>
          </div>
          <input
            type="range" min={0} max={1000} value={Math.round(p.progress)}
            className="mm-range mm-lp-progress" onChange={seek} title="Seek"
          />
          <div className="mm-lp-transport">
            <button className={`mm-lp-btn ${p.shuffle ? 'on' : 'dim'}`} onClick={() => p.setShuffle((v) => !v)} title="Shuffle">
              <Shuffle size={17} />
            </button>
            <button className="mm-lp-btn" onClick={p.prev} title="Previous"><SkipBack size={26} /></button>
            <button className="mm-lp-btn" onClick={p.toggle} title="Play / Pause" disabled={!p.current}>
              {p.isPlaying ? <Pause size={34} /> : <Play size={34} style={{ marginLeft: 3 }} />}
            </button>
            <button className="mm-lp-btn" onClick={p.next} title="Next"><SkipForward size={26} /></button>
            <button className={`mm-lp-btn ${p.repeat ? 'on' : 'dim'}`} onClick={() => p.setRepeat((v) => !v)} title="Repeat">
              <Repeat size={17} />
            </button>
          </div>
          <div className="mm-lp-vol">
            <Volume2 size={16} />
            <input
              type="range" min={0} max={1} step={0.01} value={p.volume}
              className="mm-range" style={{ flex: 1 }} onChange={(e) => p.setVolume(Number(e.target.value))} title="Volume"
            />
          </div>
        </div>
        <ScrollLyrics bodyRef={bodyRef} activeIdx={activeIdx} />
      </div>
    </div>
  );
}

function ScrollLyrics({ bodyRef, activeIdx }) {
  const p = usePlayer();
  useAutoScroll(bodyRef, activeIdx, p.lyricsOpen);
  return (
    <div ref={bodyRef} className="mm-lyrics-body">
      {p.lyricsLoading && <p className="mm-lyrics-note">Finding lyrics...</p>}
      {!p.lyricsLoading && p.lyrics.length === 0 && <p className="mm-lyrics-note">Lyrics are not available for this track yet.</p>}
      {p.lyrics.map((l, i) => {
        const state = i === activeIdx ? 'active' : i < activeIdx ? 'past' : 'upcoming';
        return (
          <div
            key={i} data-lyric={i}
            className={`mm-lyric-line ${state}`}
            onClick={() => { const el = p.audioRef.current; if (el) el.currentTime = l.t; }}
          >
            {l.txt}
          </div>
        );
      })}
    </div>
  );
}

function useAutoScroll(bodyRef, activeIdx, open) {
  useEffect(() => {
    if (!open) return;
    // Manual scroll math on the lyrics container ONLY.
    // (scrollIntoView would also scroll the page behind the overlay.)
    const body = bodyRef.current;
    if (!body) return;
    const el = body.querySelector(`[data-lyric="${activeIdx}"]`);
    if (!el) return;
    const target = el.offsetTop - body.clientHeight / 2 + el.clientHeight / 2;
    body.scrollTo({ top: target, behavior: 'smooth' });
  }, [activeIdx, open]);
}

function PlaylistModal({ playlists }) {
  const p = usePlayer();
  if (!p.playlistModalSong) return null;
  return (
    <div className="mm-modal-backdrop" onClick={() => p.setPlaylistModalSong(null)}>
      <div className="mm-modal" onClick={(e) => e.stopPropagation()}>
        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: 14 }}>
          <h3 style={{ margin: 0, fontSize: '1.05rem' }}>Save to playlist</h3>
          <button className="mm-icon-btn" style={{ width: 34, height: 34 }} onClick={() => p.setPlaylistModalSong(null)}><X size={15} /></button>
        </div>
        <div style={{ display: 'grid', gap: 8 }}>
          {(playlists ?? []).map((pl) => (
            <button
              key={pl.id}
              className="mm-btn-ghost"
              style={{ justifyContent: 'flex-start' }}
              onClick={async () => {
                try {
                  const data = await addSongToPlaylist(pl.id, p.playlistModalSong);
                  p.showToast(data.message || 'Added to playlist');
                  p.setPlaylistModalSong(null);
                } catch {
                  p.showToast('Could not add to playlist');
                }
              }}
            >
              <img src={coverOf(pl)} alt="" style={{ width: 32, height: 32, borderRadius: 8, objectFit: 'cover', flexShrink: 0 }} />
              {pl.name}
            </button>
          ))}
          {(playlists ?? []).length === 0 && (
            <a href="/create" className="mm-btn-primary" style={{ justifyContent: 'center' }}><FolderPlus size={16} /> Create a playlist first</a>
          )}
        </div>
      </div>
    </div>
  );
}

export default function AppShell({ page, user, isAdmin, playlists, children }) {
  const p = usePlayer();
  const [sideOpen, setSideOpen] = useState(false);

  return (
    <div className="mm-app">
      <form id="logout-form" action="/logout" method="POST" style={{ display: 'none' }}>
        <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''} />
      </form>
      <div className="mm-body">
        <Sidebar page={page} isAdmin={isAdmin} playlists={playlists} open={sideOpen} onClose={() => setSideOpen(false)} />
        <div className="mm-col">
          <main className="mm-main">
            <TopBar user={user} isAdmin={isAdmin} page={page} onMenu={() => setSideOpen(true)} />
            {children}
          </main>
          <PlayerBar />
        </div>
      </div>
      <LyricsOverlay />
      <PlaylistModal playlists={playlists} />
      {p.toast && (
        <div className="mm-toast">
          <span style={{ width: 30, height: 30, borderRadius: 9, display: 'grid', placeItems: 'center', background: 'var(--mm-accent)' }}>
            <Disc3 size={15} color="#fff" />
          </span>
          {p.toast}
        </div>
      )}
    </div>
  );
}

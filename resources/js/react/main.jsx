import React from 'react';
import { createRoot } from 'react-dom/client';
import '../../css/app.css';
import './theme.css';
import { PlayerProvider } from './lib/player';
import AppShell from './components/AppShell';
import Welcome from './pages/Welcome';
import Home from './pages/Home';
import Search from './pages/Search';
import Library from './pages/Library';
import Favorites from './pages/Favorites';
import Artist from './pages/Artist';
import PlaylistDetail from './pages/PlaylistDetail';
import CreatePlaylist, { EditPlaylist } from './pages/PlaylistForm';
import { Login, Register } from './pages/Auth';
import { AdminSongs, AdminSongForm, AdminUsers } from './pages/Admin';
import AdminImport from './pages/AdminImport';

const standalone = new Set(['welcome', 'login', 'register']);

function mount() {
  const rootEl = document.getElementById('react-root');
  if (!rootEl) return;

  let page = rootEl.dataset.page || 'home';
  let props = {};
  try {
    props = JSON.parse(rootEl.dataset.props || '{}');
  } catch {
    props = {};
  }

  const renderPage = () => {
    switch (page) {
      case 'welcome': return <Welcome {...props} />;
      case 'login': return <Login {...props} />;
      case 'register': return <Register {...props} />;
      case 'home': return <Home {...props} />;
      case 'search': return <Search {...props} />;
      case 'library': return <Library {...props} />;
      case 'favorites': return <Favorites {...props} />;
      case 'artist': return <Artist {...props} />;
      case 'playlist.show': return <PlaylistDetail {...props} />;
      case 'create': return <CreatePlaylist {...props} />;
      case 'playlist.edit': return <EditPlaylist {...props} />;
      case 'admin.songs.index': return <AdminSongs {...props} />;
      case 'admin.songs.create': return <AdminSongForm {...props} />;
      case 'admin.songs.edit': return <AdminSongForm {...props} />;
      case 'admin.users.index': return <AdminUsers {...props} />;
      case 'admin.tools.import': return <AdminImport {...props} />;
      default: return <Home {...props} />;
    }
  };

  const app = standalone.has(page) ? (
    <PlayerProvider initialFavorites={props.favoriteIds || []}>{renderPage()}</PlayerProvider>
  ) : (
    <PlayerProvider initialFavorites={props.favoriteIds || []}>
      <AppShell page={props.shellPage || page} user={props.user} isAdmin={props.isAdmin} playlists={props.playlists}>
        {renderPage()}
      </AppShell>
    </PlayerProvider>
  );

  createRoot(rootEl).render(<React.StrictMode>{app}</React.StrictMode>);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', mount);
} else {
  mount();
}

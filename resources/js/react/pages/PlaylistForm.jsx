import { useState } from 'react';
import { ArrowLeft, Check, ImagePlus } from 'lucide-react';
import { PageHeader } from '../components/ui';

export default function CreatePlaylist({ errors = {} }) {
  const [preview, setPreview] = useState('');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

  return (
    <div style={{ minHeight: '70vh', display: 'grid', placeItems: 'center' }}>
      <div style={{ width: '100%', maxWidth: 880 }}>
        <PageHeader badge="STUDIO" title="Create playlist" subtitle="Give it a name, a cover, and start collecting." />
        <form action="/playlist/store" method="POST" encType="multipart/form-data" className="mm-glass mm-enter" style={{ padding: 36, borderRadius: 28, display: 'flex', gap: 34, flexWrap: 'wrap' }}>
          <input type="hidden" name="_token" value={csrf} />
          <label style={{ width: 260, height: 260, borderRadius: 20, border: '2px dashed var(--mm-line-2)', display: 'grid', placeItems: 'center', cursor: 'pointer', overflow: 'hidden', position: 'relative', background: 'rgba(0,0,0,0.3)', flexShrink: 0 }}>
            <input type="file" name="cover" accept="image/*" hidden onChange={(e) => { const f = e.target.files?.[0]; if (f) setPreview(URL.createObjectURL(f)); }} />
            {preview
              ? <img src={preview} alt="Cover" style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', objectFit: 'cover' }} />
              : (
                <span style={{ textAlign: 'center', color: 'var(--mm-dim)' }}>
                  <ImagePlus size={36} style={{ marginBottom: 10 }} />
                  <div style={{ fontWeight: 800, color: '#fff' }}>Add cover</div>
                  <div style={{ fontSize: '0.78rem' }}>Min 300 x 300px</div>
                </span>
              )}
          </label>
          <div style={{ flex: 1, minWidth: 280 }}>
            <label className="mm-label">PLAYLIST NAME</label>
            <input name="name" required placeholder="Untitled playlist" style={{ width: '100%', background: 'transparent', border: 0, borderBottom: '2px solid var(--mm-line)', color: '#fff', fontSize: '2rem', fontWeight: 800, padding: '8px 0 14px', outline: 'none', marginBottom: 8 }} />
            {errors.name && <div style={{ color: '#f87171', fontSize: '0.8rem', marginBottom: 12 }}>{errors.name}</div>}
            <label className="mm-label" style={{ marginTop: 18 }}>DESCRIPTION (OPTIONAL)</label>
            <textarea name="description" rows={3} placeholder="Give your playlist a cool description..." className="mm-input" style={{ padding: 14 }} />
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: 26 }}>
              <a href="/library" className="mm-btn-ghost"><ArrowLeft size={15} /> Back to library</a>
              <button type="submit" className="mm-btn-primary"><Check size={16} /> Confirm and save</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  );
}

export function EditPlaylist({ playlist, errors = {} }) {
  const [preview, setPreview] = useState('');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
  const cover = playlist.cover_path ? `/${String(playlist.cover_path).replace(/^\//, '')}` : '/images/default_playlist.jpg';

  return (
    <div style={{ minHeight: '70vh', display: 'grid', placeItems: 'center' }}>
      <div style={{ width: '100%', maxWidth: 880 }}>
        <PageHeader badge="STUDIO" title="Edit playlist" subtitle={playlist.name} />
        <form action={`/playlists/${playlist.id}`} method="POST" encType="multipart/form-data" className="mm-glass mm-enter" style={{ padding: 36, borderRadius: 28, display: 'flex', gap: 34, flexWrap: 'wrap' }}>
          <input type="hidden" name="_token" value={csrf} />
          <input type="hidden" name="_method" value="PUT" />
          <label style={{ width: 260, height: 260, borderRadius: 20, overflow: 'hidden', position: 'relative', cursor: 'pointer', border: '1px solid var(--mm-line-2)', flexShrink: 0 }}>
            <input type="file" name="cover" accept="image/*" hidden onChange={(e) => { const f = e.target.files?.[0]; if (f) setPreview(URL.createObjectURL(f)); }} />
            <img src={preview || cover} alt="Cover" style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', objectFit: 'cover' }} onError={(e) => { e.currentTarget.src = '/images/default_playlist.jpg'; }} />
          </label>
          <div style={{ flex: 1, minWidth: 280 }}>
            <label className="mm-label">PLAYLIST NAME</label>
            <input name="name" required defaultValue={playlist.name} style={{ width: '100%', background: 'transparent', border: 0, borderBottom: '2px solid var(--mm-line)', color: '#fff', fontSize: '2rem', fontWeight: 800, padding: '8px 0 14px', outline: 'none', marginBottom: 8 }} />
            {errors.name && <div style={{ color: '#f87171', fontSize: '0.8rem', marginBottom: 12 }}>{errors.name}</div>}
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: 26 }}>
              <a href="/library" className="mm-btn-ghost"><ArrowLeft size={15} /> Back to library</a>
              <button type="submit" className="mm-btn-primary"><Check size={16} /> Save changes</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  );
}

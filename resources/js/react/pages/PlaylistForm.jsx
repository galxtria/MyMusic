import { useState } from 'react';
import { ArrowLeft, Check, Globe, ImagePlus, Lock } from 'lucide-react';
import { PageHeader } from '../components/ui';

function CoverPicker({ name, current, preview, setPreview }) {
  return (
    <label style={{ width: 260, height: 260, borderRadius: 20, overflow: 'hidden', position: 'relative', cursor: 'pointer', border: '1px dashed var(--mm-line-2)', background: 'rgba(0,0,0,0.3)', flexShrink: 0, display: 'block' }}>
      <input type="file" name={name} accept="image/*" hidden onChange={(e) => { const f = e.target.files?.[0]; if (f) setPreview(URL.createObjectURL(f)); }} />
      {(preview || current)
        ? <img src={preview || current} alt="Cover" style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', objectFit: 'cover' }} onError={(e) => { e.currentTarget.src = '/images/default_playlist.jpg'; }} />
        : (
          <span style={{ position: 'absolute', inset: 0, display: 'grid', placeItems: 'center', textAlign: 'center', color: 'var(--mm-dim)', padding: 16 }}>
            <span>
              <ImagePlus size={36} style={{ marginBottom: 10 }} />
              <div style={{ fontWeight: 800, color: '#fff' }}>Add cover</div>
              <div style={{ fontSize: '0.78rem' }}>JPG / PNG • min 300×300</div>
            </span>
          </span>
        )}
      {(preview || current) && (
        <span style={{ position: 'absolute', left: 0, right: 0, bottom: 0, padding: '8px 12px', fontSize: '0.72rem', fontWeight: 800, letterSpacing: 0.6, background: 'rgba(0,0,0,0.6)', color: '#fff', textAlign: 'center' }}>
          CLICK TO REPLACE
        </span>
      )}
    </label>
  );
}

function PublicSwitch({ defaultChecked }) {
  const [on, setOn] = useState(!!defaultChecked);
  return (
    <label className="mm-switch" style={{ marginTop: 18 }}>
      <input type="checkbox" name="is_public" value="1" defaultChecked={!!defaultChecked} onChange={(e) => setOn(e.target.checked)} />
      <span className="mm-track" />
      <span style={{ display: 'inline-flex', alignItems: 'center', gap: 7 }}>
        {on ? <Globe size={15} style={{ color: 'var(--mm-teal)' }} /> : <Lock size={15} />}
        {on ? 'Public — anyone with the link can view & fork' : 'Private — only you can see this'}
      </span>
    </label>
  );
}

function NameField({ defaultValue, autoFocus }) {
  const [len, setLen] = useState((defaultValue || '').length);
  return (
    <>
      <label className="mm-label">PLAYLIST NAME</label>
      <input
        name="name" required maxLength={80} defaultValue={defaultValue} autoFocus={autoFocus}
        placeholder="Untitled playlist" onChange={(e) => setLen(e.target.value.length)}
        style={{ width: '100%', background: 'transparent', border: 0, borderBottom: '2px solid var(--mm-line)', color: '#fff', fontSize: '2rem', fontWeight: 800, padding: '8px 0 14px', outline: 'none', marginBottom: 4 }}
      />
      <div style={{ fontSize: '0.72rem', color: 'var(--mm-faint)', textAlign: 'right', marginBottom: 8 }}>{len}/80</div>
    </>
  );
}

function DescField({ defaultValue }) {
  const [len, setLen] = useState((defaultValue || '').length);
  return (
    <>
      <label className="mm-label" style={{ marginTop: 14 }}>DESCRIPTION (OPTIONAL)</label>
      <textarea
        name="description" rows={3} maxLength={500} defaultValue={defaultValue}
        placeholder="What is this playlist for? Mood, moment, story..." className="mm-input"
        style={{ padding: 14 }} onChange={(e) => setLen(e.target.value.length)}
      />
      <div style={{ fontSize: '0.72rem', color: 'var(--mm-faint)', textAlign: 'right', marginTop: 4 }}>{len}/500</div>
    </>
  );
}

export default function CreatePlaylist({ errors = {} }) {
  const [preview, setPreview] = useState('');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

  return (
    <div style={{ minHeight: '70vh', display: 'grid', placeItems: 'center' }}>
      <div style={{ width: '100%', maxWidth: 880 }}>
        <PageHeader badge="STUDIO" title="Create playlist" subtitle="Give it a name, a cover, and start collecting." />
        <form action="/playlist/store" method="POST" encType="multipart/form-data" className="mm-glass mm-enter" style={{ padding: 36, borderRadius: 28, display: 'flex', gap: 34, flexWrap: 'wrap' }}>
          <input type="hidden" name="_token" value={csrf} />
          <CoverPicker name="cover" current="" preview={preview} setPreview={setPreview} />
          <div style={{ flex: 1, minWidth: 280 }}>
            <NameField autoFocus />
            {errors.name && <div style={{ color: '#f87171', fontSize: '0.8rem', marginBottom: 12 }}>{errors.name}</div>}
            <DescField />
            <PublicSwitch defaultChecked={false} />
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: 26, flexWrap: 'wrap', gap: 12 }}>
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
          <CoverPicker name="cover" current={cover} preview={preview} setPreview={setPreview} />
          <div style={{ flex: 1, minWidth: 280 }}>
            <NameField defaultValue={playlist.name} />
            {errors.name && <div style={{ color: '#f87171', fontSize: '0.8rem', marginBottom: 12 }}>{errors.name}</div>}
            <DescField defaultValue={playlist.description || ''} />
            <PublicSwitch defaultChecked={!!playlist.is_public} />
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: 26, flexWrap: 'wrap', gap: 12 }}>
              <a href="/library" className="mm-btn-ghost"><ArrowLeft size={15} /> Back to library</a>
              <button type="submit" className="mm-btn-primary"><Check size={16} /> Save changes</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  );
}

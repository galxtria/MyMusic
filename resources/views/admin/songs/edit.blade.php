@extends('layouts.app')

@section('content')

<style>
    /* GLOBAL SCROLL FIX */
    html, body { height: auto !important; overflow-y: auto !important; background: #050b14; }

    .admin-studio-scope {
        --c-accent: #fbbf24;
        --glass: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.1);
    }

    .page-background {
        position: fixed; inset: 0; z-index: -1; background: #050b14;
        background-image: radial-gradient(circle at 0% 100%, rgba(251, 191, 36, 0.1), transparent 40%),
                          radial-gradient(circle at 100% 0%, rgba(59, 130, 246, 0.1), transparent 40%);
    }
    
    .content-wrapper { position: relative; z-index: 1; min-height: 100vh; padding: 60px 20px 120px; display: flex; justify-content: center; }

    .studio-card {
        width: 100%; max-width: 1000px; background: rgba(15, 23, 42, 0.8);
        backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border); border-radius: 32px;
        padding: 40px; box-shadow: 0 50px 100px -20px rgba(0,0,0,0.6);
        position: relative; overflow: hidden;
    }

    .studio-card::before {
        content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: var(--c-accent);
    }

    .form-label { font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; display: block; }
    
    .input-studio {
        width: 100%; background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border);
        border-radius: 14px; padding: 14px 18px; color: white; transition: 0.3s; outline: none;
    }
    .input-studio:focus { border-color: var(--c-accent); box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.1); }

    .upload-zone {
        width: 100%; aspect-ratio: 1/1; background: rgba(0,0,0,0.2);
        border: 2px dashed var(--glass-border); border-radius: 24px;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        cursor: pointer; transition: 0.3s; overflow: hidden; position: relative;
    }
    .current-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .upload-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; opacity: 0; transition: 0.3s; }
    .upload-zone:hover .upload-overlay { opacity: 1; }

    .btn-update { background: var(--c-accent); color: black; padding: 14px 40px; border-radius: 50px; font-weight: 800; border: none; transition: 0.3s; }
    .btn-update:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(251, 191, 36, 0.3); }
</style>

<div class="admin-studio-scope">
    <div class="page-background"></div>
    <div class="content-wrapper">
        <div class="studio-card">
            <div class="mb-5 d-flex justify-content-between align-items-center">
                <h1 style="font-size: 1.5rem; color: white; font-weight: 800; margin:0;">Edit Track Details</h1>
                <span style="background: rgba(251, 191, 36, 0.1); color: var(--c-accent); padding: 5px 15px; border-radius: 10px; font-size: 0.7rem; font-weight: 800; letter-spacing: 1px;">TRACK ID: #{{ $song->id }}</span>
            </div>

            {{-- Pesan Error Validasi --}}
            @if ($errors->any())
                <div class="alert alert-danger bg-danger text-white border-0 rounded-4 mb-4 p-3 shadow-sm">
                    <ul class="mb-0 small fw-bold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.songs.update', $song->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row g-5">
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label class="form-label">Update Cover</label>
                            <label class="upload-zone">
                                <img src="{{ asset($song->album_art) }}" id="previewImg" class="current-img">
                                <div class="upload-overlay">
                                    <i class="fas fa-camera fa-2x text-white"></i>
                                </div>
                                <input type="file" name="image" accept="image/*" hidden onchange="previewImage(this)">
                            </label>
                        </div>
                        
                        <div class="p-4 rounded-4" style="background: rgba(251, 191, 36, 0.03); border: 1px solid rgba(251, 191, 36, 0.1);">
                            <label class="form-label" style="color: var(--c-accent);">Audio Source</label>
                            <div class="mb-3 text-white-50 small text-truncate">
                                <i class="fas fa-file-audio me-2"></i>Current: {{ basename($song->file_path) }}
                            </div>
                            <input type="file" name="audio" accept=".mp3" class="form-control form-control-sm bg-transparent border-secondary text-white">
                            <small class="text-warning mt-2 d-block" style="font-size: 0.65rem;">* Biarkan kosong jika tidak ingin mengganti lagu</small>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="mb-4">
                            <label class="form-label">Track Title</label>
                            <input type="text" name="title" class="input-studio" style="font-size: 1.5rem; font-weight: 700;" value="{{ old('title', $song->title) }}" required>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Artist Name</label>
                                <input type="text" name="artist" class="input-studio" value="{{ old('artist', $song->artist) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Genre</label>
                                <select name="genre" class="input-studio" required>
                                    @php
                                        $genres = ['Indie Rock', 'Britpop', 'Shoegaze', 'Grunge', 'Dream Pop', 'Synthwave', 'Alternative Rock', 'Pop', 'Hip Hop', 'Jazz', 'Indie', 'Soul', 'Dangdut', 'K-Pop', 'City Pop', 'Lofi', 'Phonk', 'Acoustic'];
                                        sort($genres);
                                        $currentGenre = old('genre', $song->genre);
                                    @endphp
                                    @foreach($genres as $g)
                                        <option value="{{ $g }}" {{ $currentGenre == $g ? 'selected' : '' }}>{{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Duration</label>
                                <input type="text" name="duration" class="input-studio" value="{{ old('duration', $song->duration) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Update Artist Photo</label>
                                @if($song->artist_image)
                                    <div class="mb-2 small text-white-50">Current: <a href="{{ asset($song->artist_image) }}" target="_blank" class="text-accent text-decoration-none">View Photo</a></div>
                                @endif
                                <input type="file" name="artist_photo" class="form-control form-control-sm bg-transparent border-secondary text-white">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Lyrics</label>
                            {{-- Gunakan old() pada textarea --}}
                            <textarea name="lyrics" class="input-studio" style="min-height: 250px; font-family: 'Courier New', monospace; font-size: 0.9rem;">{{ old('lyrics', $song->lyrics) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-5">
                            <a href="{{ route('admin.songs.index') }}" class="btn text-white-50 fw-bold px-4">Cancel</a>
                            <button type="submit" class="btn-update shadow-lg">Save Changes</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
@extends('layouts.app')

@section('content')

<style>
    /* GLOBAL SCROLL FIX */
    html, body { height: auto !important; overflow-y: auto !important; background: #050b14; }

    .admin-studio-scope {
        --c-accent: #3b82f6;
        --glass: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.1);
    }

    .page-background {
        position: fixed; inset: 0; z-index: -1; background: #050b14;
        background-image: radial-gradient(circle at 0% 0%, rgba(59, 130, 246, 0.15), transparent 40%),
                          radial-gradient(circle at 100% 100%, rgba(139, 92, 246, 0.15), transparent 40%);
    }
    
    .content-wrapper { position: relative; z-index: 1; min-height: 100vh; padding: 60px 20px 120px; display: flex; justify-content: center; }

    .studio-card {
        width: 100%; max-width: 1000px; background: rgba(15, 23, 42, 0.8);
        backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border); border-radius: 32px;
        padding: 40px; box-shadow: 0 50px 100px -20px rgba(0,0,0,0.5);
    }

    /* HEADER */
    .studio-header { margin-bottom: 40px; border-bottom: 1px solid var(--glass-border); padding-bottom: 20px; }
    .studio-title { font-size: 2rem; font-weight: 800; color: white; margin: 0; letter-spacing: -1px; }

    /* FORM ELEMENTS */
    .form-label { font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; display: block; }
    
    .input-studio {
        width: 100%; background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border);
        border-radius: 14px; padding: 14px 18px; color: white; transition: 0.3s; outline: none;
    }
    .input-studio:focus { border-color: var(--c-accent); box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }

    /* DROPDOWN FIX */
    select.input-studio { cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 15px center; background-size: 15px; }
    select.input-studio option { background-color: #0f172a; color: white; padding: 15px; }

    /* UPLOAD AREA */
    .upload-zone {
        width: 100%; aspect-ratio: 1/1; background: rgba(255,255,255,0.02);
        border: 2px dashed var(--glass-border); border-radius: 24px;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        cursor: pointer; transition: 0.3s; overflow: hidden; position: relative;
    }
    .upload-zone:hover { border-color: var(--c-accent); background: rgba(59, 130, 246, 0.05); }
    #previewImg { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: none; }

    .btn-save { background: white; color: black; padding: 14px 40px; border-radius: 50px; font-weight: 800; border: none; transition: 0.3s; }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(255,255,255,0.1); }
</style>

<div class="admin-studio-scope">
    <div class="page-background"></div>
    <div class="content-wrapper">
        <div class="studio-card">
            <div class="studio-header">
                <h1 class="studio-title">Add New Track</h1>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" style="background: rgba(220, 38, 38, 0.2); border: 1px solid #dc2626; color: white; border-radius: 14px; padding: 15px; margin-bottom: 20px;">
                    <p class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i> Upload Gagal:</p>
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.songs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-5">
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label class="form-label">Cover Art</label>
                            <label class="upload-zone">
                                <img id="previewImg">
                                <div class="text-center p-3" id="uploadPlaceholder">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-white-50 mb-3"></i>
                                    <p class="text-white-50 small fw-bold mb-0">Drag or Click to Upload Image</p>
                                </div>
                                <input type="file" name="image" accept="image/*" hidden onchange="previewImage(this)">
                            </label>
                        </div>
                        
                        <div class="p-4 rounded-4" style="background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.1);">
                            <label class="form-label text-accent">Audio Source</label>
                            <input type="file" name="audio" accept=".mp3" class="form-control form-control-sm bg-transparent border-secondary text-white" required>
                            <small class="text-white-50 mt-2 d-block">Format: MP3 only (Max 10MB)</small>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="mb-4">
                            <label class="form-label">Track Title</label>
                            <input type="text" name="title" class="input-studio" style="font-size: 1.5rem; font-weight: 700;" placeholder="Enter song title..." required>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Artist Name</label>
                                <input type="text" name="artist" class="input-studio" placeholder="Artist name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Genre</label>
                                <select name="genre" class="input-studio" required>
                                    <option value="" disabled selected>Select genre...</option>
                                    @php
                                        $genres = [
                                            'Indie Rock', 'Britpop', 'Shoegaze', 'Grunge', 'Dream Pop', 'Synthwave', 
                                            'Post-Punk', 'Alternative Rock', 'Pop', 'Hip Hop', 'R&B', 'Electronic', 
                                            'Jazz', 'Indie', 'Soul', 'Dangdut', 'K-Pop', 'City Pop', 'Lofi', 'Phonk',
                                            'Reggae', 'Blues', 'Classical', 'Metal', 'Folk', 'Ambient', 'Acoustic'
                                        ];
                                        sort($genres);
                                    @endphp
                                    @foreach($genres as $g)
                                        <option value="{{ $g }}">{{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Duration</label>
                                <input type="text" name="duration" class="input-studio" placeholder="03:45" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Artist Photo (Optional)</label>
                                <input type="file" name="artist_photo" class="form-control form-control-sm bg-transparent border-secondary text-white">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Lyrics (LRC Format preferred)</label>
                            <textarea name="lyrics" class="input-studio" style="min-height: 250px; font-family: 'Fira Code', monospace; font-size: 0.9rem;" placeholder="[00:10.00] Lyrics here..."></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-5">
                            <a href="{{ route('admin.songs.index') }}" class="btn text-white-50 fw-bold px-4">Cancel</a>
                            <button type="submit" class="btn-save shadow-lg">Upload Track</button>
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
                const img = document.getElementById('previewImg');
                img.src = e.target.result;
                img.style.display = 'block';
                document.getElementById('uploadPlaceholder').style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
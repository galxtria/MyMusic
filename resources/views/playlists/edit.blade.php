@extends('layouts.app')

@section('content')
<style>
    /* --- AURORA BACKGROUND (TANPA BINTIK) --- */
    .page-background {
        position: fixed; 
        inset: 0; 
        z-index: 0;
        background: #050b14;
        /* Lapisan Aurora Bergerak */
        background-image: 
            radial-gradient(circle at 20% 30%, rgba(44, 116, 179, 0.15), transparent 50%), 
            radial-gradient(circle at 80% 70%, rgba(79, 70, 229, 0.15), transparent 50%),
            radial-gradient(circle at 50% 50%, rgba(236, 72, 153, 0.05), transparent 60%);
        overflow: hidden;
    }

    /* Animasi Aurora Bergerak Pelan */
    @keyframes auroraMove {
        0% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(-5%, 5%) scale(1.1); }
        100% { transform: translate(0, 0) scale(1); }
    }

    .aurora-layer {
        position: absolute;
        inset: -50%;
        z-index: -1;
        background: radial-gradient(circle at 50% 50%, rgba(44, 116, 179, 0.1), transparent 50%);
        filter: blur(80px);
        animation: auroraMove 15s ease-in-out infinite;
    }

    .edit-scope {
        position: relative;
        z-index: 1;
        min-height: 85vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .edit-card {
        width: 100%;
        max-width: 480px;
        background: rgba(255, 255, 255, 0.02);
        backdrop-filter: blur(40px);
        -webkit-backdrop-filter: blur(40px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 40px;
        padding: 50px 40px;
        box-shadow: 0 50px 100px rgba(0, 0, 0, 0.6);
    }

    .edit-header h2 {
        font-weight: 900;
        letter-spacing: -1.5px;
        background: linear-gradient(135deg, #fff 30%, var(--c-accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 35px;
    }

    .form-label-custom {
        color: rgba(255, 255, 255, 0.5);
        font-weight: 800;
        font-size: 0.7rem;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-bottom: 12px;
        display: block;
    }

    .input-glass {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        color: white;
        padding: 16px 22px;
        transition: 0.3s;
    }

    .input-glass:focus {
        background: rgba(255, 255, 255, 0.07);
        border-color: var(--c-accent);
        box-shadow: 0 0 20px rgba(44, 116, 179, 0.2);
        color: white;
        outline: none;
    }

    /* Area Preview Gambar */
    .preview-wrapper {
        position: relative;
        width: 200px;
        height: 200px;
        margin: 0 auto 40px;
        cursor: pointer;
    }

    .img-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 35px;
        border: 2px solid rgba(255, 255, 255, 0.1);
        transition: 0.4s ease;
    }

    .preview-wrapper:hover .img-preview {
        transform: scale(1.03);
        border-color: var(--c-accent);
        filter: brightness(0.7);
    }

    .camera-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        opacity: 0;
        transition: 0.3s;
    }

    .preview-wrapper:hover .camera-overlay { opacity: 1; }

    .btn-save {
        background: linear-gradient(135deg, var(--c-accent), #5ba4e5);
        border: none;
        border-radius: 22px;
        padding: 18px;
        font-weight: 800;
        color: white;
        box-shadow: 0 10px 30px rgba(44, 116, 179, 0.3);
        transition: 0.3s;
    }

    .btn-save:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(44, 116, 179, 0.5);
    }

    .btn-cancel {
        color: rgba(255, 255, 255, 0.4);
        text-decoration: none;
        font-weight: 700;
        transition: 0.3s;
    }

    .btn-cancel:hover { color: #ff4d4d; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-enter { animation: fadeInUp 0.8s ease forwards; }
</style>

<div class="page-background">
    <div class="aurora-layer"></div>
</div>

<div class="edit-scope">
    <div class="edit-card animate-enter">
        <div class="edit-header text-center">
            <h2>Edit Playlist</h2>
        </div>
        
        <form action="{{ route('playlist.update', $playlist->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="preview-wrapper" onclick="document.getElementById('coverInput').click()">
                <img src="{{ asset($playlist->cover_path ?? 'images/default_playlist.jpg') }}" id="coverPreview" class="img-preview shadow-lg">
                <div class="camera-overlay">
                    <i class="fas fa-camera"></i>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label-custom">Playlist Name</label>
                <input type="text" name="name" class="form-control input-glass" value="{{ $playlist->name }}" required autocomplete="off">
            </div>

            <input type="file" name="cover" id="coverInput" accept="image/*" style="display: none;">

            <div class="d-grid gap-3 mt-5">
                <button type="submit" class="btn btn-primary btn-save">
                    Save Changes
                </button>
                <div class="text-center">
                    <a href="{{ route('library') }}" class="btn-cancel">Discard Changes</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('coverInput').onchange = evt => {
        const [file] = document.getElementById('coverInput').files
        if (file) {
            const preview = document.getElementById('coverPreview');
            preview.src = URL.createObjectURL(file);
        }
    }
</script>
@endsection
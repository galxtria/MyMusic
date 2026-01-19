@extends('layouts.app')

@section('content')

<style>
    /* --- SCOPED VARIABLES (CREATE) --- */
    .create-scope {
        --c-deep: #050b14;
        --c-accent-1: #4f46e5;
        --c-accent-2: #06b6d4;
        --glass-border: rgba(255, 255, 255, 0.08);
        --glass-surface: rgba(255, 255, 255, 0.03);
    }

    /* --- BACKGROUND --- */
    .page-background {
        position: fixed; inset: 0; z-index: 0;
        background: #050b14;
        background-image: 
            radial-gradient(circle at 20% 20%, rgba(6, 182, 212, 0.1), transparent 40%), 
            radial-gradient(circle at 80% 80%, rgba(79, 70, 229, 0.1), transparent 40%);
    }

    .create-content-wrapper { 
        position: relative; z-index: 1; 
        min-height: 85vh; display: flex; align-items: center; justify-content: center;
        padding: 20px;
    }

    /* --- ANIMATIONS --- */
    @keyframes floatUp {
        from { opacity: 0; transform: translateY(30px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-enter { animation: floatUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; opacity: 0; }

    /* --- GLASS PANEL --- */
    .studio-panel {
        width: 100%; max-width: 900px;
        background: rgba(255, 255, 255, 0.02);
        backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px);
        border: 1px solid var(--glass-border);
        border-radius: 30px;
        padding: 50px;
        box-shadow: 0 40px 80px rgba(0,0,0,0.5);
        position: relative; overflow: hidden;
    }
    
    .studio-panel::before {
        content: ''; position: absolute; top: -100px; right: -100px; width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(79, 70, 229, 0.15) 0%, transparent 70%);
        pointer-events: none;
    }

    .studio-content { display: flex; gap: 50px; align-items: flex-start; position: relative; z-index: 2; }

    /* --- UPLOAD BOX --- */
    .image-upload-wrapper { width: 300px; height: 300px; flex-shrink: 0; }
    
    .image-upload-box {
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.3);
        border-radius: 20px;
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        cursor: pointer; overflow: hidden; position: relative;
        border: 2px dashed rgba(255,255,255,0.1);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    }
    
    .image-upload-box:hover {
        border-color: var(--c-accent-2);
        background: rgba(255,255,255,0.05);
        transform: translateY(-5px);
        box-shadow: 0 30px 60px rgba(0,0,0,0.5);
    }

    #imagePreview {
        position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;
        display: none; transition: 0.5s; z-index: 1;
    }
    
    .upload-placeholder { z-index: 2; text-align: center; transition: 0.3s; }
    .upload-icon { font-size: 3.5rem; color: rgba(255,255,255,0.2); margin-bottom: 15px; transition: 0.3s; }
    
    .image-upload-box:hover .upload-icon { color: var(--c-accent-2); transform: scale(1.1); }

    /* Hover State if Image Exists */
    .image-upload-box:hover #imagePreview { filter: brightness(0.4) blur(4px); }
    .upload-placeholder.has-image { opacity: 0; position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; }
    .image-upload-box:hover .upload-placeholder.has-image { opacity: 1; }

    /* --- INPUTS --- */
    .form-section { flex-grow: 1; display: flex; flex-direction: column; justify-content: center; height: 300px; }

    .lbl-small {
        font-size: 0.75rem; font-weight: 800; letter-spacing: 2px; text-transform: uppercase;
        color: rgba(255,255,255,0.4); margin-bottom: 8px; display: block;
    }

    .input-hero {
        width: 100%; background: transparent; border: none;
        border-bottom: 2px solid rgba(255,255,255,0.1);
        color: white; font-size: 2.8rem; font-weight: 800;
        padding: 10px 0; margin-bottom: 30px; transition: 0.3s;
    }
    .input-hero:focus { outline: none; border-bottom-color: var(--c-accent-2); }
    .input-hero::placeholder { color: rgba(255,255,255,0.05); }

    .input-desc {
        width: 100%; background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.1); border-radius: 12px;
        color: white; padding: 15px; font-size: 1rem; resize: none; transition: 0.3s;
    }
    .input-desc:focus { outline: none; border-color: var(--c-accent-1); background: rgba(255,255,255,0.06); }

    /* --- FOOTER --- */
    .studio-footer {
        display: flex; justify-content: space-between; align-items: center;
        padding-top: 30px; border-top: 1px solid var(--glass-border);
    }

    .btn-create {
        background: white; color: black;
        padding: 14px 45px; border-radius: 50px;
        font-size: 1rem; font-weight: 800; border: none; cursor: pointer;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 30px rgba(255,255,255,0.1);
    }
    .btn-create:hover { 
        transform: scale(1.05); 
        box-shadow: 0 15px 40px rgba(255,255,255,0.2);
        background: #f8f9fa;
    }

    .text-error { color: #ff4d4d; font-size: 0.8rem; margin-top: -20px; margin-bottom: 20px; display: block; }

    @media (max-width: 900px) {
        .studio-panel { padding: 30px; }
        .studio-content { flex-direction: column; align-items: center; gap: 30px; }
        .form-section { width: 100%; height: auto; text-align: center; }
        .input-hero { text-align: center; font-size: 2rem; }
        .image-upload-wrapper { width: 220px; height: 220px; }
    }
</style>

<div class="create-scope">
    <div class="page-background"></div>

    <div class="container create-content-wrapper">
        <div class="studio-panel animate-enter">
            
            {{-- FORM ACTION DIARAHKAN KE CONTROLLER --}}
            <form action="{{ route('playlist.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="studio-content">
                    {{-- UPLOAD COVER --}}
                    <div class="image-upload-wrapper">
                        <label for="coverInput" class="image-upload-box" id="uploadBox">
                            <img id="imagePreview" src="#" alt="Playlist Cover">
                            
                            <div class="upload-placeholder" id="uploadPlaceholder">
                                <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.05); border-radius: 50%; display:flex; align-items:center; justify-content:center; margin: 0 auto 15px;">
                                    <i class="fas fa-plus fa-2x text-white-50"></i>
                                </div>
                                <div class="fw-bold text-white fs-5">Add Cover</div>
                                <div class="small text-white-50 mt-1">Min 300x300px</div>
                            </div>

                            <input type="file" id="coverInput" name="cover" accept="image/*" style="display: none;" onchange="previewImage(this)">
                        </label>
                        @error('cover') <span class="text-error text-center mt-2 d-block">{{ $message }}</span> @enderror
                    </div>

                    {{-- FORM INPUTS --}}
                    <div class="form-section">
                        <div>
                            <label class="lbl-small">Playlist Name</label>
                            <input type="text" class="input-hero" name="name" 
                                   value="{{ old('name') }}"
                                   placeholder="Untitled Playlist" required autocomplete="off" autofocus>
                            @error('name') <span class="text-error">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="lbl-small">Description (Optional)</label>
                            <textarea class="input-desc" name="description" rows="3" 
                                      placeholder="Give your playlist a cool description...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="studio-footer">
                    <a href="{{ route('library') }}" class="text-white-50 text-decoration-none fw-bold hover-white" style="transition: 0.3s;">
                        <i class="fas fa-arrow-left me-2"></i> Back to Library
                    </a>
                    
                    <button type="submit" class="btn-create">
                        Confirm & Save
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- SCRIPT PREVIEW DENGAN TRANSISI --}}
<script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const placeholder = document.getElementById('uploadPlaceholder');
        const box = document.getElementById('uploadBox');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                
                // Animasi Transisi
                placeholder.classList.add('has-image');
                box.style.borderStyle = 'solid';
                box.style.borderColor = 'rgba(255,255,255,0.2)';
                
                // Update text placeholder saat di hover (Edit Mode)
                placeholder.querySelector('.fw-bold').innerText = "Change Cover";
                placeholder.querySelector('.text-white-50').style.display = 'none';
                placeholder.querySelector('div').innerHTML = '<i class="fas fa-camera text-white"></i>';
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

@endsection
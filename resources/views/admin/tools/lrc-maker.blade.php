@extends('layouts.app')

@section('content')

<style>
    /* --- SCOPED VARIABLES --- */
    .lrc-scope {
        --c-deep: #050b14;
        --c-accent: #3b82f6; 
        --glass-border: rgba(255, 255, 255, 0.15);
        --glass-surface: rgba(15, 23, 42, 0.8);
    }

    .page-background {
        position: fixed; inset: 0; z-index: 0;
        background: #050b14;
        background-image: 
            radial-gradient(circle at 10% 10%, rgba(139, 92, 246, 0.15), transparent 40%), 
            radial-gradient(circle at 90% 90%, rgba(6, 182, 212, 0.1), transparent 40%);
    }

    .lrc-content-wrapper { position: relative; z-index: 1; padding-top: 40px; }
    .tool-header { margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--glass-border); }
    .tool-title { font-size: 2.5rem; font-weight: 900; color: white; letter-spacing: -1px; margin: 0; }

    .lrc-panel { display: grid; grid-template-columns: 380px 1fr; gap: 30px; align-items: start; }

    /* KIRI: CONTROL PANEL */
    .control-panel {
        background: var(--glass-surface); backdrop-filter: blur(30px);
        border: 1px solid var(--glass-border); border-radius: 24px; padding: 30px;
        position: sticky; top: 100px; box-shadow: 0 20px 50px rgba(0,0,0,0.4);
    }

    .source-switcher { display: flex; background: rgba(0,0,0,0.3); border-radius: 12px; padding: 5px; margin-bottom: 20px; }
    .source-btn { flex: 1; padding: 8px; border-radius: 8px; border: none; background: transparent; color: #94a3b8; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: 0.3s; }
    .source-btn.active { background: var(--c-accent); color: white; }

    .song-select-box { margin-bottom: 25px; }
    .song-select-box label { font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 10px; display: block; }
    
    /* FIX: DROPDOWN COLOR */
    .custom-select {
        width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);
        border-radius: 12px; padding: 12px; color: white; cursor: pointer; transition: 0.3s;
    }
    .custom-select option {
        background-color: #0f172a; /* Warna gelap agar teks putih terlihat */
        color: white;
        padding: 10px;
    }

    .file-input-custom { width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 12px; padding: 10px; color: white; }

    .audio-visual-box { background: rgba(0,0,0,0.3); border-radius: 16px; padding: 20px; text-align: center; margin-bottom: 25px; }
    
    .stamp-btn {
        width: 100%; padding: 20px; border-radius: 16px; border: none;
        background: white; color: black; font-weight: 800; font-size: 1.1rem;
        cursor: pointer; transition: 0.3s; display: flex; flex-direction: column; align-items: center; gap: 5px;
    }
    .stamp-btn:hover:not(:disabled) { transform: scale(1.02); background: #f8fafc; }
    .stamp-btn:disabled { opacity: 0.3; cursor: not-allowed; }

    /* KANAN: EDITOR */
    .editor-panel { background: var(--glass-surface); backdrop-filter: blur(30px); border: 1px solid var(--glass-border); border-radius: 24px; overflow: hidden; }
    .editor-header { padding: 20px 30px; background: rgba(255,255,255,0.03); border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; }
    .lrc-textarea { width: 100%; min-height: 550px; background: transparent; border: none; padding: 30px; color: #e2e8f0; font-family: 'Fira Code', monospace; font-size: 1.1rem; line-height: 1.6; resize: none; outline: none; }

    .btn-action { padding: 10px 25px; border-radius: 50px; font-weight: 700; border: none; cursor: pointer; transition: 0.3s; }
    .btn-copy { background: rgba(255,255,255,0.1); color: white; }
    .btn-save { background: var(--c-accent); color: white; }

    @media (max-width: 992px) { .lrc-panel { grid-template-columns: 1fr; } .control-panel { position: static; } }
</style>

<div class="lrc-scope">
    <div class="page-background"></div>
    <div class="container pb-5 lrc-content-wrapper">
        <div class="tool-header d-flex align-items-center gap-3">
            <h1 class="tool-title">LRC Studio</h1>
        </div>

        <div class="lrc-panel">
            <div class="control-panel">
                <label class="small fw-bold text-white-50 mb-2 d-block">SUMBER AUDIO</label>
                <div class="source-switcher">
                    <button class="source-btn active" data-type="db">Database</button>
                    <button class="source-btn" data-type="local">Komputer</button>
                </div>

                <div class="song-select-box" id="dbSource">
                    <label>Pilih dari Library</label>
                    <select class="custom-select" id="songSelect">
                        <option value="">-- Pilih Lagu --</option>
                        @foreach($songs as $song)
                            <option value="{{ asset($song->file_path) }}" data-title="{{ $song->title }}">
                                {{ $song->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="song-select-box d-none" id="localSource">
                    <label>Ambil File MP3</label>
                    <input type="file" id="localFileInput" class="file-input-custom" accept="audio/*">
                </div>

                <div class="audio-visual-box">
                    <i class="fas fa-compact-disc fa-4x text-white-50 mb-3" id="diskIcon"></i>
                    <h6 class="text-white mb-0" id="currentSongTitle">Siap Memulai</h6>
                    <audio id="lrcAudio" controls class="w-100 mt-3" style="height: 35px;"></audio>
                </div>

                <button class="stamp-btn" id="stampBtn" disabled>
                    <span><i class="fas fa-clock me-2"></i> STAMP TIME</span>
                    <span style="font-size: 0.7rem; color: #64748b;">Tekan [ ENTER ] untuk stamp</span>
                </button>
            </div>

            <div class="editor-panel">
                <div class="editor-header">
                    <span class="text-white-50 small fw-bold">EDITOR</span>
                    <div class="d-flex gap-2">
                        <button class="btn-action btn-copy" id="copyBtn">Salin</button>
                        <button class="btn-action btn-save" id="downloadBtn">Unduh .lrc</button>
                    </div>
                </div>
                <textarea id="lrcEditor" class="lrc-textarea" placeholder="Masukkan lirik teks saja..."></textarea>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const audio = document.getElementById('lrcAudio');
    const editor = document.getElementById('lrcEditor');
    const stampBtn = document.getElementById('stampBtn');
    const diskIcon = document.getElementById('diskIcon');
    const currentSongTitle = document.getElementById('currentSongTitle');

    // 1. Switcher Logic
    document.querySelectorAll('.source-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.source-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const type = this.dataset.type;
            document.getElementById('dbSource').classList.toggle('d-none', type !== 'db');
            document.getElementById('localSource').classList.toggle('d-none', type !== 'local');
            
            audio.src = "";
            stampBtn.disabled = true;
            diskIcon.classList.remove('fa-spin');
            currentSongTitle.innerText = "Siap Memulai";
        });
    });

    // 2. Load Logic
    document.getElementById('songSelect').addEventListener('change', function() {
        if(this.value) {
            audio.src = this.value;
            currentSongTitle.innerText = this.options[this.selectedIndex].dataset.title;
            activateUI();
        }
    });

    document.getElementById('localFileInput').addEventListener('change', function() {
        if(this.files[0]) {
            audio.src = URL.createObjectURL(this.files[0]);
            currentSongTitle.innerText = this.files[0].name;
            activateUI();
        }
    });

    function activateUI() {
        stampBtn.disabled = false;
        diskIcon.classList.add('fa-spin');
        diskIcon.style.color = '#3b82f6';
    }

    // 3. Stamp Logic
    function doStamp() {
        if (audio.paused || audio.src === "") return;

        const time = audio.currentTime;
        const min = Math.floor(time / 60).toString().padStart(2, '0');
        const sec = (time % 60).toFixed(2).padStart(5, '0');
        const timestamp = `[${min}:${sec}] `;

        const start = editor.selectionStart;
        const text = editor.value;
        const lineStart = text.lastIndexOf('\n', start - 1) + 1;
        
        // Sisipkan timestamp di awal baris kursor berada
        const newText = text.slice(0, lineStart) + timestamp + text.slice(lineStart);
        editor.value = newText;

        // Pindah ke baris berikutnya
        const nextLine = editor.value.indexOf('\n', lineStart + timestamp.length) + 1;
        if(nextLine > 0) {
            editor.setSelectionRange(nextLine, nextLine);
            editor.focus();
        }
    }

    // 4. Listeners
    stampBtn.addEventListener('click', doStamp);
    
    editor.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            doStamp();
        }
    });

    document.getElementById('copyBtn').addEventListener('click', () => {
        editor.select();
        document.execCommand('copy');
        alert('Disalin!');
    });

    document.getElementById('downloadBtn').addEventListener('click', () => {
        const blob = new Blob([editor.value], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = currentSongTitle.innerText.split('.')[0] + ".lrc";
        a.click();
    });
});
</script>
@endsection
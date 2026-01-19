<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminSongController extends Controller
{
    // 1. Tampilkan Daftar Lagu
    public function index(Request $request)
    {
        $search = $request->input('search');

        $songs = Song::query()
            ->when($search, function ($query, $search) {
                return $query->where('title', 'LIKE', "%{$search}%")
                             ->orWhere('artist', 'LIKE', "%{$search}%")
                             ->orWhere('genre', 'LIKE', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.songs.index', compact('songs'));
    }

    // 2. Tampilkan Form Tambah Lagu
    public function create()
    {
        return view('admin.songs.create');
    }

    // 3. Proses Simpan Lagu Baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'genre'  => 'required|string',
            'duration' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Dinaikkan ke 5MB agar aman
            'audio' => 'required|mimes:mp3|max:20480', // Dinaikkan ke 20MB untuk lagu durasi panjang
            'artist_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Pastikan folder tujuan ada, jika tidak buat otomatis
        if (!File::isDirectory(public_path('images'))) File::makeDirectory(public_path('images'), 0777, true);
        if (!File::isDirectory(public_path('music'))) File::makeDirectory(public_path('music'), 0777, true);
        if (!File::isDirectory(public_path('images/artist'))) File::makeDirectory(public_path('images/artist'), 0777, true);

        // 1. Upload Gambar Album
        $imageName = time() . '_cover.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        // 2. Upload File Audio
        $audioName = time() . '_audio.' . $request->audio->extension();
        $request->audio->move(public_path('music'), $audioName);

        // 3. Upload Foto Artis (Opsional)
        $artistImageName = null;
        if ($request->hasFile('artist_photo')) {
            $artistImageName = time() . '_artist.' . $request->artist_photo->extension();
            $request->artist_photo->move(public_path('images/artist'), $artistImageName);
        }

        // Simpan data ke Database
        Song::create([
            'title' => $request->title,
            'artist' => $request->artist,
            'genre'  => $request->genre,
            'duration' => $request->duration,
            'album_art' => '/images/' . $imageName,
            'file_path' => '/music/' . $audioName,
            // Fallback ke cover jika foto artis kosong
            'artist_image' => $artistImageName ? '/images/artist/' . $artistImageName : '/images/' . $imageName, 
            'lyrics' => $request->lyrics
        ]);

        return redirect()->route('admin.songs.index')->with('success', 'Lagu berhasil ditambahkan!');
    }

    // 4. Tampilkan Form Edit
    public function edit(Song $song)
    {
        return view('admin.songs.edit', compact('song'));
    }

   // 5. Proses Update Lagu
    public function update(Request $request, Song $song)
    {
        $request->validate([
            'title' => 'required',
            'artist' => 'required',
            'genre' => 'required', // <--- Tambahkan Validasi Genre
            'duration' => 'required',
        ]);

        // Data dasar yang akan diupdate
        $data = [
            'title' => $request->title,
            'artist' => trim($request->artist),
            'genre' => $request->genre, // <--- PENTING: Tangkap input genre dari form
            'duration' => $request->duration,
            'lyrics' => $request->lyrics,
        ];

        // --- (Kode upload gambar/audio di bawah ini TETAP SAMA, tidak perlu diubah) ---
        
        // Cek jika ada gambar album baru diupload
        if ($request->hasFile('image')) {
            if (\Illuminate\Support\Facades\File::exists(public_path($song->album_art))) {
                \Illuminate\Support\Facades\File::delete(public_path($song->album_art));
            }
            $imageName = time() . '_cover.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data['album_art'] = '/images/' . $imageName;
        }

        // Cek jika ada file audio baru diupload
        if ($request->hasFile('audio')) {
            if (\Illuminate\Support\Facades\File::exists(public_path($song->file_path))) {
                \Illuminate\Support\Facades\File::delete(public_path($song->file_path));
            }
            $audioName = time() . '_audio.' . $request->audio->extension();
            $request->audio->move(public_path('music'), $audioName);
            $data['file_path'] = '/music/' . $audioName;
        }
        
         // Cek jika ada foto artis baru diupload
         if ($request->hasFile('artist_photo')) {
            if ($song->artist_image && \Illuminate\Support\Facades\File::exists(public_path($song->artist_image))) {
                \Illuminate\Support\Facades\File::delete(public_path($song->artist_image));
            }
            $artistImageName = time() . '_artist.' . $request->artist_photo->extension();
            $request->artist_photo->move(public_path('images/artist'), $artistImageName);
            $data['artist_image'] = '/images/artist/' . $artistImageName;
        }

        // Simpan Perubahan ke Database
        $song->update($data);

        return redirect()->route('admin.songs.index')->with('success', 'Lagu berhasil diupdate!');
    }

    // 6. Hapus Lagu
    public function destroy(Song $song)
    {
        // Hapus file fisik dari folder agar server tidak penuh
        if(File::exists(public_path($song->album_art))) {
            File::delete(public_path($song->album_art));
        }
        
        if(File::exists(public_path($song->file_path))) {
            File::delete(public_path($song->file_path));
        }

        if($song->artist_image && File::exists(public_path($song->artist_image))) {
            File::delete(public_path($song->artist_image));
        }

        // Hapus data dari database
        $song->delete();
        
        return redirect()->route('admin.songs.index')->with('success', 'Lagu berhasil dihapus.');
    }

    public function lrcMaker()
    {
        // Ambil semua lagu untuk dropdown pilihan
        $songs = Song::orderBy('title', 'asc')->get();
        return view('admin.tools.lrc-maker', compact('songs'));
    }
}
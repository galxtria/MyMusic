<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SongController;
use App\Http\Controllers\AdminSongController;
use App\Http\Controllers\FavoriteController; 
use App\Http\Controllers\Admin\AdminUserController; 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// --- GROUP ROUTE USER (Login Required) ---
Route::middleware(['auth'])->group(function () {
    
    // Core User Routes
    Route::get('/home', [SongController::class, 'index'])->name('home');
    Route::get('/search', [SongController::class, 'search'])->name('search');
    Route::get('/library', [SongController::class, 'library'])->name('library');
    Route::get('/create', [SongController::class, 'create'])->name('create');

    // Route Streaming Musik
    Route::get('/stream-music/{filename}', [SongController::class, 'stream'])->name('music.stream');
    
    // --- FAVORITE SYSTEM ---
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');
    Route::post('/favorites/toggle/{songId}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/favorites/check/{songId}', [FavoriteController::class, 'check'])->name('favorites.check');

    // --- PLAYLIST SYSTEM ---
    Route::post('/playlist/store', [FavoriteController::class, 'storePlaylist'])->name('playlist.store');
    Route::post('/playlist/add-song', [FavoriteController::class, 'addSongToPlaylist'])->name('playlist.add-song');
    Route::get('/playlist/{id}', [FavoriteController::class, 'showPlaylist'])->name('playlist.show');
    
    // Edit & Update Playlist
    Route::get('/playlists/{playlist}/edit', [FavoriteController::class, 'editPlaylist'])->name('playlist.edit');
    Route::put('/playlists/{playlist}', [FavoriteController::class, 'updatePlaylist'])->name('playlist.update');
    
    // Hapus Playlist (Gunakan .destroy agar sinkron dengan library.blade.php)
    Route::delete('/playlist/{id}', [FavoriteController::class, 'destroyPlaylist'])->name('playlist.destroy');
    Route::delete('/playlist-legacy/{id}', [FavoriteController::class, 'destroyPlaylist'])->name('playlist.delete');
    
    // Hapus lagu tertentu dari playlist
    Route::delete('/playlist/{playlistId}/song/{songId}', [FavoriteController::class, 'removeSongFromPlaylist'])->name('playlist.removeSong');

    // Artis
    Route::get('/artist/{name}', [SongController::class, 'artist'])->name('artist.show'); 
});

// --- GROUP ROUTE ADMIN ---
// Perhatikan: .name('admin.') akan menambahkan awalan 'admin.' ke semua rute di dalamnya
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    
    Route::get('/', function () {
        return redirect()->route('admin.songs.index');
    })->name('dashboard');

    // Resource akan menghasilkan admin.songs.index, admin.songs.create, dst.
    Route::resource('songs', AdminSongController::class);
    
    // User Management
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::patch('users/{user}/role', [AdminUserController::class, 'toggleRole'])->name('users.toggle-role');
    
    // Tool LRC Maker (Nama rutenya menjadi admin.tools.lrc-maker)
    Route::get('tools/lrc-maker', [AdminSongController::class, 'lrcMaker'])->name('tools.lrc-maker');                
});
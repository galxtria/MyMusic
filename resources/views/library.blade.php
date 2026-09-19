@extends('layouts.app')

@section('react-page', 'library')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['id' => $me->id, 'name' => $me->name, 'email' => $me->email, 'role' => $me->role] : null,
    'isAdmin' => $me && $me->role === 'admin',
    'playlists' => $playlists->map(function ($pl) {
        return ['id' => $pl->id, 'name' => $pl->name, 'cover_path' => $pl->cover_path, 'songs_count' => $pl->songs_count ?? $pl->songs->count(), 'is_public' => (bool) $pl->is_public, 'share_token' => $pl->share_token, 'share_url' => $pl->share_token ? url('/p/' . $pl->share_token) : null];
    })->values(),
    'publicPlaylists' => isset($publicPlaylists) ? $publicPlaylists->map(function ($pl) {
        return ['id' => $pl->id, 'name' => $pl->name, 'cover_path' => $pl->cover_path, 'songs_count' => $pl->songs_count ?? 0, 'owner' => $pl->user ? $pl->user->name : '-', 'share_token' => $pl->share_token];
    })->values() : [],
    'favoritesCount' => $me ? $me->favoriteSongs()->count() : 0,
    'favoriteIds' => $me ? $me->favoriteSongs->pluck('id')->values() : [],
    'shellPage' => 'library',
];
@endphp
@json($props)
@endsection

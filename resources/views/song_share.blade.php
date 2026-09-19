@extends('layouts.app')

@section('react-page', 'song.share')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['id' => $me->id, 'name' => $me->name, 'email' => $me->email, 'role' => $me->role, 'avatar_url' => $me->avatar_url] : null,
    'isAdmin' => $me && $me->role === 'admin',
    'song' => $song,
    'isFavorite' => $me ? $me->favoriteSongs()->where('songs.id', $song->id)->exists() : false,
    'favoriteIds' => $me ? $me->favoriteSongs->pluck('id')->values() : [],
    'playlists' => $me ? $me->playlists()->get(['id', 'name', 'cover_path'])->values() : [],
    'shellPage' => 'home',
];
@endphp
@json($props)
@endsection

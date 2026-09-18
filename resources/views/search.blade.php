@extends('layouts.app')

@section('react-page', 'search')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['id' => $me->id, 'name' => $me->name, 'email' => $me->email, 'role' => $me->role] : null,
    'isAdmin' => $me && $me->role === 'admin',
    'initialQuery' => $query ?? '',
    'localSongs' => isset($songs) ? $songs->values() : [],
    'localArtists' => isset($artists) ? $artists->values()->map(function ($a) {
        return ['artist' => $a->artist, 'artwork_url' => $a->artwork_url ?? null, 'album_art' => $a->album_art ?? null];
    })->values() : [],
    'favoriteIds' => $me ? $me->favoriteSongs->pluck('id')->values() : [],
    'playlists' => $me ? $me->playlists()->get(['id', 'name', 'cover_path'])->values() : [],
    'shellPage' => 'search',
];
@endphp
@json($props)
@endsection

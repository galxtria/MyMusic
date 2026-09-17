@extends('layouts.app')

@section('react-page', 'home')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['name' => $me->name, 'role' => $me->role] : null,
    'isAdmin' => $me && $me->role === 'admin',
    'songs' => $songs->getCollection()->values(),
    'trending' => isset($trendingSongs) ? $trendingSongs->values() : [],
    'artists' => $songs->getCollection()->unique('artist')->take(10)->map(function ($s) {
        return ['artist' => $s->artist, 'artwork_url' => $s->artwork_url, 'album_art' => $s->album_art, 'artist_image' => $s->artist_image];
    })->values(),
    'pagination' => [
        'current' => $songs->currentPage(),
        'lastPage' => $songs->lastPage(),
        'prev' => $songs->previousPageUrl(),
        'next' => $songs->nextPageUrl(),
    ],
    'favoriteIds' => $me ? $me->favoriteSongs->pluck('id')->values() : [],
    'playlists' => $me ? $me->playlists()->get(['id', 'name', 'cover_path'])->values() : [],
    'shellPage' => 'home',
];
@endphp
@json($props)
@endsection

@extends('layouts.app')

@section('react-page', 'artist')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['id' => $me->id, 'name' => $me->name, 'email' => $me->email, 'role' => $me->role] : null,
    'isAdmin' => $me && $me->role === 'admin',
    'artistName' => $artistName,
    'artistCover' => $artistInfo->artwork_url ?? $artistInfo->artist_image ?? $artistInfo->album_art,
    'songs' => $songs->values(),
    'isFollowing' => $isFollowing ?? false,
    'followers' => $followers ?? 0,
    'favoriteIds' => $me ? $me->favoriteSongs->pluck('id')->values() : [],
    'playlists' => $me ? $me->playlists()->get(['id', 'name', 'cover_path'])->values() : [],
    'shellPage' => 'home',
];
@endphp
@json($props)
@endsection

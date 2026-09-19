@extends('layouts.app')

@section('react-page', 'profile')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['id' => $me->id, 'name' => $me->name, 'email' => $me->email, 'role' => $me->role, 'avatar_url' => $me->avatar_url, 'created_at' => $me->created_at ? $me->created_at->format('M Y') : ''] : null,
    'isAdmin' => $me && $me->role === 'admin',
    'stats' => [
        'totalPlays' => $totalPlays,
        'minutes' => $minutes,
        'likesCount' => $likesCount,
        'playlistCount' => $playlistCount,
    ],
    'topSongs' => $topSongs->values(),
    'topSongPlays' => $topSongPlays,
    'topArtists' => $topArtists->values(),
    'recentRows' => $recentRows->map(function ($r) { return ['song_id' => $r->song_id, 'played_at' => \Carbon\Carbon::parse($r->played_at)->diffForHumans()]; })->values(),
    'recentSongs' => $recentSongs->values(),
    'followedArtists' => $followedArtists,
    'favoriteIds' => $me ? $me->favoriteSongs->pluck('id')->values() : [],
    'playlists' => $me ? $me->playlists()->get(['id', 'name', 'cover_path'])->values() : [],
    'shellPage' => 'profile',
];
@endphp
@json($props)
@endsection

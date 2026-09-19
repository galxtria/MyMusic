@extends('layouts.app')

@section('react-page', 'admin.dashboard')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['id' => $me->id, 'name' => $me->name, 'email' => $me->email, 'role' => $me->role] : null,
    'isAdmin' => true,
    'stats' => [
        'totalSongs' => $totalSongs,
        'totalUsers' => $totalUsers,
        'totalPlays' => $totalPlays,
        'totalPlaylists' => $totalPlaylists,
    ],
    'playsPerDay' => $days,
    'topSongs' => $topSongs->values(),
    'topUsers' => $topUsers->values(),
    'genreDist' => $genreDist->values(),
    'dupArtists' => $dupArtists ?? [],
    'favoriteIds' => [],
    'playlists' => [],
    'shellPage' => 'admin',
];
@endphp
@json($props)
@endsection

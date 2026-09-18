@extends('layouts.app')

@section('react-page', 'admin.songs.index')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['id' => $me->id, 'name' => $me->name, 'email' => $me->email, 'role' => $me->role] : null,
    'isAdmin' => true,
    'songs' => $songs->getCollection()->values()->map(function ($s) {
        return array_merge($s->toArray(), ['uploaded' => $s->created_at ? $s->created_at->diffForHumans() : '']);
    })->values(),
    'stats' => [
        'adminName' => $me->name,
        'totalSongs' => $songs->total(),
        'genreCount' => \App\Models\Song::distinct('genre')->count('genre'),
        'artistCount' => \App\Models\Song::distinct('artist')->count('artist'),
    ],
    'pagination' => [
        'current' => $songs->currentPage(),
        'lastPage' => $songs->lastPage(),
        'prev' => $songs->previousPageUrl(),
        'next' => $songs->nextPageUrl(),
        'total' => $songs->total(),
    ],
    'search' => request('search', ''),
    'favoriteIds' => [],
    'playlists' => [],
    'shellPage' => 'admin',
];
@endphp
@json($props)
@endsection

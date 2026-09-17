@extends('layouts.app')

@section('react-page', 'library')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['name' => $me->name, 'role' => $me->role] : null,
    'isAdmin' => $me && $me->role === 'admin',
    'playlists' => $playlists->map(function ($pl) {
        return ['id' => $pl->id, 'name' => $pl->name, 'cover_path' => $pl->cover_path, 'songs_count' => $pl->songs_count ?? $pl->songs->count()];
    })->values(),
    'favoritesCount' => $me ? $me->favoriteSongs()->count() : 0,
    'favoriteIds' => $me ? $me->favoriteSongs->pluck('id')->values() : [],
    'shellPage' => 'library',
];
@endphp
@json($props)
@endsection

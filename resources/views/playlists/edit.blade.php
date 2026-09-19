@extends('layouts.app')

@section('react-page', 'playlist.edit')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['id' => $me->id, 'name' => $me->name, 'email' => $me->email, 'role' => $me->role] : null,
    'isAdmin' => $me && $me->role === 'admin',
    'playlist' => ['id' => $playlist->id, 'name' => $playlist->name, 'description' => $playlist->description, 'cover_path' => $playlist->cover_path, 'is_public' => (bool) $playlist->is_public],
    'errors' => $errors->toArray(),
    'favoriteIds' => $me ? $me->favoriteSongs->pluck('id')->values() : [],
    'playlists' => $me ? $me->playlists()->get(['id', 'name', 'cover_path'])->values() : [],
    'shellPage' => 'library',
];
@endphp
@json($props)
@endsection

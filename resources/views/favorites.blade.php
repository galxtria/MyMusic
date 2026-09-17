@extends('layouts.app')

@section('react-page', 'favorites')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['name' => $me->name, 'role' => $me->role] : null,
    'isAdmin' => $me && $me->role === 'admin',
    'songs' => $songs->values(),
    'favoriteIds' => $songs->pluck('id')->values(),
    'playlists' => $me ? $me->playlists()->get(['id', 'name', 'cover_path'])->values() : [],
    'shellPage' => 'favorites',
];
@endphp
@json($props)
@endsection

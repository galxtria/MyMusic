@extends('layouts.app')

@section('react-page', 'favorites')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['id' => $me->id, 'name' => $me->name, 'email' => $me->email, 'role' => $me->role] : null,
    'isAdmin' => $me && $me->role === 'admin',
    'songs' => $songs->values(),
    'favoriteIds' => $songs->pluck('id')->values(),
    'playlists' => $me ? $me->playlists()->get(['id', 'name', 'cover_path'])->values() : [],
    'shellPage' => 'favorites',
];
@endphp
@json($props)
@endsection

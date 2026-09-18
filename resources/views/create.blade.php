@extends('layouts.app')

@section('react-page', 'create')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['id' => $me->id, 'name' => $me->name, 'email' => $me->email, 'role' => $me->role] : null,
    'isAdmin' => $me && $me->role === 'admin',
    'errors' => $errors->toArray(),
    'favoriteIds' => $me ? $me->favoriteSongs->pluck('id')->values() : [],
    'playlists' => $me ? $me->playlists()->get(['id', 'name', 'cover_path'])->values() : [],
    'shellPage' => 'create',
];
@endphp
@json($props)
@endsection

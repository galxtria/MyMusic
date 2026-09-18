@extends('layouts.app')

@section('react-page', 'admin.songs.create')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['id' => $me->id, 'name' => $me->name, 'email' => $me->email, 'role' => $me->role] : null,
    'isAdmin' => true,
    'song' => null,
    'errors' => $errors->toArray(),
    'favoriteIds' => [],
    'playlists' => [],
    'shellPage' => 'admin',
];
@endphp
@json($props)
@endsection

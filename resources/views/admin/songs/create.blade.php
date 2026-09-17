@extends('layouts.app')

@section('react-page', 'admin.songs.create')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['name' => $me->name, 'role' => $me->role] : null,
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

@extends('layouts.app')

@section('react-page', 'admin.tools.import')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['id' => $me->id, 'name' => $me->name, 'email' => $me->email, 'role' => $me->role] : null,
    'isAdmin' => true,
    'favoriteIds' => [],
    'playlists' => [],
    'shellPage' => 'admin',
];
@endphp
@json($props)
@endsection

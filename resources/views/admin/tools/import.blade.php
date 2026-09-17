@extends('layouts.app')

@section('react-page', 'admin.tools.import')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['name' => $me->name, 'role' => $me->role] : null,
    'isAdmin' => true,
    'favoriteIds' => [],
    'playlists' => [],
    'shellPage' => 'admin',
];
@endphp
@json($props)
@endsection

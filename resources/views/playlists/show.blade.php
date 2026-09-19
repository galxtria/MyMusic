@extends('layouts.app')

@section('react-page', 'playlist.show')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['id' => $me->id, 'name' => $me->name, 'email' => $me->email, 'role' => $me->role] : null,
    'isAdmin' => $me && $me->role === 'admin',
    'playlist' => [
        'id' => $playlist->id,
        'name' => $playlist->name,
        'description' => $playlist->description,
        'cover_path' => $playlist->cover_path,
        'is_public' => (bool) $playlist->is_public,
        'share_token' => $playlist->share_token,
        'share_url' => $playlist->share_token ? url('/p/' . $playlist->share_token) : null,
        'created_at' => $playlist->created_at ? $playlist->created_at->format('M d, Y') : '',
        'songs' => $playlist->songs->values(),
    ],
    'isOwner' => $isOwner ?? true,
    'ownerName' => $ownerName ?? ($me ? $me->name : '-'),
    'favoriteIds' => $favoriteIds ?? [],
    'playlists' => $me ? $me->playlists()->get(['id', 'name', 'cover_path'])->values() : [],
    'shellPage' => 'library',
];
@endphp
@json($props)
@endsection

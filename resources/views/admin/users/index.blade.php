@extends('layouts.app')

@section('react-page', 'admin.users.index')

@section('react-props')
@php
$me = auth()->user();
$props = [
    'user' => $me ? ['id' => $me->id, 'name' => $me->name, 'email' => $me->email, 'role' => $me->role] : null,
    'isAdmin' => true,
    'users' => $users->getCollection()->values()->map(function ($u) {
        return [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'role' => $u->role,
            'joined' => $u->created_at ? $u->created_at->format('M d, Y') : '',
        ];
    })->values(),
    'stats' => [
        'totalUsers' => $users->total(),
        'adminCount' => \App\Models\User::where('role', 'admin')->count(),
        'newToday' => \App\Models\User::whereDate('created_at', \Carbon\Carbon::today())->count(),
    ],
    'pagination' => [
        'current' => $users->currentPage(),
        'lastPage' => $users->lastPage(),
        'prev' => $users->previousPageUrl(),
        'next' => $users->nextPageUrl(),
        'total' => $users->total(),
    ],
    'search' => request('search', ''),
    'favoriteIds' => [],
    'playlists' => [],
    'shellPage' => 'admin',
];
@endphp
@json($props)
@endsection

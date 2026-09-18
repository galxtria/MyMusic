@extends('layouts.app')

@section('react-page', 'welcome')

@section('react-props')
@php
$props = [
    'user' => auth()->check() ? ['id' => auth()->user()->id, 'name' => auth()->user()->name, 'email' => auth()->user()->email, 'role' => auth()->user()->role] : null,
];
@endphp
@json($props)
@endsection

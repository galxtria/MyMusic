@extends('layouts.app')

@section('react-page', 'welcome')

@section('react-props')
@php
$props = [
    'user' => auth()->check() ? ['name' => auth()->user()->name, 'role' => auth()->user()->role] : null,
];
@endphp
@json($props)
@endsection

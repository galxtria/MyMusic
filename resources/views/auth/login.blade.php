@extends('layouts.app')

@section('react-page', 'login')

@section('react-props')
@php
$props = ['errors' => $errors->toArray()];
@endphp
@json($props)
@endsection

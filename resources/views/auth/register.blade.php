@extends('layouts.app')

@section('react-page', 'register')

@section('react-props')
@php
$props = ['errors' => $errors->toArray()];
@endphp
@json($props)
@endsection

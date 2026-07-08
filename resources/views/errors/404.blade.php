@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Sorry, the page you are looking for could not be found. It might have been moved or deleted.'))
@section('icon')
    <i class="fa-solid fa-map-location-dot text-4xl"></i>
@endsection

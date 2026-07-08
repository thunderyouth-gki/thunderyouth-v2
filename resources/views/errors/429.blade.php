@extends('errors::minimal')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('You have made too many requests. Please wait a moment and try again later.'))
@section('icon')
    <i class="fa-solid fa-stopwatch text-4xl"></i>
@endsection

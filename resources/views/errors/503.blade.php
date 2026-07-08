@extends('errors::minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __($exception->getMessage() ?: 'We are currently performing some maintenance. Please check back soon.'))
@section('icon')
    <i class="fa-solid fa-screwdriver-wrench text-4xl"></i>
@endsection

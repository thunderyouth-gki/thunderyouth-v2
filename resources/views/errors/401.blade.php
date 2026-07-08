@extends('errors::minimal')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('You are not authorized to access this resource. Please log in.'))
@section('icon')
    <i class="fa-solid fa-lock text-4xl"></i>
@endsection

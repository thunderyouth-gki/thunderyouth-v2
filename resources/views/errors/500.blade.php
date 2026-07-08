@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Whoops, something went wrong on our servers. Please try again later.'))
@section('icon')
    <i class="fa-solid fa-server text-4xl text-red-500"></i>
@endsection

@extends('errors::minimal')

@section('title', __('Bad Gateway'))
@section('code', '502')
@section('message', __('The server encountered a temporary error and could not complete your request.'))
@section('icon')
    <i class="fa-solid fa-network-wired text-4xl"></i>
@endsection

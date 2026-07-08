@extends('errors::minimal')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'You do not have permission to access this page.'))
@section('icon')
    <i class="fa-solid fa-ban text-4xl"></i>
@endsection

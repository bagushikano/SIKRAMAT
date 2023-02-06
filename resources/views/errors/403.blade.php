@extends('errors::minimal')

@section('title', __('SIKRAMAT | Forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Forbidden'))

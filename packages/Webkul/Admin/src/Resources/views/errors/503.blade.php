@extends('admin::errors.illustrated-layout')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('Service is currently unavailable, please try again later.'))

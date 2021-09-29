@extends('admin::errors.illustrated-layout')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Something went wrong, please try again later.'))

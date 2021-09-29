@extends('admin::errors.illustrated-layout')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('You don\'t have necessary permissions to perform this action.'))

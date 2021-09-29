@extends('admin::errors.illustrated-layout')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'You are forbidden to do this action.'))

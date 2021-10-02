@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.tags.edit-title') }}
@stop

@section('css')
    <style>
        .color-item {
            position: relative;
            display: inline-block;
            margin: 10px 5px 5px 0px;
        }

        .color-item input {
            position: absolute;
            top: 4px;
            left: 1px;
            opacity: 0;
            z-index: 100;
            cursor: pointer;
        }

        .color-item label {
            width: 25px;
            height: 25px;
            margin-right: 3px;
            border-radius: 50%;
            cursor: pointer;
            display: inline-block;
            box-shadow: 0px 4px 15.36px 0.75px rgb(0 0 0 / 10%), 0px 2px 6px 0px rgb(0 0 0 / 15%);
            transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .color-item input:checked + label {
            border: solid 3px #FFFFFF;
        }
    </style>
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        {!! view_render_event('admin.settings.tags.edit.header.before') !!}

        <div class="page-header">
            
            {{ Breadcrumbs::render('settings.tags.edit', $tag) }}

            <div class="page-title">
                <h1>{{ __('admin::app.settings.tags.edit-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.settings.tags.edit.header.after') !!}

        <form action="{{ route('admin.settings.tags.update', $tag->id) }}" method="POST" @submit.prevent="onSubmit">
            <div class="page-content">
                <div class="form-container">
                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.settings.tags.edit.form_buttons.before') !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.tags.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.tags.index') }}">
                                {{ __('admin::app.layouts.back') }}
                            </a>

                            {!! view_render_event('admin.settings.tags.edit.form_buttons.after') !!}
                        </div>

                        <div class="panel-body tag-container">
                            {!! view_render_event('admin.settings.tags.edit.form_controls.before') !!}

                            @csrf()
                                
                            <input name="_method" type="hidden" value="PUT">

                            <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label class="required">
                                    {{ __('admin::app.settings.tags.name') }}
                                </label>
            
                                <input
                                    type="text"
                                    name="name"
                                    class="control"
                                    placeholder="{{ __('admin::app.settings.tags.name') }}"
                                    v-validate="'required'"
                                    data-vv-as="{{ __('admin::app.settings.tags.name') }}"
                                    value="{{ $tag->name }}"
                                />
            
                                <span class="control-error" v-if="errors.has('name')">
                                    @{{ errors.first('name') }}
                                </span>
                            </div>

                            <div class="form-group">
                                <label>{{ __('admin::app.settings.tags.color') }}</label>
                                
                                <div class="color-list">
                                    <span class="color-item">
                                        <input type="radio" id="337CFF" name="color" value="#337CFF" {{ $tag->color == "#337CFF" ? 'checked' : '' }}>
                                        <label for="337CFF" style="background: #337CFF;"></label>
                                    </span>
            
                                    <span class="color-item">
                                        <input type="radio" id="FEBF00" name="color" value="#FEBF00" {{ $tag->color == "#FEBF00" ? 'checked' : '' }}>
                                        <label for="FEBF00" style="background: #FEBF00;"></label>
                                    </span>
            
                                    <span class="color-item">
                                        <input type="radio" id="E5549F" name="color" value="#E5549F" {{ $tag->color == "#E5549F" ? 'checked' : '' }}>
                                        <label for="E5549F" style="background: #E5549F;"></label>
                                    </span>
            
                                    <span class="color-item">
                                        <input type="radio" id="27B6BB" name="color" value="#27B6BB" {{ $tag->color == "#27B6BB" ? 'checked' : '' }}>
                                        <label for="27B6BB" style="background: #27B6BB;"></label>
                                    </span>
            
                                    <span class="color-item">
                                        <input type="radio" id="FB8A3F" name="color" value="#FB8A3F" {{ $tag->color == "#FB8A3F" ? 'checked' : '' }}>
                                        <label for="FB8A3F" style="background: #FB8A3F;"></label>
                                    </span>
            
                                    <span class="color-item">
                                        <input type="radio" id="43AF52" name="color" value="#43AF52" {{ $tag->color == "#337CFF" ? 'checked' : '' }}>
                                        <label for="43AF52" style="background: #43AF52;"></label>
                                    </span>
                                </div>
                            </div>

                            {!! view_render_event('admin.settings.tags.edit.form_controls.after') !!}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop
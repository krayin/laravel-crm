@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.sources.edit-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        <div class="page-header">
            
            {{ Breadcrumbs::render('settings.sources.edit', $source) }}

            <div class="page-title">
                <h1>{{ __('admin::app.settings.sources.edit-title') }}</h1>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.sources.update', ['id' => $source->id]) }}" @submit.prevent="onSubmit">
            <div class="page-content">
                <div class="form-container">
                    <div class="panel">
                        <div class="panel-header">
                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.sources.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.sources.index') }}">
                                {{ __('admin::app.layouts.back') }}
                            </a>
                        </div>

                        <div class="panel-body">
                            @csrf()

                            <input name="_method" type="hidden" value="PUT">
                            
                            <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label>
                                    {{ __('admin::app.layouts.name') }}
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="control"
                                    v-validate="'required'"
                                    value="{{ $source->name }}"
                                    data-vv-as="{{ __('admin::app.layouts.name') }}"
                                    placeholder="{{ __('admin::app.layouts.name') }}"
                                />

                                <span class="control-error" v-if="errors.has('name')">
                                    @{{ errors.first('name') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop
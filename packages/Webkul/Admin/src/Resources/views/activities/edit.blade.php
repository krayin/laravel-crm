@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.activities.edit-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        {!! view_render_event('admin.activities.edit.header.before', ['activity' => $activity]) !!}

        <div class="page-header">

            {{ Breadcrumbs::render('activities.edit', $activity) }}

            <div class="page-title">
                <h1>{{ __('admin::app.activities.edit-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.activities.edit.header.after', ['activity' => $activity]) !!}

        <form method="POST" action="{{ route('admin.activities.update', $activity->id) }}" @submit.prevent="onSubmit">

            <div class="page-content">
                <div class="form-container">

                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.activities.edit.form_buttons.before', ['activity' => $activity]) !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.activities.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.activities.index') }}">{{ __('admin::app.activities.back') }}</a>

                            {!! view_render_event('admin.activities.edit.form_buttons.after', ['activity' => $activity]) !!}
                        </div>
        
                        <div class="panel-body">
                            {!! view_render_event('admin.activities.edit.form_controls.before', ['activity' => $activity]) !!}

                            @csrf()

                            <input name="_method" type="hidden" value="PUT">
        
                            <div class="form-group" :class="[errors.has('title') ? 'has-error' : '']">
                                <label for="comment" class="required">{{ __('admin::app.activities.title-control') }}</label>

                                <input class="control" v-validate="'required'" name="title" value="{{ old('title') ?: $activity->title }}" data-vv-as="&quot;{{ __('admin::app.activities.title-control') }}&quot;"/>
        
                                <span class="control-error" v-if="errors.has('title')">@{{ errors.first('title') }}</span>
                            </div>
                
                            <div class="form-group" :class="[errors.has('type') ? 'has-error' : '']">
                                <label for="type" class="required">{{ __('admin::app.activities.type') }}</label>
        
                                <?php $selectedOption = old('type') ?: $activity->type ?>

                                <select v-validate="'required'" class="control" name="type" data-vv-as="&quot;{{ __('admin::app.activities.type') }}&quot;">
                                    <option value=""></option>
                                    <option value="call" {{ $selectedOption == 'call' ? 'selected' : '' }}>{{ __('admin::app.activities.call') }}</option>
                                    <option value="meeting" {{ $selectedOption == 'meeting' ? 'selected' : '' }}>{{ __('admin::app.activities.meeting') }}</option>
                                    <option value="lunch" {{ $selectedOption == 'lunch' ? 'selected' : '' }}>{{ __('admin::app.activities.lunch') }}</option>
                                </select>
        
                                <span class="control-error" v-if="errors.has('type')">@{{ errors.first('type') }}</span>
                            </div>
        
                            <div class="form-group">
                                <label for="comment">{{ __('admin::app.activities.description') }}</label>
                                <textarea class="control" id="activity-comment" name="comment">{{ old('comment') ?: $activity->comment }}</textarea>
                            </div>
        
                            <div class="form-group" :class="[errors.has('schedule_from') || errors.has('schedule_to') ? 'has-error' : '']">
                                <label for="schedule_from" class="required">{{ __('admin::app.activities.schedule') }}</label>
        
                                <div class="input-group">
                                    <datetime>
                                        <input type="text" name="schedule_from" value="{{ old('schedule_from') ?: $activity->schedule_from }}" class="control" v-validate="'required'" data-vv-as="&quot;{{ __('admin::app.activities.from') }}&quot;" placeholder="{{ __('admin::app.activities.from') }}">
        
                                        <span class="control-error" v-if="errors.has('schedule_from')">@{{ errors.first('schedule_from') }}</span>
                                    </datetime>
        
                                    <datetime>
                                        <input type="text" name="schedule_to" value="{{ old('schedule_to') ?: $activity->schedule_to }}" class="control" v-validate="'required'" data-vv-as="&quot;{{ __('admin::app.activities.to') }}&quot;" placeholder="{{ __('admin::app.activities.to') }}">
        
                                        <span class="control-error" v-if="errors.has('schedule_to')">@{{ errors.first('schedule_to') }}</span>
                                    </datetime>
                                </div>
                            </div>
        
                            @include ('admin::common.custom-attributes.edit.multi-lookup')
        
                            <div class="form-group">
                                <label for="participants">{{ __('admin::app.activities.participants') }}</label>
        
                                <multi-lookup-component :attribute="{'id': 20, 'code': 'participants[]', 'name': 'Participants'}" :data='@json($activity->participants)'></multi-lookup-component>
                            </div>

                            {!! view_render_event('admin.activities.edit.form_controls.after', ['activity' => $activity]) !!}

                        </div>
                    </div>

                </div>

            </div>

        </form>

    </div>
@stop
@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.contacts.persons.edit-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">

        {!! view_render_event('admin.contacts.persons.edit.header.before', ['person' => $person]) !!}

        <div class="page-header">

            {{ Breadcrumbs::render('contacts.persons.edit', $person) }}

            <div class="page-title">
                <h1>{{ __('admin::app.contacts.persons.edit-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.contacts.persons.edit.header.after', ['person' => $person]) !!}

        <form method="POST" action="{{ route('admin.contacts.persons.update', $person->id) }}" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-content">
                <div class="form-container">

                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.contacts.persons.edit.form_buttons.before', ['person' => $person]) !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.contacts.persons.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.contacts.persons.index') }}">{{ __('admin::app.contacts.persons.back') }}</a>

                            {!! view_render_event('admin.contacts.persons.edit.form_buttons.after', ['person' => $person]) !!}
                        </div>
        
                        <div class="panel-body">
                            {!! view_render_event('admin.contacts.persons.edit.form_controls.before', ['person' => $person]) !!}

                            @csrf()
                            
                            <input name="_method" type="hidden" value="PUT">
                
                            @include('admin::common.custom-attributes.edit', [
                                'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    'entity_type' => 'persons',
                                ]),
                                'entity'           => $person,
                            ])

                            {!! view_render_event('admin.contacts.persons.edit.form_controls.after', ['person' => $person]) !!}

                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
@stop
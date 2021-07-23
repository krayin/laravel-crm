@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.configuration.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        <form method="POST" action="" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-header">

                {{ Breadcrumbs::render('configuration.slug', request('slug')) }}

                <div class="page-title">
                    <h1>
                        {{ __('admin::app.configuration.title') }}
                    </h1>
                </div>
            </div>

            <div class="page-content">
                <div class="form-container">

                    <div class="panel">
                        <div class="panel-header">
                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.configuration.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.users.index') }}">
                                {{ __('admin::app.settings.users.back') }}
                            </a>
                        </div>
                    
                        @csrf()

                        @if ($groups = \Illuminate\Support\Arr::get(app('core_config')->items, request()->route('slug') . '.children'))

                            <tabs>
                                @foreach ($groups as $key => $item)

                                    <tab :name="'{{ __($item['name']) }}'">

                                        @foreach ($item['fields'] as $field)

                                            @include ('admin::configuration.field-type', ['field' => $field])

                                        @endforeach

                                    </tab>

                                @endforeach
                            </tabs>

                        @endif

                    </div>

                </div>
            </div>

        </form>
    </div>
@stop
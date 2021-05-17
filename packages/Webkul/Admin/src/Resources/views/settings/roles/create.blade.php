@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.roles.create_role') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        <div class="page-header">
            <h6 class="breadcrumbs">
                Home/Leads/ 
            </h6>

            <div class="page-title">
                <h1>{{ __('admin::app.settings.roles.create_role') }}</h1>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.roles.store') }}" @submit.prevent="onSubmit">
            <div class="page-content">
                <div class="form-container">
                    <div class="panel">
                        <div class="panel-header">
                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.roles.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.roles.index') }}">
                                {{ __('admin::app.layouts.back') }}
                            </a>
                        </div>

                        <div class="panel-body">
                            @csrf()
                            
                            <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label>
                                    {{ __('admin::app.layouts.name') }}
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="control"
                                    v-validate="'required'"
                                    data-vv-as="{{ __('admin::app.layouts.name') }}"
                                    placeholder="{{ __('admin::app.layouts.name') }}"
                                />

                                <span class="control-error" v-if="errors.has('name')">
                                    @{{ errors.first('name') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('description') ? 'has-error' : '']">
                                <label>
                                    {{ __('admin::app.settings.roles.description') }}
                                </label>

                                <textarea
                                    class="control"
                                    name="description"
                                    v-validate="'required'"
                                    data-vv-as="{{ __('admin::app.settings.roles.description') }}"
                                    placeholder="{{ __('admin::app.settings.roles.description') }}"
                                >
                                </textarea>

                                <span class="control-error" v-if="errors.has('description')">
                                    @{{ errors.first('description') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('permission_type') ? 'has-error' : '']">
                                <label>
                                    {{ __('admin::app.settings.roles.permission_type') }}
                                </label>

                                <select
                                    class="control"
                                    id="permission_type"
                                    name="permission_type"
                                    v-validate="'required'"
                                    data-vv-as="{{ __('admin::app.settings.roles.role') }}"
                                >
                                    <option value="custom">
                                        {{ __('admin::app.settings.roles.custom') }}
                                    </option>

                                    <option value="all">
                                        {{ __('admin::app.settings.roles.all') }}
                                    </option>
                                </select>

                                <span class="control-error" v-if="errors.has('permission_type')">
                                    @{{ errors.first('permission_type') }}
                                </span>
                            </div>

                            <div class="control-group tree-wrapper">
                                <tree-view value-field="key" id-field="key" items='@json($acl->items)'></tree-view>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#permission_type').on('change', function(event) {
                if ($(event.target).val() == 'custom') {
                    $('.tree-wrapper').removeClass('hide')
                } else {
                    $('.tree-wrapper').addClass('hide')
                }

            })
        });
    </script>
@endpush
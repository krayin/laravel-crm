@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.roles.edit-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        {!! view_render_event('admin.settings.roles.edit.header.before', ['role' => $role]) !!}

        <div class="page-header">
            
            {{ Breadcrumbs::render('settings.roles.edit', $role) }}

            <div class="page-title">
                <h1>{{ __('admin::app.settings.roles.edit-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.settings.roles.edit.header.after', ['role' => $role]) !!}

        <form method="POST" action="{{ route('admin.settings.roles.update', ['id' => $role->id]) }}" @submit.prevent="onSubmit">
            <div class="page-content">
                <div class="form-container">
                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.settings.roles.edit.form_buttons.before', ['role' => $role]) !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.roles.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.roles.index') }}">
                                {{ __('admin::app.layouts.back') }}
                            </a>

                            {!! view_render_event('admin.settings.roles.edit.form_buttons.after', ['role' => $role]) !!}
                        </div>

                        <div class="panel-body">
                            {!! view_render_event('admin.settings.roles.edit.form_controls.before', ['role' => $role]) !!}

                            @csrf()

                            <input name="_method" type="hidden" value="PUT">
                            
                            <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label class="required">
                                    {{ __('admin::app.layouts.name') }}
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="control"
                                    value="{{ old('name') ?? $role->name }}"
                                    placeholder="{{ __('admin::app.layouts.name') }}"
                                    v-validate="'required'"
                                    data-vv-as="{{ __('admin::app.layouts.name') }}"
                                />

                                <span class="control-error" v-if="errors.has('name')">
                                    @{{ errors.first('name') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('description') ? 'has-error' : '']">
                                <label class="required">
                                    {{ __('admin::app.settings.roles.description') }}
                                </label>

                                <textarea
                                    class="control"
                                    name="description"
                                    placeholder="{{ __('admin::app.settings.roles.description') }}"
                                    v-validate="'required'"
                                    data-vv-as="{{ __('admin::app.settings.roles.description') }}"
                                >{{ old('description') ?? $role->description }}</textarea>

                                <span class="control-error" v-if="errors.has('description')">
                                    @{{ errors.first('description') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('permission_type') ? 'has-error' : '']">
                                <label class="required">
                                    {{ __('admin::app.settings.roles.permission_type') }}
                                </label>

                                <?php $selectedOption = old('permission_type') ?: $role->permission_type ?>

                                <select
                                    class="control"
                                    name="permission_type"
                                    id="permission_type"
                                    v-validate="'required'"
                                    data-vv-as="{{ __('admin::app.settings.roles.role') }}"
                                >
                                    <option value="custom" {{ $selectedOption == 'custom' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.roles.custom') }}
                                    </option>

                                    <option value="all" {{ $selectedOption == 'all' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.roles.all') }}
                                    </option>
                                </select>

                                <span class="control-error" v-if="errors.has('permission_type')">
                                    @{{ errors.first('permission_type') }}
                                </span>
                            </div>

                            <div class="control-group tree-wrapper {{ $selectedOption == 'all' ? 'hide' : '' }}">
                                <tree-view value-field="key" id-field="key" items='@json($acl->items)' value='@json($role->permissions)'></tree-view>
                            </div>

                            {!! view_render_event('admin.settings.roles.edit.form_controls.after', ['role' => $role]) !!}
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
@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.user.account.my_account') }}
@stop

@section('css')
    <style>
        .panel-header,
        .panel-body {
            margin: 0 auto;
            max-width: 800px;
        }
    </style>
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">

        <form method="POST" action="{{ route('admin.user.account.update') }}" enctype="multipart/form-data" @submit.prevent="onSubmit">
            <div class="page-content">
                <div class="form-container">

                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.user_profile.edit.form_buttons.before', ['user' => $user]) !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.user.account.update_details') }}
                            </button>

                            <a href="{{ route('admin.dashboard.index') }}">{{ __('admin::app.common.back') }}</a>

                            {!! view_render_event('admin.user_profile.edit.form_buttons.after', ['user' => $user]) !!}
                        </div>

                        <div class="panel-body">
                            {!! view_render_event('admin.user_profile.edit.form_controls.before', ['user' => $user]) !!}

                            @csrf()

                            <input name="_method" type="hidden" value="PUT">

                            <upload-profile-image></upload-profile-image>

                            @if(isset($user->image_url) && $user->image_url != NULL)
                                <input
                                    type="checkbox"
                                    name="remove_image"
                                />

                                <label for="remove" class="">
                                    {{ __('admin::app.user.account.remove-image') }}
                                </label>
                            @endif

                            <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label for="name" class="required">
                                    {{ __('admin::app.user.account.name') }}
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="control"
                                    id="name"
                                    value="{{ old('name') ?: $user->name }}"
                                    v-validate="'required'"
                                    data-vv-as="{{ __('admin::app.user.account.name') }}"
                                />

                                <span class="control-error" v-if="errors.has('name')">
                                    @{{ errors.first('name') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('email') ? 'has-error' : '']">
                                <label for="email" class="required">
                                    {{ __('admin::app.user.account.email') }}
                                </label>

                                <input
                                    type="text"
                                    name="email"
                                    class="control"
                                    id="email"
                                    value="{{ old('email') ?: $user->email }}"
                                    v-validate="'required'"
                                    data-vv-as="{{ __('admin::app.user.account.email') }}"
                                />

                                <span class="control-error" v-if="errors.has('email')">
                                    @{{ errors.first('email') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('current_password') ? 'has-error' : '']">
                                <label for="current_password" class="required">
                                    {{ __('admin::app.user.account.current_password') }}
                                </label>

                                <input
                                    type="password"
                                    name="current_password"
                                    class="control"
                                    id="current_password"
                                    v-validate="'required'"
                                    data-vv-as="{{ __('admin::app.user.account.current_password') }}"
                                />

                                <span class="control-error" v-if="errors.has('current_password')">
                                    @{{ errors.first('current_password') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('password') ? 'has-error' : '']">
                                <label for="password">
                                    {{ __('admin::app.user.account.password') }}
                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    class="control"
                                    id="password"
                                    ref="password"
                                    v-validate="'min:6'"
                                    data-vv-as="{{ __('admin::app.user.account.password') }}"
                                />

                                <span class="control-error" v-if="errors.has('password')">
                                    @{{ errors.first('password') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('password_confirmation') ? 'has-error' : '']">
                                <label for="confirm_password">
                                    {{ __('admin::app.user.account.confirm_password') }}
                                </label>

                                <input
                                    type="password"
                                    name="password_confirmation"
                                    class="control"
                                    id="confirm_password"
                                    v-validate="'min:6|confirmed:password'"
                                    data-vv-as="{{ __('admin::app.user.account.confirm_password') }}"
                                />

                                <span class="control-error" v-if="errors.has('password_confirmation')">
                                    @{{ errors.first('password_confirmation') }}
                                </span>
                            </div>

                            {!! view_render_event('admin.user_profile.edit.form_controls.after', ['user' => $user]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop


@push('scripts')
    <script type="text/x-template" id="upload-profile-image-template">
        <div class="form-group">
            <div class="image-upload-brick">
                <input
                    type="file"
                    name="image"
                    id="upload-profile"
                    ref="imageInput"
                    v-validate="'ext:jpeg,jpg,png'"
                    accept="image/*"
                    @change="addImageView($event)"
                >

                <i class="icon upload-icon"></i>

                <img class="preview" :src="imageData" v-if="imageData.length > 0">
            </div>

            <div class="image-info-brick">
                <span class="field-info">
                {{ __('admin::app.user.account.upload_image_pix') }} <br>
                {{ __('admin::app.user.account.upload_image_format') }}
                </span>
            </div>
        </div>
    </script>

    <script>
        Vue.component('upload-profile-image', {
            template: '#upload-profile-image-template',

            data: function() {
                return {
                    imageData: "{{ $user->image_url }}",
                }
            },

            methods: {
                addImageView () {
                    var imageInput = this.$refs.imageInput;

                    if (imageInput.files && imageInput.files[0]) {
                        if (imageInput.files[0].type.includes('image/')) {
                            var reader = new FileReader();

                            reader.onload = (e) => {
                                this.imageData = e.target.result;
                            }

                            reader.readAsDataURL(imageInput.files[0]);
                        } else {
                            imageInput.value = '';

                            alert('{{ __('admin::app.user.account.image_upload_message') }}');
                        }
                    }
                }
            }
        });
    </script>
@endpush

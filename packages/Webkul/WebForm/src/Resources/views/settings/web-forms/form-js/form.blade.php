<link rel="stylesheet" href="{{ asset('vendor/webkul/web-form/assets/css/web-form.css') }}">

<style>
    button.btn {
        background-color:<?= $webForm->form_submit_button_color ?> !important;
    }

    h1.web-form-title {
        color: <?= $webForm->form_title_color ?> !important;
    }

    .attribute-label {
        color: <?= $webForm->attribute_label_color ?> !important;
    }

    form#krayinWebForm {
        background-color:<?= $webForm->form_background_color ?>;
    }

    .anonymous-layout-container {
        background-color:<?= $webForm->background_color ?>;
    }
</style>


<div class="anonymous-layout-container">
    <div class="center-box">
        <div class="adjacent-center">
            <div class="title-box">
            <img src="{{ url('vendor/webkul/admin/assets/images/logo.svg') }}" alt="krayin">
                <h1 class="web-form-title">{{ $webForm->title }}</h1>
                <p>{{ $webForm->description }}</p>
            </div>

            <div class="panel">
                <div class="panel-body">
                    <div class="alert-wrapper" style="display: none">
                        <div class="alert success">
                            <p>This is message</p>
                            <span class="icon close-white-icon"></span>
                        </div>
                    </div>

                    <form id="krayinWebForm" method="post">
                        @foreach ($webForm->attributes as $attribute)
                            @php
                                $parentAttribute = $attribute->attribute;

                                $fieldName = $parentAttribute->entity_type . '[' . $parentAttribute->code . ']';
                            @endphp

                            <div class="form-group {{ $parentAttribute->type }}">
                                <label class="attribute-label {{ $attribute->is_required ? 'required' : '' }}" for="{{ $attribute->code }}" >
                                    {{ $attribute->name ?? $parentAttribute->name }}
                                </label>

                                @switch($parentAttribute->type)
                                    @case('text')
                                        <input
                                        type="text"
                                        name="{{ $fieldName }}"
                                        class="control"
                                        placeholder="{{ $attribute->placeholder }}"
                                        id="{{ $fieldName }}"
                                        {{ $parentAttribute->is_required ? 'required' : '' }}
                                        />

                                        @break;

                                    @case('date')
                                    @case('datetime')
                                        <input
                                        type="text"
                                        name="{{ $fieldName }}"
                                        class="control"
                                        id="{{ $fieldName }}"
                                        {{ $parentAttribute->is_required ? 'required' : '' }}
                                        />

                                        @break;

                                    @case('textarea')
                                        <textarea
                                        name="{{ $fieldName }}"
                                        class="control"
                                        id="{{ $fieldName }}"
                                        {{ $parentAttribute->is_required ? 'required' : '' }}
                                        ></textarea>

                                        @break;

                                    @case('email')
                                        <input
                                            type="email"
                                            name="{{ $fieldName }}[0][value]"
                                            class="control"
                                            placeholder="{{ $attribute->placeholder }}"
                                            id="{{ $fieldName }}"
                                            {{ $parentAttribute->is_required ? 'required' : '' }}
                                        />

                                        <input
                                        type="hidden"
                                        name="{{ $fieldName }}[0][label]"
                                        class="control"
                                        value="work"
                                        />

                                        @break;

                                    @case('phone')
                                        <input
                                        type="text"
                                        name="{{ $fieldName }}[0][value]"
                                        class="control"
                                        placeholder="{{ $attribute->placeholder }}"
                                        id="{{ $fieldName }}"
                                        {{ $parentAttribute->is_required ? 'required' : '' }}
                                        />

                                        <input
                                            type="hidden"
                                            name="{{ $fieldName }}[0][label]"
                                            class="control"
                                            value="work"
                                            />

                                        @break;

                                    @case('select')
                                    @case('lookup')
                                        <select
                                        class="control"
                                        id="{{ $fieldName }}"
                                        name="{{ $fieldName }}"
                                        {{ $parentAttribute->is_required ? 'required' : '' }}
                                        >
                                            @php
                                                $options = $parentAttribute->lookup_type
                                                    ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpOptions($parentAttribute->lookup_type)
                                                    : $parentAttribute->options()->orderBy('sort_order')->get();
                                            @endphp

                                            <option value="" selected="selected" disabled="disabled">{{ __('admin::app.settings.attributes.select') }}</option>

                                            @foreach ($options as $option)
                                                <option value="{{ $option->id }}">
                                                    {{ $option->name }}
                                                </option>
                                            @endforeach

                                        </select>

                                        @break;

                                    @case('multiselect')
                                        <select class="control" id="{{ $fieldName }}" name="{{ $fieldName }}[]" multiple>

                                            @php
                                                $options = $parentAttribute->lookup_type
                                                    ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpOptions($parentAttribute->lookup_type)
                                                    : $parentAttribute->options()->orderBy('sort_order')->get();
                                            @endphp

                                            @foreach ($options as $option)
                                                <option value="{{ $option->id }}">
                                                    {{ $option->name }}
                                                </option>
                                            @endforeach

                                        </select>

                                        @break;

                                    @case('file')
                                    @case('image')
                                        <input
                                        type="file"
                                        name="{{ $fieldName }}"
                                            class="control"
                                            id="{{ $fieldName }}"
                                            style="padding-top: 5px;"
                                            />

                                        @break;

                                    @case('boolean')
                                        <select class="control" id="{{ $fieldName }}" name="{{ $fieldName }}">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>

                                        @break;

                                    @case('address')

                                        @break;
                                @endswitch
                            </div>
                        @endforeach

                        <div class="button-group">
                            <button type="submit" class="btn btn-xl btn-primary">
                                {{ $webForm->submit_button_label }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
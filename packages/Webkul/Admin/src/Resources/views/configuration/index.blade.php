@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.configuration.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <form method="POST" action="" @submit.prevent="onSubmit" enctype="multipart/form-data">
            <div class="page-header">
                <div class="page-title">
                    <h1>
                        {{ __('admin::app.configuration.title') }}
                    </h1>
                </div>

                <div class="page-action">
                    <button type="submit" class="btn btn-md btn-primary">
                        {{ __('admin::app.configuration.save-btn-title') }}
                    </button>
                </div>
            </div>

            <div class="page-content">
                <div class="form-container">
                    
                    @csrf()

                    @php ($groups = \Illuminate\Support\Arr::get($config->items, request()->route('slug') . '.children.' . request()->route('slug2') . '.children'))

                    @if ($groups)

                        @foreach ($groups as $key => $item)

                            <accordian :title="'{{ __($item['name']) }}'" :active="true">
                                <div slot="body">

                                    @foreach ($item['fields'] as $field)

                                        @include ('admin::configuration.field-type', ['field' => $field])

                                        @php ($hint = $field['title'] . '-hint')
                                        @if ($hint !== __($hint))
                                            {{ __($hint) }}
                                        @endif

                                    @endforeach

                                </div>
                            </accordian>

                        @endforeach

                    @endif

                </div>
            </div>

        </form>
    </div>
@stop

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#channel-switcher, #locale-switcher').on('change', function (e) {
                $('#channel-switcher').val()
                var query = '?channel=' + $('#channel-switcher').val() + '&locale=' + $('#locale-switcher').val();

                window.location.href = "{{ route('admin.configuration.index', [request()->route('slug'), request()->route('slug2')]) }}" + query;
            })
        });
    </script>
@endpush
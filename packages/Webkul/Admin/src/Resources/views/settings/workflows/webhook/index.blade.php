@push('scripts')
    <script type="text/x-template" id="webhook-component-template">
            <div>
                <label>{{ __('admin::app.settings.workflows.webhook_heading') }}</label>
                <hr>
                <div class="form-group">
                    <label>{{ __('admin::app.settings.workflows.webhook_request_method') }}</label>
                    <select :name="['actions[' + index + '][hook][method]']" class="control">  
                        <option v-for='(text, method) in matchedAction.request_methods' :value="method">
                            @{{ text }}
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>{{ __('admin::app.settings.workflows.webhook_url') }}</label>
                    <textarea id="description" type="text" :name="['actions[' + index + '][hook][url]']" class="control"></textarea>
                </div>

                <div class="form-group">
                    <label>{{ __('admin::app.settings.workflows.webhook_encoding') }}</label>
                    <span v-for='(text, encoding) in matchedAction.encodings'>
                        <input type="radio" :name="['actions[' + index + '][hook][encoding]']" :value="encoding" class="control inline-radio">
                        @{{ text }}
                    </span>
                </div>

                <div class="form-group">
                    <label>{{ __('admin::app.settings.workflows.request_body') }}</label>
                    <tabs>
                    <tab name="{{ __('admin::app.settings.workflows.simple_body') }}" :selected="true">
                        @include('admin::settings.workflows.webhook.requests.simple')

                        <simple-request-component></simple-request-component>
                    </tab>
                    <tab name="{{ __('admin::app.settings.workflows.custom_body') }}">
                        <textarea id="description" :name="['actions[' + index + '][hook][custom]']" class="control"></textarea>
                    </tab>
                    </tabs>
                </div>
            </div>
    </script>

    <script>
        Vue.component('webhook-component', {

            template: '#webhook-component-template',

            inject: ['$validator'],

            data: function () {
                return {
                    index: this.$parent.index,
                    matchedAction: this.$parent.matchedAction
                }
            }
        });
    </script>
@endpush
@extends('admin::layouts.master')

@section('page_title')
    {{ $lead->title }}
@stop

@section('css')
    <style>
        .modal-container .modal-header {
            border: 0;
        }

        .modal-container .modal-body {
            padding: 0;
        }

        .content-container .content .page-header {
            margin-bottom: 30px;
        }
    </style>
@stop

@section('content-wrapper')

    <div class="content full-page">

        <div class="page-header">
            
            {{ Breadcrumbs::render('leads.view', $lead) }}

            <div class="page-title">
                <h1>
                    {{ $lead->title }}

                    <tags-component></tags-component>
                </h1>
            </div>

            <div class="page-action">
                <button class="btn btn-primary btn-md" @click="openModal('updateLeadModal')">Edit</button>
            </div>
        </div>

        <div class="page-content lead-view">
            
            <div class="lead-content-left">
                <div class="panel">
                    <div class="panel-header" style="padding-top: 0">
                        {{ __('admin::app.leads.details') }}
                    </div>

                    <div class="panel-body">
                        
                        <div class="custom-attribute-view">
                            @include('admin::common.custom-attributes.view', [
                                'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    'entity_type' => 'leads',
                                ]),
                                'entity'           => $lead,
                            ])
                        </div>

                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        {{ __('admin::app.leads.contact-person') }}
                    </div>
    
                    <div class="panel-body custom-attribute-view">
                        
                        <div class="attribute-value-row">
                            <div class="label">Name</div>
                
                            <div class="value">
                                <a href="{{ route('admin.contacts.persons.edit', $lead->person->id) }}" target="_blank">
                                    {{ $lead->person->name }}
                                </a>
                            </div>
                        </div>
                        
                        <div class="attribute-value-row">
                            <div class="label">Email</div>
                
                            <div class="value">
                                @include ('admin::common.custom-attributes.view.email', ['value' => $lead->person->emails])
                            </div>
                        </div>
                        
                        <div class="attribute-value-row">
                            <div class="label">Contact Numbers</div>
                
                            <div class="value">
                                @include ('admin::common.custom-attributes.view.phone', ['value' => $lead->person->contact_numbers])
                            </div>
                        </div>
                        
                        <div class="attribute-value-row">
                            <div class="label">Organization</div>
                
                            <div class="value">
                                @if ($lead->person->organization)
                                    <a href="{{ route('admin.contacts.organizations.edit', $lead->person->organization->id) }}" target="_blank">
                                        {{ $lead->person->organization->name }}
                                    </a>
                                @else
                                    {{ __('admin::app.common.not-available') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        {{ __('admin::app.leads.products') }}
                    </div>
    
                    <div class="panel-body" style="position: relative">
                        @if ($lead->products->count())
                            <div class="lead-product-list">

                                @foreach ($lead->products as $product)
                                    
                                    <div class="lead-product">
                                        <div class="top-control-group">
                                            <div class="form-group">
                                                <label>{{ __('admin::app.leads.item') }}</label>
                            
                                                <div class="control-faker">
                                                    {{ $product->name }}
                                                </div>
                                            </div>
                                        </div>
                            
                                        <div class="bottom-control-group" style="padding-right: 0;">
                                            <div class="form-group">
                                                <label>{{ __('admin::app.leads.price') }}</label>
                            
                                                <div class="control-faker">
                                                    {{ $product->price }}
                                                </div>
                                            </div>
                            
                                            <div class="form-group">
                                                <label>{{ __('admin::app.leads.quantity') }}</label>
                            
                                                <div class="control-faker">
                                                    {{ $product->quantity }}
                                                </div>
                                            </div>
                            
                                            <div class="form-group">
                                                <label>{{ __('admin::app.leads.amount') }}</label>
                            
                                                <div class="control-faker">
                                                    {{ $product->price * $product->quantity }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        @else
                            <div class="empty-record">
                                <img src="http://localhost/laravel/bagisto-crm/public/vendor/webkul/admin/assets/images/empty-table-icon.svg">
                                
                                <span>{{ __('admin::app.common.no-records-found') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lead-content-right">
    
                <stage-component></stage-component>

                <activity-action-component></activity-action-component>

                <activity-list-component></activity-list-component>
            </div>

        </div>
    </div>

    <form action="{{ route('admin.leads.update', $lead->id) }}" method="post" @submit.prevent="onSubmit">
        <modal id="updateLeadModal" :is-open="modalIds.updateLeadModal">
            <h3 slot="header-title">{{ __('admin::app.leads.edit-title') }}</h3>
            
            <div slot="header-actions">
                <button class="btn btn-sm btn-secondary-outline" @click="closeModal('updateLeadModal')">{{ __('admin::app.leads.cancel') }}</button>

                <button class="btn btn-sm btn-primary">{{ __('admin::app.leads.save-btn-title') }}</button>
            </div>

            <div slot="body">
                @csrf()
                
                <input name="_method" type="hidden" value="PUT">

                <tabs>
                    <tab name="{{ __('admin::app.leads.details') }}" :selected="true">
                        @include('admin::common.custom-attributes.edit', [
                            'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                'entity_type' => 'leads',
                            ]),
                            'entity'           => $lead,
                        ])
                    </tab>

                    <tab name="{{ __('admin::app.leads.contact-person') }}">
                        @include('admin::leads.common.contact')

                        <contact-component :data='@json($lead->person)'></contact-component>
                    </tab>

                    <tab name="{{ __('admin::app.leads.products') }}">
                        @include('admin::leads.common.products')

                        <product-list :data='@json($lead->products)'></product-list>
                    </tab>
                </tabs>
            </div>
        </modal>
    </form>
@stop

@push('scripts')
    <script src="{{ asset('vendor/webkul/admin/assets/js/tinyMCE/tinymce.min.js') }}"></script>

    <script type="text/x-template" id="tags-component-template">
        <div class="tags-container">
            <i class="icon tags-icon" @click="is_dropdown_open = ! is_dropdown_open"></i>

            <ul class="tag-list">
                <li v-for='(tag, index) in tags' :style="'background-color: ' + tag.color">
                    @{{ tag.name }} <i class="icon close-white-icon" @click="removeTag(tag)"></i>
                </li>
            </ul>

            <div class="tag-dropdown" v-if="is_dropdown_open">
                <div class="lookup-results" v-if="! show_form">
                    <ul>
                        <li class="control-list-item">
                            <div class="form-group">
                                <input type="text" class="control" v-model="term" v-on:keyup="search" placeholder="{{ __('admin::app.leads.search-tag') }}" autocomplete="off">

                                <i class="icon loader-active-icon" v-if="is_searching"></i>
                            </div>
                        </li>

                        <li v-for='(tag, index) in search_results' @click="addTag(tag)">
                            <span>@{{ tag.name }}</span>
                        </li>

                        <li v-if="! search_results.length && term.length && ! is_searching">
                            <span>{{ __('admin::app.common.no-result-found') }}</span>
                        </li>

                        <li class="action" @click="show_form = true">
                            <span>
                                + {{ __('admin::app.leads.add-tag') }}
                            </span> 
                        </li>
                    </ul>
                </div>

                <div class="form-container" v-else>
                    <form data-vv-scope="tag-form">
                        <div class="form-group" :class="[errors.has('tag-form.name') ? 'has-error' : '']">
                            <label class="required">{{ __('admin::app.leads.name') }}</label>
                            <input type="text" v-validate="'required'" name="name" v-model="tag.name" class="control" data-vv-as="&quot;{{ __('admin::app.leads.name') }}&quot;">
                            <span class="control-error" v-if="errors.has('tag-form.name')">@{{ errors.first('tag-form.name') }}</span>
                        </div>

                        <div class="form-group">
                            <label>{{ __('admin::app.leads.color') }}</label>
                            
                            <div class="color-list">
                                <span
                                    v-for='color in colors'
                                    :style="'background:' + color"
                                    :class="{active: tag.color == color}"
                                    @click="tag.color = color"
                                >
                                </span>
                            </div>
                        </div>

                        <div class="form-group button-group">
                            <button type="button" class="btn btn-sm btn-secondary-outline" @click="show_form = false">{{ __('admin::app.leads.cancel') }}</button>
                            <button type="button" class="btn btn-sm btn-primary" @click="createTag">{{ __('admin::app.leads.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </script>

    <script type="text/x-template" id="stage-component-template">
        <div>
            <div class="pipeline-stage-container">
                <ul class="pipeline-stages" :class="currentStage.code">
                    <li
                        class="stage"
                        v-for="(stage, index) in customStages"
                        :class="{ active: currentStage.id >= stage.id }"
                        @click="changeStage(stage)"
                        v-if="stage.code != 'won' && stage.code != 'lost'"
                    >
                        <span>@{{ stage.name }}</span>
                    </li>

                    <li class="stage">
                        <span class="dropdown-toggle">
                            {{ __('admin::app.leads.won-lost') }}
                            <i class="icon arrow-down-s-icon"></i>
                        </span>

                        <div class="dropdown-list">
                            <div class="dropdown-container">
                                <ul>
                                    <li @click="nextStageCode = 'won'; $root.openModal('updateLeadStageModal')">{{ __('admin::app.leads.won') }}</li>
                                    <li @click="nextStageCode = 'lost'; $root.openModal('updateLeadStageModal')">{{ __('admin::app.leads.lost') }}</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <form action="{{ route('admin.leads.update', $lead->id) }}" method="post" data-vv-scope="change-stage-form">
                <modal id="updateLeadStageModal" :is-open="$root.modalIds.updateLeadStageModal">
                    <h3 slot="header-title">{{ __('admin::app.leads.change-stage') }}</h3>
                    
                    <div slot="header-actions">
                        <button class="btn btn-sm btn-secondary-outline" @click="$root.closeModal('updateLeadStageModal')">{{ __('admin::app.leads.cancel') }}</button>

                        <button class="btn btn-sm btn-primary">{{ __('admin::app.leads.save-btn-title') }}</button>
                    </div>

                    <div slot="body" class="tabs-content">
                        @csrf()

                        <input name="_method" type="hidden" value="PUT">

                        <input type="hidden" name="lead_stage_id" :value="this[nextStageCode] && this[nextStageCode].id">

                        <div class="form-group" v-if="this[nextStageCode] && this[nextStageCode].code == 'lost'">
                            <label>{{ __('admin::app.leads.lost-reason') }}</label>

                            <textarea class="control" name="lost_reason"></textarea>
                        </div>

                        <div class="form-group" v-if="this[nextStageCode] && this[nextStageCode].code == 'won'">
                            <label>{{ __('admin::app.leads.won-value') }}</label>

                            <input type="text" name="lead_value" class="control" value="{{ $lead->lead_value }}" />
                        </div>

                        <div class="form-group">
                            <label>{{ __('admin::app.leads.closed-date') }}</label>

                            <date>
                                <input type="text" name="closed_at" class="control" />
                            </date>
                        </div>
                    </div>
                </modal>
            </form>
        </div>
    </script>

    <script type="text/x-template" id="activity-action-component-template">
        <tabs>
            <tab name="{{ __('admin::app.leads.note') }}" :selected="true">
                <form action="{{ route('admin.activities.store', $lead->id) }}" method="post" data-vv-scope="note-form" @submit.prevent="onSubmit($event, 'note-form')">

                    <input type="hidden" name="type" value="note">
                    @csrf()

                    <div class="form-group" :class="[errors.has('note-form.comment') ? 'has-error' : '']">
                        <label for="comment" class="required">{{ __('admin::app.leads.note') }}</label>
                        <textarea v-validate="'required'" class="control" id="note-comment" name="comment" data-vv-as="&quot;{{ __('admin::app.leads.note') }}&quot;">{{ old('comment') }}</textarea>
                        <span class="control-error" v-if="errors.has('note-form.comment')">@{{ errors.first('note-form.comment') }}</span>
                    </div>

                    <button type="submit" class="btn btn-md btn-primary">
                        {{ __('admin::app.leads.save') }}
                    </button>

                </form>
            </tab>

            <tab name="{{ __('admin::app.leads.activity') }}">
                <form action="{{ route('admin.activities.store', $lead->id) }}" method="post" data-vv-scope="activity-form" @submit.prevent="onSubmit($event, 'activity-form')">

                    @csrf()

                    <div class="form-group" :class="[errors.has('activity-form.type') ? 'has-error' : '']">
                        <label for="type" class="required">{{ __('admin::app.leads.type') }}</label>

                        <select v-validate="'required'" class="control" name="type" data-vv-as="&quot;{{ __('admin::app.leads.type') }}&quot;">
                            <option value=""></option>
                            <option value="call">{{ __('admin::app.leads.call') }}</option>
                            <option value="meeting">{{ __('admin::app.leads.meeting') }}</option>
                            <option value="lunch">{{ __('admin::app.leads.lunch') }}</option>
                            <option value="email">{{ __('admin::app.leads.email') }}</option>
                        </select>

                        <span class="control-error" v-if="errors.has('activity-form.type')">@{{ errors.first('activity-form.type') }}</span>
                    </div>

                    <div class="form-group">
                        <label for="comment">{{ __('admin::app.leads.description') }}</label>
                        <textarea class="control" id="activity-comment" name="comment">{{ old('comment') }}</textarea>
                    </div>

                    <div class="form-group" :class="[errors.has('activity-form.schedule_from') || errors.has('activity-form.schedule_to') ? 'has-error' : '']">
                        <label for="schedule_from" class="required">{{ __('admin::app.leads.schedule') }}</label>

                        <div class="input-group">
                            <datetime>
                                <input type="text" name="schedule_from" class="control" v-validate="'required'" data-vv-as="&quot;{{ __('admin::app.leads.from') }}&quot;" placeholder="{{ __('admin::app.leads.from') }}">

                                <span class="control-error" v-if="errors.has('activity-form.schedule_from')">@{{ errors.first('activity-form.schedule_from') }}</span>
                            </datetime>

                            <datetime>
                                <input type="text" name="schedule_to" class="control" v-validate="'required'" data-vv-as="&quot;{{ __('admin::app.leads.to') }}&quot;" placeholder="{{ __('admin::app.leads.to') }}">

                                <span class="control-error" v-if="errors.has('activity-form.schedule_to')">@{{ errors.first('activity-form.schedule_to') }}</span>
                            </datetime>
                        </div>

                    </div>

                    <button type="submit" class="btn btn-md btn-primary">
                        {{ __('admin::app.leads.save') }}
                    </button>

                </form>
            </tab>

            <tab name="{{ __('admin::app.leads.email') }}">
                <form action="{{ route('admin.mail.store') }}" method="post" data-vv-scope="email-form" @submit.prevent="onSubmit($event, 'email-form')" enctype="multipart/form-data">

                    @csrf()

                    <input type="hidden" name="lead_id" value="{{ $lead->id }}"/>

                    @include ('admin::common.custom-attributes.edit.email-tags')

                    <div class="form-group email-control-group" :class="[errors.has('email-form.reply_to[]') ? 'has-error' : '']">
                        <label for="to" class="required">{{ __('admin::app.leads.to') }}</label>

                        <email-tags-component control-name="reply_to[]" control-label="{{ __('admin::app.leads.to') }}" :validations="'required'"></email-tags-component>

                        <span class="control-error" v-if="errors.has('email-form.reply_to[]')">@{{ errors.first('email-form.reply_to[]') }}</span>

                        <div class="email-address-options">
                            <label @click="show_cc = ! show_cc">{{ __('admin::app.leads.cc') }}</label>
                            <label @click="show_bcc = ! show_bcc">{{ __('admin::app.leads.bcc') }}</label>
                        </div>
                    </div>

                    <div class="form-group email-control-group" :class="[errors.has('email-form.cc[]') ? 'has-error' : '']" v-if="show_cc">
                        <label for="cc">{{ __('admin::app.leads.cc') }}</label>

                        <email-tags-component control-name="cc[]" control-label="{{ __('admin::app.leads.cc') }}"></email-tags-component>

                        <span class="control-error" v-if="errors.has('email-form.cc[]')">@{{ errors.first('email-form.cc[]') }}</span>
                    </div>

                    <div class="form-group email-control-group" :class="[errors.has('email-form.bcc[]') ? 'has-error' : '']" v-if="show_bcc">
                        <label for="bcc">{{ __('admin::app.leads.bcc') }}</label>

                        <email-tags-component control-name="bcc[]" control-label="{{ __('admin::app.leads.bcc') }}"></email-tags-component>

                        <span class="control-error" v-if="errors.has('email-form.bcc[]')">@{{ errors.first('email-form.bcc[]') }}</span>
                    </div>
                    
                    <div class="form-group" :class="[errors.has('email-form.subject') ? 'has-error' : '']">
                        <label for="subject" class="required">{{ __('admin::app.leads.subject') }}</label>
                        <input type="text" v-validate="'required'" class="control" id="subject" name="subject" data-vv-as="&quot;{{ __('admin::app.leads.subject') }}&quot;">
                        <span class="control-error" v-if="errors.has('email-form.subject')">@{{ errors.first('email-form.subject') }}</span>
                    </div>
                    
                    <div class="form-group" :class="[errors.has('email-form.reply') ? 'has-error' : '']">
                        <label for="reply" class="required" style="margin-bottom: 10px">{{ __('admin::app.leads.reply') }}</label>
                        <textarea v-validate="'required'" class="control" id="reply" name="reply" data-vv-as="&quot;{{ __('admin::app.leads.reply') }}&quot;"></textarea>
                        <span class="control-error" v-if="errors.has('email-form.reply')">@{{ errors.first('email-form.reply') }}</span>
                    </div>

                    <div class="form-group">
                        <attachment-wrapper></attachment-wrapper>
                    </div>

                    <button type="submit" class="btn btn-md btn-primary">
                        {{ __('admin::app.leads.send') }}
                    </button>

                </form>
            </tab>

            <tab name="{{ __('admin::app.leads.files') }}">
                <form action="{{ route('admin.leads.file_upload', $lead->id) }}" method="post" data-vv-scope="file-form" @submit.prevent="onSubmit($event, 'file-form')" enctype="multipart/form-data">

                    <input type="hidden" name="type" value="file">
                    @csrf()

                    <div class="form-group">
                        <label for="name">{{ __('admin::app.leads.name') }}</label>
                        <input type="text" class="control" id="name" name="name">
                    </div>

                    <div class="form-group">
                        <label for="comment">{{ __('admin::app.leads.description') }}</label>
                        <textarea class="control" id="files-comment" name="comment">{{ old('comment') }}</textarea>
                    </div>

                    <div class="form-group" :class="[errors.has('file-form.file') ? 'has-error' : '']">
                        <label for="file" class="required">{{ __('admin::app.leads.file') }}</label>
                        <input type="file" v-validate="'required'" class="control" id="file" name="file" data-vv-as="&quot;{{ __('admin::app.leads.file') }}&quot;">
                        <span class="control-error" v-if="errors.has('file-form.file')">@{{ errors.first('file-form.file') }}</span>
                    </div>

                    <button type="submit" class="btn btn-md btn-primary">
                        {{ __('admin::app.leads.upload') }}
                    </button>

                </form>
            </tab>

            <tab name="{{ __('admin::app.leads.quote') }}">

                <a href="{{ route('admin.quotes.create', $lead->id) }}" class="btn btn-primary">{{ __('admin::app.leads.create-quote') }}</a>

            </tab>
        </tabs>
    </script>

    <script type="text/x-template" id="activity-list-component-template">
        <tabs class="activity-list">
            <tab v-for="type in types" :name="typeLabels[type]" :key="type" :selected="type == 'all'">

                <div v-for="subType in ['planned', 'done']" :class="subType + '-activities ' + type">

                    <div class="section-tag" v-if="type != 'note' && type != 'file'">
                        <span v-if="subType == 'planned'">{{ __('admin::app.leads.planned') }}</span>

                        <span v-else>{{ __('admin::app.leads.done') }}</span>

                        <hr/>
                    </div>

                    <div class="activity-item" v-for="activity in getActivities(type, subType)">
                        <div class="title">
                            <span v-if="activity.type == 'note'">
                                {{ __('admin::app.leads.note-added') }}
                            </span>
                            
                            <span v-else-if="activity.type == 'call'">
                                @{{ '{!! __('admin::app.leads.call-scheduled') !!}'.replace(':from', activity.schedule_from).replace(':to', activity.schedule_to) }}
                            </span>

                            <span v-else-if="activity.type == 'meeting'">
                                @{{ '{!! __('admin::app.leads.meeting-scheduled') !!}'.replace(':from', activity.schedule_from).replace(':to', activity.schedule_to) }}
                            </span>

                            <span v-else-if="activity.type == 'lunch'">
                                @{{ '{!! __('admin::app.leads.lunch-scheduled') !!}'.replace(':from', activity.schedule_from).replace(':to', activity.schedule_to) }}
                            </span>

                            <span v-else-if="activity.type == 'email'">
                                @{{ '{!! __('admin::app.leads.email-scheduled') !!}'.replace(':from', activity.schedule_from).replace(':to', activity.schedule_to) }}
                            </span>
                            
                            <span v-else-if="activity.type == 'file'">
                                {{ __('admin::app.leads.file-added') }}
                            </span>

                            <span class="icon ellipsis-icon dropdown-toggle"></span>

                            <div class="dropdown-list">
                                <div class="dropdown-container">
                                    <ul>
                                        <li v-if="! activity.is_done" @click="markAsDone(activity)">
                                            {{ __('admin::app.leads.mark-as-done') }}
                                        </li>
                                        {{-- <li @click="edit(activity)">
                                            {{ __('admin::app.leads.edit') }}
                                        </li> --}}
                                        <li @click="remove(activity)">
                                            {{ __('admin::app.leads.remove') }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="attachment" v-if="activity.file">
                            <i class="icon attachment-icon"></i>
                            <a :href="'{{ route('admin.leads.file_download') }}/' + activity.file.id" target="_blank">@{{ activity.file.name }}</a>
                        </div>

                        <div class="comment" v-if="activity.comment">
                            @{{ activity.comment }}
                        </div>

                        <div class="info">
                            @{{ activity.created_at | moment("Do MMMM YYYY, h:mm A") }}
                            <span class="seperator">·</span>
                            <a :href="'{{ route('admin.settings.users.edit') }}/' + activity.user.id" target="_blank">@{{ activity.user.name }}</a> 
                        </div>
                    </div>

                    <div class="empty-activities" v-if="! getActivities(type, subType).length">
                        <span v-if="subType == 'planned'">{{ __('admin::app.leads.empty-planned-activities') }}</span>

                        <span v-else>{{ __('admin::app.leads.empty-done-activities') }}</span>
                    </div>
                    
                </div>
            </tab>

            <tab name="Quotes">
                <div class="table lead-quote-list" style="padding: 5px">

                    <table>
                        <thead>
                            <tr>
                                <th class="sales-owner">{{ __('admin::app.leads.sales-owner') }}</th>
                                <th class="person">{{ __('admin::app.leads.person') }}</th>
                                <th class="quote-subject">{{ __('admin::app.leads.subject') }}</th>
                                <th class="expired-at">{{ __('admin::app.leads.expired-at') }}</th>
                                <th class="sub-total">
                                    {{ __('admin::app.leads.sub-total') }}
                                    <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                                </th>
                                <th class="discount">
                                    {{ __('admin::app.leads.discount') }}
                                    <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                                </th>
                                <th class="tax">
                                    {{ __('admin::app.leads.tax') }}
                                    <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                                </th>
                                <th class="adjustment">
                                    {{ __('admin::app.leads.adjustment') }}
                                    <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                                </th>
                                <th class="grand-total">
                                    {{ __('admin::app.leads.grand-total') }}
                                    <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                                </th>
                                <th class="actions">{{ __('admin::app.leads.actions') }}</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <tr v-for="quote in quotes">
                                <td class="sales-owner">@{{ quote.user.name }}</td>
                                <td class="person">@{{ quote.person.name }}</td>
                                <td class="quote-subject">@{{ quote.subject }}</td>
                                <td class="expired-at">@{{ quote.expired_at }}</td>
                                <td class="sub-total">@{{ quote.sub_total }}</td>
                                <td class="discount">@{{ quote.discount_amount }}</td>
                                <td class="tax">@{{ quote.tax_amount }}</td>
                                <td class="adjustment">@{{ quote.adjustment_amount }}</td>
                                <td class="grand-total">@{{ quote.grand_total }}</td>
                                <td class="actions">
                                    <span class="icon ellipsis-icon dropdown-toggle"></span>

                                    <div class="dropdown-list">
                                        <div class="dropdown-container">
                                            <ul>
                                                <li>
                                                    <a :href="'{{ route('admin.quotes.edit') }}/' + quote.id">{{ __('admin::app.leads.edit') }}</a>
                                                </li>
                                                <li>
                                                    <a :href="'{{ route('admin.quotes.print') }}/' + quote.id" target="_blank">{{ __('admin::app.leads.export-to-pdf') }}</a>
                                                </li>
                                                <li @click="removeQuote(quote)">
                                                    {{ __('admin::app.leads.remove') }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="! quotes.length">
                                <td colspan="10">
                                    <p style="text-align: center;">{{ __('admin::app.common.no-records-found') }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </tab>
        </tabs>
    </script>

    <script>
        Vue.component('tags-component', {

            template: '#tags-component-template',
    
            inject: ['$validator'],

            data: function() {
                return {
                    is_dropdown_open: false,

                    term: '',

                    is_searching: false,

                    tags: @json($lead->tags),

                    search_results: [],

                    tag: {
                        name: '',

                        color: '',

                        lead_id: "{{ $lead->id }}",
                    },

                    colors: [
                        '#337CFF',
                        '#FEBF00',
                        '#E5549F',
                        '#27B6BB',
                        '#FB8A3F',
                        '#43AF52',
                    ],

                    show_form: false,
                }
            },

            methods: {
                search: debounce(function () {
                    this.is_searching = true;

                    if (this.term.length < 2) {
                        this.search_results = [];

                        this.is_searching = false;

                        return;
                    }

                    var self = this;
                    
                    this.$http.get("{{ route('admin.settings.tags.search') }}", {params: {query: this.term}})
                        .then (function(response) {
                            self.search_results = response.data;

                            self.is_searching = false;
                        })
                        .catch (function (error) {
                            self.is_searching = false;
                        })
                }, 500),

                createTag: function() {
                    var self = this;

                    this.$validator.validateAll('tag-form').then(function (result) {
                        if (result) {
                            self.$http.post(`{{ route('admin.settings.tags.store') }}`, self.tag)
                                .then(response => {
                                    self.addTag(response.data.tag);
                                })
                                .catch(error => {});
                        }
                    });
                },

                addTag: function(tag) {
                    var self = this;

                    self.$http.post(`{{ route('admin.leads.tags.store', $lead->id) }}`, tag)
                        .then(response => {
                            self.is_dropdown_open = self.show_form = false;

                            self.search_results = [];

                            self.term = '';

                            self.tags.push(tag);

                            window.flashMessages = [{'type': 'success', 'message': response.data.message}];

                            self.$root.addFlashMessages();
                        })
                        .catch(error => {});
                },

                removeTag: function(tag) {
                    var self = this;

                    this.$http.delete("{{ route('admin.leads.tags.delete', $lead->id) }}/" + tag['id'])
                        .then (function(response) {
                            const index = self.tags.indexOf(tag);

                            Vue.delete(self.tags, index);
                            
                            window.flashMessages = [{'type': 'success', 'message': response.data.message}];

                            self.$root.addFlashMessages();
                        })
                        .catch (function (error) {
                        })
                }
            }
        });

        Vue.component('stage-component', {

            template: '#stage-component-template',
    
            inject: ['$validator'],

            data: function () {
                return {
                    currentStage: @json($lead->stage),

                    nextStageCode: null,

                    customStages: @json(app('\Webkul\Lead\Repositories\StageRepository')->all()),
                }
            },

            computed: {
                won: function() {
                    const results = this.customStages.filter(stage => stage.code == 'won');

                    return results[0];
                },

                lost: function() {
                    const results = this.customStages.filter(stage => stage.code == 'lost');

                    return results[0];
                },
            },

            methods: {
                changeStage: function(stage) {
                    var self = this;

                    this.$http.put("{{ route('admin.leads.update', $lead->id) }}", {'lead_stage_id': stage.id})
                        .then (function(response) {
                            self.currentStage = stage;

                            window.flashMessages = [{'type': 'success', 'message': response.data.message}];

                            self.$root.addFlashMessages();
                        })
                        .catch (function (error) {
                        })
                }
            }
        });
        
        Vue.component('activity-action-component', {

            template: '#activity-action-component-template',
    
            props: ['data'],

            inject: ['$validator'],

            data: function () {
                return {
                    show_cc: false,

                    show_bcc: false,
                }
            },

            mounted: function() {
                tinymce.init({
                    selector: 'textarea#reply',
                    height: 200,
                    width: "100%",
                    plugins: 'image imagetools media wordcount save fullscreen code table lists link hr',
                    toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor link hr | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent  | removeformat | code | table',
                    image_advtab: true
                });
            },

            methods: {
                onSubmit: function(e, formScope) {
                    this.$root.onSubmit(e, formScope);
                }
            }
        });

        Vue.component('activity-list-component', {

            template: '#activity-list-component-template',
    
            inject: ['$validator'],

            data: function () {
                return {
                    activities: @json($lead->activities),

                    types: ['all', 'note', 'call', 'meeting', 'lunch', 'email', 'file'],

                    typeLabels: {
                        'all': "{{ __('admin::app.leads.all') }}",

                        'note': "{{ __('admin::app.leads.notes') }}",

                        'call': "{{ __('admin::app.leads.calls') }}",
                        
                        'meeting': "{{ __('admin::app.leads.meetings') }}",

                        'lunch': "{{ __('admin::app.leads.lunches') }}",

                        'email': "{{ __('admin::app.leads.emails') }}",

                        'file': "{{ __('admin::app.leads.files') }}",
                    },

                    quotes: @json($lead->quotes()->with(['person', 'user'])->get())
                }
            },

            computed: {
                all: function() {
                    return this.activities;
                },

                note: function() {
                    return this.activities.filter(activity => activity.type == 'note');
                },

                call: function() {
                    return this.activities.filter(activity => activity.type == 'call');
                },

                meeting: function() {
                    return this.activities.filter(activity => activity.type == 'meeting');
                },

                lunch: function() {
                    return this.activities.filter(activity => activity.type == 'lunch');
                },

                email: function() {
                    return this.activities.filter(activity => activity.type == 'email');
                },

                file: function() {
                    return this.activities.filter(activity => activity.type == 'file');
                }
            },

            methods: {
                getActivities: function(type, subType) {
                    if (subType == 'planned') {
                        return this[type].filter(activity => ! activity.is_done);
                    } else {
                        return this[type].filter(activity => activity.is_done);
                    }
                },

                markAsDone: function(activity) {
                    var self = this;

                    this.$http.put("{{ route('admin.activities.update') }}/" + activity['id'], {'is_done': 1})
                        .then (function(response) {
                            activity.is_done = 1;

                            window.flashMessages = [{'type': 'success', 'message': response.data.message}];

                            self.$root.addFlashMessages();
                        })
                        .catch (function (error) {
                        })
                },

                edit: function(activity) {

                },

                remove: function(activity) {
                    var self = this;

                    this.$http.delete("{{ route('admin.activities.delete') }}/" + activity['id'])
                        .then (function(response) {
                            const index = self.activities.indexOf(activity);

                            Vue.delete(self.activities, index);
                            
                            window.flashMessages = [{'type': 'success', 'message': response.data.message}];

                            self.$root.addFlashMessages();
                        })
                        .catch (function (error) {
                        })
                },

                removeQuote: function(quote) {
                    var self = this;

                    this.$http.delete("{{ route('admin.leads.quotes.delete', $lead->id) }}/" + quote['id'])
                        .then (function(response) {
                            const index = self.quotes.indexOf(quote);

                            Vue.delete(self.quotes, index);
                            
                            window.flashMessages = [{'type': 'success', 'message': response.data.message}];

                            self.$root.addFlashMessages();
                        })
                        .catch (function (error) {
                        })
                }
            }
        });
    </script>
@endpush
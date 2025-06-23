
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.contacts.persons.edit.title')
    </x-slot>

    {!! view_render_event('admin.persons.edit.form.before') !!}

    <x-admin::form
        :action="route('admin.contacts.persons.update', $person->id)"
        method="PUT"
        enctype="multipart/form-data"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.persons.edit.breadcrumbs.before') !!}

                    <x-admin::breadcrumbs
                        name="contacts.persons.edit"
                        :entity="$person"
                    />

                    {!! view_render_event('admin.persons.edit.breadcrumbs.after') !!}

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.contacts.persons.edit.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <!--  Save button for Person -->
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.persons.edit.save_button.before') !!}

                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.contacts.persons.edit.save-btn')
                        </button>

                        {!! view_render_event('admin.persons.edit.save_button.after') !!}
                    </div>
                </div>
            </div>

            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                {!! view_render_event('admin.contacts.persons.edit.form_controls.before') !!}

                @php
    $attributes = app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
    'entity_type' => 'persons',
]);

 $should_remove_fields=[];

foreach ($attributes as $attribute){
    if($attribute->code==='person_type'){

           $attributeValues = app('Webkul\Attribute\Repositories\AttributeValueRepository')->findWhere([
                    'entity_type' => 'persons',
                    'attribute_id'=>$attribute->id
                ])->first();


            switch($attributeValues->integer_value){
                case 1:

                break;
                 case 2:

                $should_remove_fields=['company_name_en','company_name_ar','license_no',
                'company_issue_date','company_expiry_date','partner_2','partner_3','local_agent'
                ];

                break;
                 case 3:
 $should_remove_fields=['company_name_en','company_name_ar','license_no',
                'company_issue_date','company_expiry_date','partner_2','partner_3','local_agent'
                ];

                break;

            }

    }
}
$attributes = $attributes->reject(function ($attribute) use ($should_remove_fields) {
    return in_array($attribute->code, $should_remove_fields);
});
@endphp
                <x-admin::attributes
                    :custom-attributes="$attributes"
                    :custom-validations="[
                        'name' => [
                            'min:2',
                            'max:100',
                        ],
                        'job_title' => [
                            'max:100',
                        ],
                    ]"
                    :entity="$person"
                />

                {!! view_render_event('admin.contacts.persons.edit.form_controls.after') !!}
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.persons.edit.form.after') !!}
</x-admin::layouts>

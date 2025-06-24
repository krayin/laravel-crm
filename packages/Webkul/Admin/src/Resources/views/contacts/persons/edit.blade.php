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
            <div
                class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
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


            <div
                class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
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


                array_push($should_remove_fields,'rate');

                $rate=0;

                foreach($attributes as $attribute){
                        if($attribute->code === 'rate'){

                            $attributeValues = app('Webkul\Attribute\Repositories\AttributeValueRepository')->findWhere([
                                    'entity_type' => 'persons',
                                    'entity_id' => $person->id,
                                    'attribute_id' => $attribute->id,
                            ]);

                            if ($attributeValues->isEmpty()) {
                                // It's empty – no attribute values found
                            } else {
                                $rate = $attributeValues[0]->text_value;
                            }


                    }

                }



                //dump($attributeValues);

                $attributes = $attributes->reject(function ($attribute) use ($should_remove_fields) {
                    return in_array($attribute->code, $should_remove_fields);
                });

                $rate = intval($rate);

                @endphp

                <div class="mb-4 mb-2.5 w-full" style="display: inline-table">
                    <div class="person-rate">
                        <input type="radio" <?php echo $rate == 5 ? "checked" : ""; ?> id="star5" name="rate"
                               value="5"/>
                        <label for="star5" title="text">5 stars</label>
                        <input type="radio" <?php echo $rate == 4 ? "checked" : ""; ?> id="star4" name="rate"
                               value="4"/>
                        <label for="star4" title="text">4 stars</label>
                        <input type="radio" <?php echo $rate == 3 ? "checked" : ""; ?> id="star3" name="rate"
                               value="3"/>
                        <label for="star3" title="text">3 stars</label>
                        <input type="radio" <?php echo $rate == 2 ? "checked" : ""; ?> id="star2" name="rate"
                               value="2"/>
                        <label for="star2" title="text">2 stars</label>
                        <input type="radio" <?php echo $rate == 1 ? "checked" : ""; ?> id="star1" name="rate"
                               value="1"/>
                        <label for="star1" title="text">1 star</label>
                    </div>
                </div>

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

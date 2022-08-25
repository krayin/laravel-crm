<?php

namespace Webkul\WebForm\Http\Controllers;

use Illuminate\Support\Facades\Event;
use Webkul\WebForm\Http\Requests\WebForm;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\WebForm\Repositories\WebFormRepository;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Lead\Repositories\PipelineRepository;
use Webkul\Lead\Repositories\SourceRepository;
use Webkul\Lead\Repositories\TypeRepository;

class WebFormController extends Controller
{
    /**
     * AttributeRepository object
     *
     * @var \Webkul\Attribute\Repositories\AttributeRepository
     */
    protected $attributeRepository;

    /**
     * WebFormRepository object
     *
     * @var \Webkul\WebForm\Repositories\WebFormRepository
     */
    protected $webFormRepository;

    /**
     * LeadRepository object
     *
     * @var \Webkul\Lead\Repositories\LeadRepository
     */
    protected $leadRepository;

    /**
     * Pipeline repository instance.
     *
     * @var \Webkul\Lead\Repositories\PipelineRepository
     */
    protected $pipelineRepository;

    /**
     * PersonRepository object
     *
     * @var \Webkul\Contact\Repositories\PersonRepository
     */
    protected $personRepository;

    /**
     * SourceRepository object
     *
     * @var \Webkul\Lead\Repositories\SourceRepository
     */
    protected $sourceRepository;

    /**
     * TypeRepository object
     *
     * @var \Webkul\Lead\Repositories\TypeRepository
     */
    protected $typeRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Attribute\Repositories\AttributeRepository  $attributeRepository
     * @param  \Webkul\WebForm\Repositories\WebFormRepository  $webFormRepository
     * @param  \Webkul\Contact\Repositories\PersonRepository  $personRepository
     * @param  \Webkul\Lead\Repositories\LeadRepository  $leadRepository
     * @param \Webkul\Lead\Repositories\PipelineRepository  $pipelineRepository
     * @param \Webkul\Lead\Repositories\SourceRepository  $sourceRepository
     * @param \Webkul\Lead\Repositories\TypeRepository  $typeRepository
     * @return void
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        WebFormRepository $webFormRepository,
        PersonRepository $personRepository,
        LeadRepository $leadRepository,
        PipelineRepository $pipelineRepository,
        SourceRepository $sourceRepository,
        TypeRepository $typeRepository
    )
    {
        $this->attributeRepository = $attributeRepository;

        $this->webFormRepository = $webFormRepository;

        $this->personRepository = $personRepository;

        $this->leadRepository = $leadRepository;

        $this->pipelineRepository = $pipelineRepository;

        $this->sourceRepository = $sourceRepository;

        $this->typeRepository = $typeRepository;
    }

    /**
     * Display a listing of the email template.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(\Webkul\WebForm\DataGrids\WebFormDataGrid::class)->toJson();
        }

        return view('web_form::settings.web-forms.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $tempAttributes = $this->attributeRepository->findWhereIn('entity_type', ['persons', 'leads']);

        $attributes = [];

        foreach ($tempAttributes as $attribute) {
            if ($attribute->entity_type == 'persons'
                && (
                    $attribute->code == 'name'
                    || $attribute->code == 'emails'
                    || $attribute->code == 'contact_numbers'
                )
            ) {
                $attributes['default'][] = $attribute;
            } else {
                $attributes['other'][] = $attribute;
            }
        }

        return view('web_form::settings.web-forms.create', compact('attributes'));
    }

    /**
     * Store a newly created email templates in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'title'                  => 'required',
            'submit_button_label'    => 'required',
            'submit_success_action'  => 'required',
            'submit_success_content' => 'required',
        ]);

        Event::dispatch('settings.web_forms.create.before');

        $data = request()->all();

        $data['create_lead'] = isset($data['create_lead']) ? 1 : 0;

        $webForm = $this->webFormRepository->create($data);

        Event::dispatch('settings.web_forms.create.after', $webForm);

        session()->flash('success', trans('admin::app.settings.web-forms.create-success'));

        return redirect()->route('admin.settings.web_forms.index');
    }

    /**
     * Show the form for editing the specified email template.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $webForm = $this->webFormRepository->findOrFail($id);

        $attributes = $this->attributeRepository->findWhere([
            ['entity_type', 'IN', ['persons', 'leads']],
            ['id', 'NOTIN', $webForm->attributes()->pluck('attribute_id')->toArray()]
        ]);

        return view('web_form::settings.web-forms.edit', compact('webForm', 'attributes'));
    }

    /**
     * Update the specified email template in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'title'                  => 'required',
            'submit_button_label'    => 'required',
            'submit_success_action'  => 'required',
            'submit_success_content' => 'required',
        ]);

        Event::dispatch('settings.web_forms.update.before', $id);

        $data = request()->all();

        $data['create_lead'] = isset($data['create_lead']) ? 1 : 0;

        $webForm = $this->webFormRepository->update($data, $id);

        Event::dispatch('settings.web_forms.update.after', $webForm);

        session()->flash('success', trans('admin::app.settings.web-forms.update-success'));

        return redirect()->route('admin.settings.web_forms.index');
    }

    /**
     * Remove the specified email template from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $webForm = $this->webFormRepository->findOrFail($id);

        try {
            Event::dispatch('settings.web_forms.delete.before', $id);

            $this->webFormRepository->delete($id);

            Event::dispatch('settings.web_forms.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.settings.web-forms.delete-success'),
            ], 200);
        } catch(\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.settings.web-forms.delete-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('admin::app.settings.web-forms.delete-failed'),
        ], 400);
    }

    /**
     * Remove the specified email template from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function formJS($id)
    {
        $webForm = $this->webFormRepository->findOneByField('form_id', $id);

        return response()
            ->view('web_form::settings.web-forms.form-js', compact('webForm'))
            ->header('Content-Type', 'text/javascript');
    }

    /**
     * Remove the specified email template from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function formStore($id)
    {
        $person = $this->personRepository
            ->getModel()
            ->where('emails', 'like', "%" . request('persons.emails.0.value') . "%")
            ->first();

        if ($person) {
            request()->request->add(['persons' => array_merge(request('persons'), ['id' => $person->id])]);
        }

        app(WebForm::class);

        $webForm = $this->webFormRepository->findOrFail($id);

        if ($webForm->create_lead) {
            request()->request->add(['entity_type' => 'leads']);

            Event::dispatch('lead.create.before');

            $data = request('leads');

            $data['entity_type'] = 'leads';

            $data['person'] = request('persons');

            $data['status'] = 1;


            $pipeline = $this->pipelineRepository->getDefaultPipeline();

            $stage = $pipeline->stages()->first();

            $data['lead_pipeline_id'] = $pipeline->id;

            $data['lead_pipeline_stage_id'] = $stage->id;

            $data['title'] = request('leads.title') ?: 'Lead From Web Form';

            $data['lead_value'] = request('leads.lead_value') ?: 0;

            if (! request('leads.lead_source_id')) {
                $source = $this->sourceRepository->findOneByField('name', 'Web Form');

                if (! $source) {
                    $source = $this->sourceRepository->first();
                }

                $data['lead_source_id'] = $source->id;
            }

            $data['lead_type_id'] = request('leads.lead_type_id') ?: $this->typeRepository->first()->id;

            $lead = $this->leadRepository->create($data);

            Event::dispatch('lead.create.after', $lead);
        } else {
            if (! $person) {
                Event::dispatch('contacts.person.create.before');

                $data = request('persons');

                request()->request->add(['entity_type' => 'persons']);

                $data['entity_type'] = 'persons';

                $person = $this->personRepository->create($data);

                Event::dispatch('contacts.person.create.after', $person);
            }
        }

        if ($webForm->submit_success_action == 'message') {
            return response()->json([
                'message' => $webForm->submit_success_content,
            ], 200);
        } else {
            return response()->json([
                'redirect' => $webForm->submit_success_content,
            ], 200);
        }
    }

    /**
     * Remove the specified email template from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function preview($id)
    {
        $webForm = $this->webFormRepository->findOneByField('form_id', $id);

        if (is_null($webForm)) {
            abort(404);
        }

        return view('web_form::settings.web-forms.preview');
    }

    /**
     * Preview the web form from datagrid.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function view($id)
    {
        $webForm = $this->webFormRepository->findOneByField('id', $id);

        request()->merge(['id' => $webForm->form_id]);

        if (is_null($webForm)) {
            abort(404);
        }

        return view('web_form::settings.web-forms.preview');
    }
}
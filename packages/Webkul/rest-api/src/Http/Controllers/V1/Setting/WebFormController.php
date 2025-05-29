<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Lead\Repositories\PipelineRepository;
use Webkul\Lead\Repositories\SourceRepository;
use Webkul\Lead\Repositories\TypeRepository;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Resources\V1\Setting\WebFormResource;
use Webkul\WebForm\Repositories\WebFormRepository;

class WebFormController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected WebFormRepository $webFormRepository,
        protected PersonRepository $personRepository,
        protected LeadRepository $leadRepository,
        protected PipelineRepository $pipelineRepository,
        protected SourceRepository $sourceRepository,
        protected TypeRepository $typeRepository
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->allResources($this->webFormRepository);

        return WebFormResource::collection($users);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return WebFormResource
     */
    public function show($id)
    {
        $webForm = $this->webFormRepository->findOrFail($id);

        return new WebFormResource($webForm);
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

        return new JsonResource([
            'data'    => new WebFormResource($webForm),
            'message' => trans('rest-api::app.settings.web-forms.create-success'),
        ]);
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

        return new JsonResource([
            'data'    => new WebFormResource($webForm),
            'message' => trans('rest-api::app.settings.web-forms.updated-success'),
        ]);
    }

    /**
     * Remove the specified email template from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Event::dispatch('settings.web_forms.delete.before', $id);

            $this->webFormRepository->delete($id);

            Event::dispatch('settings.web_forms.delete.after', $id);

            return new JsonResource([
                'message' => trans('rest-api::app.settings.web-forms.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return new JsonResource([
                'message' => trans('rest-api::app.settings.web-forms.delete-failed'),
            ], 400);
        }
    }
}

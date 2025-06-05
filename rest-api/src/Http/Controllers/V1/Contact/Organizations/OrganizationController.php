<?php

namespace Webkul\RestApi\Http\Controllers\V1\Contact\Organizations;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Contact\Repositories\OrganizationRepository;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Request\MassDestroyRequest;
use Webkul\RestApi\Http\Resources\V1\Contact\OrganizationResource;

class OrganizationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected OrganizationRepository $organizationRepository)
    {
        $this->addEntityTypeInRequest('organizations');
    }

    /**
     * Display a listing of the organizations.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $organizations = $this->allResources($this->organizationRepository);

        return OrganizationResource::collection($organizations);
    }

    /**
     * Show resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $resource = $this->organizationRepository->find($id);

        return new OrganizationResource($resource);
    }

    /**
     * Store a newly created organization in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        Event::dispatch('contacts.organization.create.before');

        $organization = $this->organizationRepository->create($request->all());

        Event::dispatch('contacts.organization.create.after', $organization);

        return new JsonResource([
            'data'    => new OrganizationResource($organization),
            'message' => trans('rest-api::app.contacts.organizations.create-success'),
        ]);
    }

    /**
     * Update the organization in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeForm $request, $id)
    {
        Event::dispatch('contacts.organization.update.before', $id);

        $organization = $this->organizationRepository->update($request->all(), $id);

        Event::dispatch('contacts.organization.update.after', $organization);

        return new JsonResource([
            'data'    => new OrganizationResource($organization),
            'message' => trans('rest-api::app.contacts.organizations.update-success'),
        ]);
    }

    /**
     * Remove the organization from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Event::dispatch('contact.organization.delete.before', $id);

            $this->organizationRepository->delete($id);

            Event::dispatch('contact.organization.delete.after', $id);

            return new JsonResource([
                'message' => trans('rest-api::app.contacts.organizations.delete-success'),
            ]);
        } catch (\Exception $exception) {
            return new JsonResource([
                'message' => trans('rest-api::app.contacts.organizations.delete-failed'),
            ], 500);
        }
    }

    /**
     * Mass delete the organizations.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest)
    {
        $organizationIds = $massDestroyRequest->input('indices', []);

        foreach ($organizationIds as $organizationId) {
            $organization = $this->organizationRepository->find($organizationId);

            if (! $organization) {
                continue;
            }

            Event::dispatch('contact.organization.delete.before', $organizationId);

            $organization->delete($organizationId);

            Event::dispatch('contact.organization.delete.after', $organizationId);
        }

        return new JsonResource([
            'message' => trans('rest-api::app.contacts.organizations.delete-success'),
        ]);
    }
}

<?php

namespace Webkul\RestApi\Http\Controllers\V1\Lead;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\RestApi\Http\Controllers\V1\Controller;

class TagController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected LeadRepository $leadRepository) {}

    /**
     * Store a newly created resource in storage.
     */
    public function attach(int $id): JsonResource
    {
        Event::dispatch('leads.tag.create.before', $id);

        $lead = $this->leadRepository->find($id);

        if (! $lead->tags->contains(request()->input('tag_id'))) {
            $lead->tags()->attach(request()->input('tag_id'));
        }

        Event::dispatch('leads.tag.create.after', $lead);

        return new JsonResource([
            'message' => trans('rest-api::app.leads.view.tags.create-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function detach($leadId): JsonResource
    {
        Event::dispatch('leads.tag.delete.before', $leadId);

        $lead = $this->leadRepository->find($leadId);

        $lead->tags()->detach(request()->input('tag_id'));

        Event::dispatch('leads.tag.delete.after', $lead);

        return new JsonResource([
            'message' => trans('rest-api::app.leads.view.tags.delete-success'),
        ]);
    }
}

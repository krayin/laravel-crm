<?php

namespace Webkul\Admin\Http\Controllers\Lead;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Lead\Repositories\LeadRepository;

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
     *
     * @param  int  $id
     * @return Response
     */
    public function attach($id)
    {
        $lead = $this->leadRepository->findOrFail($id);

        $this->preventUnauthorizedAccess($lead->user_id);

        Event::dispatch('leads.tag.create.before', $id);

        if (! $lead->tags->contains(request()->input('tag_id'))) {
            $lead->tags()->attach(request()->input('tag_id'));
        }

        Event::dispatch('leads.tag.create.after', $lead);

        return response()->json([
            'message' => trans('admin::app.leads.view.tags.create-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $leadId
     * @return Response
     */
    public function detach($leadId)
    {
        $lead = $this->leadRepository->findOrFail($leadId);

        $this->preventUnauthorizedAccess($lead->user_id);

        Event::dispatch('leads.tag.delete.before', $leadId);

        $lead->tags()->detach(request()->input('tag_id'));

        Event::dispatch('leads.tag.delete.after', $lead);

        return response()->json([
            'message' => trans('admin::app.leads.view.tags.destroy-success'),
        ]);
    }
}

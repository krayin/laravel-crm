<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting\Marketing;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Webkul\EmailTemplate\Repositories\EmailTemplateRepository;
use Webkul\Marketing\Repositories\CampaignRepository;
use Webkul\Marketing\Repositories\EventRepository;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Request\MassDestroyRequest;
use Webkul\RestApi\Http\Resources\V1\Setting\CampaignResource;

class CampaignController extends Controller
{
    /**
     * Create new a controller instance.
     */
    public function __construct(
        protected CampaignRepository $campaignRepository,
        protected EventRepository $eventRepository,
        protected EmailTemplateRepository $emailTemplateRepository,
    ) {}

    /**
     * Display a listing of the marketing campaigns.
     */
    public function index(): JsonResource
    {
        $campaigns = $this->allResources($this->campaignRepository);

        return CampaignResource::collection($campaigns);
    }

    /**
     * Store a newly created marketing campaign in storage.
     */
    public function store(): JsonResource
    {
        $validatedData = $this->validate(request(), [
            'name'                  => 'required|string|max:255',
            'subject'               => 'required|string|max:255',
            'marketing_template_id' => 'required|exists:email_templates,id',
            'marketing_event_id'    => 'required|exists:marketing_events,id',
            'status'                => 'sometimes|required|in:0,1',
        ]);

        Event::dispatch('settings.marketing.campaigns.create.before');

        $marketingCampaign = $this->campaignRepository->create($validatedData);

        Event::dispatch('settings.marketing.campaigns.create.after', $marketingCampaign);

        return new JsonResource([
            'data'    => new CampaignResource($marketingCampaign),
            'message' => trans('rest-api::app.settings.marketing.campaigns.create-success'),
        ]);
    }

    /**
     * Show the specified Resource.
     */
    public function show(int $id): CampaignResource
    {
        $campaign = $this->campaignRepository->findOrFail($id);

        return new CampaignResource($campaign);
    }

    /**
     * Update the specified marketing campaign in storage.
     */
    public function update(int $id): JsonResource
    {
        $validatedData = $this->validate(request(), [
            'name'                  => 'required|string|max:255',
            'subject'               => 'required|string|max:255',
            'marketing_template_id' => 'required|exists:email_templates,id',
            'marketing_event_id'    => 'required|exists:marketing_events,id',
            'status'                => 'sometimes|required|in:0,1',
        ]);

        Event::dispatch('settings.marketing.campaigns.update.before', $id);

        $marketingCampaign = $this->campaignRepository->update($validatedData, $id);

        Event::dispatch('settings.marketing.campaigns.update.after', $marketingCampaign);

        return new JsonResource([
            'data'    => new CampaignResource($marketingCampaign),
            'message' => trans('rest-api::app.settings.marketing.campaigns.update-success'),
        ]);
    }

    /**
     * Remove the specified marketing campaign from storage.
     */
    public function destroy(int $id): JsonResource
    {
        Event::dispatch('settings.marketing.campaigns.delete.before', $id);

        $this->campaignRepository->delete($id);

        Event::dispatch('settings.marketing.campaigns.delete.after', $id);

        return new JsonResource([
            'message' => trans('rest-api::app.settings.marketing.campaigns.destroy-success'),
        ]);
    }

    /**
     * Remove the specified marketing campaigns from storage.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResource
    {
        $marketingCampaignIds = $massDestroyRequest->input('indices');

        foreach ($marketingCampaignIds as $marketingCampaignId) {
            $campaign = $this->campaignRepository->find($marketingCampaignId);

            if (! $campaign) {
                continue;
            }

            Event::dispatch('settings.marketing.campaigns.delete.before', $campaign);

            $campaign?->delete();

            Event::dispatch('settings.marketing.campaigns.delete.after', $campaign);
        }

        return new JsonResource([
            'message' => trans('rest-api::app.settings.marketing.campaigns.destroy-success'),
        ]);
    }
}

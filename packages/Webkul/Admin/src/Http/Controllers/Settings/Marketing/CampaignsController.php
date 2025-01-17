<?php

namespace Webkul\Admin\Http\Controllers\Settings\Marketing;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\Marketing\CampaignDatagrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\EmailTemplate\Repositories\EmailTemplateRepository;
use Webkul\Marketing\Repositories\CampaignRepository;
use Webkul\Marketing\Repositories\EventRepository;

class CampaignsController extends Controller
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
    public function index(): View|JsonResponse
    {
        if (request()->isXmlHttpRequest()) {
            return datagrid(CampaignDatagrid::class)->process();
        }

        return view('admin::settings.marketing.campaigns.index');
    }

    /**
     * Get marketing events.
     */
    public function getEvents(): JsonResponse
    {
        $events = $this->eventRepository->get(['id', 'name']);

        return response()->json([
            'data' => $events,
        ]);
    }

    /**
     * Get Email Templates.
     */
    public function getEmailTemplates(): JsonResponse
    {
        $emailTemplates = $this->emailTemplateRepository->get(['id', 'name']);

        return response()->json([
            'data' => $emailTemplates,
        ]);
    }

    /**
     * Store a newly created marketing campaign in storage.
     */
    public function store(): JsonResponse
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

        return response()->json([
            'message' => trans('admin::app.settings.marketing.campaigns.index.create-success'),
        ]);
    }

    /**
     * Show the specified Resource.
     */
    public function show(int $id): JsonResponse
    {
        $campaign = $this->campaignRepository->findOrFail($id);

        return response()->json([
            'data' => $campaign,
        ]);
    }

    /**
     * Update the specified marketing campaign in storage.
     */
    public function update(int $id): JsonResponse
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

        return response()->json([
            'message' => trans('admin::app.settings.marketing.campaigns.index.update-success'),
        ]);
    }

    /**
     * Remove the specified marketing campaign from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        Event::dispatch('settings.marketing.campaigns.delete.before', $id);

        $this->campaignRepository->delete($id);

        Event::dispatch('settings.marketing.campaigns.delete.after', $id);

        return response()->json([
            'message' => trans('admin::app.settings.marketing.campaigns.index.delete-success'),
        ]);
    }

    /**
     * Remove the specified marketing campaigns from storage.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $campaigns = $this->campaignRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

        foreach ($campaigns as $campaign) {
            Event::dispatch('settings.marketing.campaigns.delete.before', $campaign);

            $this->campaignRepository->delete($campaign->id);

            Event::dispatch('settings.marketing.campaigns.delete.after', $campaign);
        }

        return response()->json([
            'message' => trans('admin::app.settings.marketing.campaigns.index.mass-delete-success'),
        ]);
    }
}

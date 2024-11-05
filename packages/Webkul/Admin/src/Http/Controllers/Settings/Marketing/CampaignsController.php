<?php

namespace Webkul\Admin\Http\Controllers\Settings\Marketing;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\Marketing\CampaignDatagrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\EmailTemplate\Repositories\EmailTemplateRepository;
use Webkul\Marketing\Repositories\CampaignRepository;
use Webkul\Marketing\Repositories\EventRepository;

class CampaignsController extends Controller
{
    /**
     * CampaignRepository object
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
        if (request()->ajax()) {
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

        Event::dispatch('settings.marketing.campaign.create.before');

        $marketingCampaign = $this->campaignRepository->create($validatedData);

        Event::dispatch('settings.marketing.campaign.create.after', $marketingCampaign);

        return response()->json([
            'message' => trans('Campanigs created successfully.'),
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
     * Update the specified resource.
     */
    public function update(int $id): JsonResponse
    {
        return response()->json([]);
    }
}

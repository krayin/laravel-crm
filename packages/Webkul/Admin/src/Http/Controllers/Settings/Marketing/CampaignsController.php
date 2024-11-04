<?php

namespace Webkul\Admin\Http\Controllers\Settings\Marketing;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\Marketing\CampaignDatagrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Marketing\Repositories\CampaignRepository;

class CampaignsController extends Controller
{
    /**
     * CampaignRepository object
     */
    public function __construct(protected CampaignRepository $campaignRepository) {}

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

    // /**
    //  * Store a newly created marketing campaign in storage.
    //  */
    // public function store(): JsonResponse
    // {
    //     $validatedData = $this->validate(request(), [
    //         'name'        => 'required',
    //         'description' => 'required',
    //         'date'        => 'required|date|after_or_equal:today',
    //     ]);

    //     Event::dispatch('settings.marketing-campaign.create.before');

    //     $marketingCampaign = $this->campaignRepository->create($validatedData);

    //     Event::dispatch('settings.marketing-campaign.create.after', $marketingCampaign);
    // }
}
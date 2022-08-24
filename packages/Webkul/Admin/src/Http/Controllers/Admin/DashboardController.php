<?php

namespace Webkul\Admin\Http\Controllers\Admin;

use Carbon\Carbon;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Helpers\Dashboard as DashboardHelper;

class DashboardController extends Controller
{
    /**
     * Dashboard object
     *
     * @var \Webkul\Admin\Helpers\Dashboard
     */
    protected $dashboardHelper;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Admin\Helpers\DashboardHelper  $dashboardHelper
     * @return void
     */
    public function __construct(DashboardHelper $dashboardHelper)
    {
        $this->dashboardHelper = $dashboardHelper;

        $this->dashboardHelper->setCards();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cards = $this->dashboardHelper->getCards();

        if ($dateRange = request('date-range')) {
            $dateRange = explode(",", $dateRange);

            $endDate = $dateRange[1];
            $startDate = $dateRange[0];
        } else {
            $endDate = Carbon::now()->format('Y-m-d');
            
            $startDate = Carbon::now()->subMonth()->addDays(1)->format('Y-m-d');
        }

        return view('admin::dashboard.index', compact('cards', 'startDate', 'endDate'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function template()
    {
        return view('admin::dashboard.template');
    }

    /**
     * Returns json data for dashboard card.
     */
    public function getCardData()
    {
        $cardData = $this->dashboardHelper->getFormattedCardData(request()->all());

        return response()->json($cardData);
    }

    /**
     * Returns json data for available dashboard cards.
     * 
     * @return \Illuminate\Http\Response
     */
    public function getCards()
    {
        $response = $this->dashboardHelper->getCards();;

        $response = array_map(function ($card) {
            if ($card['view_url'] ?? false) {
                $card['view_url'] = route($card['view_url'], $card['url_params'] ?? []);
            }

            return $card;
        }, $response);

        return response()->json($response);
    }

    /**
     * Returns updated json data for available dashboard cards.
     * 
     * @return \Illuminate\Http\Response
     */
    public function updateCards()
    {
        $requestData = request()->all();

        $cards = $this->dashboardHelper->getCards();

        foreach ($requestData['cards'] as $requestedCardData) {
            foreach ($cards as $cardIndex => $card) {
                if (isset($card['card_id'])
                    && isset($requestedCardData['card_id'])
                    && $card['card_id'] == $requestedCardData['card_id']
                ) {
                    $cards[$cardIndex]['selected'] = $requestedCardData['selected'];
                }
            }
        }

        return response()->json($cards);
    }
}
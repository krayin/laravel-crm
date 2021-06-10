<?php

namespace Webkul\Admin\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Traits\DashboardHelper;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Admin\Http\Controllers\Controller;

class DashboardController extends Controller
{
    use DashboardHelper;

    private $cards;

    private $cardData = [];

    private $leadRepository;

    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadRepository = $leadRepository;

        $this->setCardsData();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {

        return view('admin::dashboard.template');
        
        $cards = $this->cards;

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
        $cardData = $this->getFormattedCardData(request()->all());

        return response()->json($cardData);
    }

    /**
     * Returns json data for available dashboard cards.
     */
    public function getCards()
    {
        $response = $this->cards;

        return response()->json($response);
    }

    /**
     * Returns updated json data for available dashboard cards.
     */
    public function updateCards()
    {
        $requestData = request()->all();

        foreach ($requestData['cards'] as $requestedCardData) {
            foreach ($this->cards as $cardIndex => $card) {
                if (isset($card['card_id']) && isset($requestedCardData['card_id']) && $card['card_id'] == $requestedCardData['card_id']) {
                    $this->cards[$cardIndex]['selected'] = $requestedCardData['selected'];
                }
            }
        }

        return response()->json($this->cards);
    }
}
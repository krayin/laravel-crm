<?php

namespace Webkul\RestApi\Http\Controllers\V1\Configuration;

use Illuminate\Support\Facades\Event;
use Webkul\Core\Repositories\CoreConfigRepository as ConfigurationRepository;
use Webkul\RestApi\Http\Controllers\V1\Controller;

class ConfigurationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected ConfigurationRepository $configurationRepository) {}

    /**
     * Store a newly created configuration in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        Event::dispatch('core.configuration.save.before');

        $this->configurationRepository->create(request()->all());

        Event::dispatch('core.configuration.save.after');

        return response([
            'message' => trans('rest-api::app.configuration.save-success'),
        ]);
    }
}

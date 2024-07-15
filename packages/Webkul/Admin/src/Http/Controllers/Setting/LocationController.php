<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Event;
use Prettus\Repository\Criteria\RequestCriteria;
use Webkul\Attribute\Http\Requests\AttributeForm;
use Webkul\Admin\DataGrids\Setting\LocationDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Warehouse\Repositories\LocationRepository;

class LocationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected LocationRepository $locationRepository)
    {
    }

    /**
     * Search location results
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        $results = $this->locationRepository
            ->pushCriteria(app(RequestCriteria::class))
            ->all();

        return response()->json($results);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Webkul\Attribute\Http\Requests\AttributeForm $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        Event::dispatch('settings.location.create.before');

        $location = $this->locationRepository->create(request()->all());

        Event::dispatch('settings.location.create.after', $location);

        return response()->json([
            'message' => trans('admin::app.locations.create-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->locationRepository->findOrFail($id);

        try {
            Event::dispatch('settings.location.delete.before', $id);

            $this->locationRepository->delete($id);

            Event::dispatch('settings.location.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.locations.location')]),
            ], 200);
        } catch(\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.response.destroy-failed', ['name' => trans('admin::app.locations.location')]),
            ], 400);
        }
    }
}

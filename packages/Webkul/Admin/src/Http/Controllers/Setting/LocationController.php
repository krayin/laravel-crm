<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Event;
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
        request()->request->add(['entity_type' => 'locations']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(LocationDataGrid::class)->toJson();
        }

        return view('admin::settings.locations.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::settings.locations.create');
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

        if (request()->ajax()) {
            return response()->json([
                'message' => trans('admin::app.locations.create-success'),
            ]);
        }

        session()->flash('success', trans('admin::app.locations.create-success'));

        return redirect()->route('admin.settings.locations.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $location = $this->locationRepository->findOrFail($id);

        return view('admin::settings.locations.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Webkul\Attribute\Http\Requests\AttributeForm $request
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeForm $request, $id)
    {
        Event::dispatch('settings.location.update.before', $id);

        $location = $this->locationRepository->update(request()->all(), $id);

        Event::dispatch('settings.location.update.after', $location);

        session()->flash('success', trans('admin::app.locations.update-success'));

        return redirect()->route('admin.settings.locations.index');
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

    /**
     * Search location results
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        $results = $this->locationRepository->findWhere([
            ['name', 'like', '%' . urldecode(request()->input('query')) . '%']
        ]);

        return response()->json($results);
    }
}

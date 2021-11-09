<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Lead\Repositories\SourceRepository;

class SourceController extends Controller
{
    /**
     * SourceRepository object
     *
     * @var \Webkul\User\Repositories\SourceRepository
     */
    protected $sourceRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Lead\Repositories\SourceRepository  $sourceRepository
     * @return void
     */
    public function __construct(SourceRepository $sourceRepository)
    {
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(\Webkul\Admin\DataGrids\Setting\SourceDataGrid::class)->toJson();
        }

        return view('admin::settings.sources.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required|unique:lead_sources,name'
        ]);
        
        if ($validator->fails()) {
            session()->flash('error', trans('admin::app.settings.sources.name-exists'));

            return redirect()->back();
        }

        Event::dispatch('settings.source.create.before');

        $source = $this->sourceRepository->create(request()->all());

        Event::dispatch('settings.source.create.after', $source);

        session()->flash('success', trans('admin::app.settings.sources.create-success'));

        return redirect()->route('admin.settings.sources.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $source = $this->sourceRepository->findOrFail($id);

        return view('admin::settings.sources.edit', compact('source'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'name' => 'required|unique:lead_sources,name,' . $id,
        ]);

        Event::dispatch('settings.source.update.before', $id);

        $source = $this->sourceRepository->update(request()->all(), $id);

        Event::dispatch('settings.source.update.after', $source);

        session()->flash('success', trans('admin::app.settings.sources.update-success'));

        return redirect()->route('admin.settings.sources.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $source = $this->sourceRepository->findOrFail($id);

        try {
            Event::dispatch('settings.source.delete.before', $id);

            $this->sourceRepository->delete($id);

            Event::dispatch('settings.source.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.settings.sources.delete-success'),
            ], 200);
        } catch(\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.settings.sources.delete-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('admin::app.settings.sources.delete-failed'),
        ], 400);
    }
}

<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Lead\Repositories\PipelineRepository;

class PipelineController extends Controller
{
    /**
     * PipelineRepository object
     *
     * @var \Webkul\Lead\Repositories\PipelineRepository
     */
    protected $pipelineRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Lead\Repositories\PipelineRepository  $pipelineRepository
     * @return void
     */
    public function __construct(PipelineRepository $pipelineRepository)
    {
        $this->pipelineRepository = $pipelineRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(\Webkul\Admin\DataGrids\Setting\PipelineDataGrid::class)->toJson();
        }

        return view('admin::settings.pipelines.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::settings.pipelines.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'name'        => 'required',
            'rotten_days' => 'required',
        ]);

        request()->merge([
            'is_default' => request()->has('is_default') ? 1 : 0,
        ]);

        Event::dispatch('settings.pipeline.create.before');

        $pipeline = $this->pipelineRepository->create(request()->all());

        Event::dispatch('settings.pipeline.create.after', $pipeline);

        session()->flash('success', trans('admin::app.settings.pipelines.create-success'));

        return redirect()->route('admin.settings.pipelines.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $pipeline = $this->pipelineRepository->findOrFail($id);

        return view('admin::settings.pipelines.edit', compact('pipeline'));
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
            'name' => 'required',
        ]);

        request()->merge([
            'is_default' => request()->has('is_default') ? 1 : 0,
        ]);

        Event::dispatch('settings.pipeline.update.before', $id);

        $pipeline = $this->pipelineRepository->update(request()->all(), $id);

        Event::dispatch('settings.pipeline.update.after', $pipeline);

        session()->flash('success', trans('admin::app.settings.pipelines.update-success'));

        return redirect()->route('admin.settings.pipelines.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pipeline = $this->pipelineRepository->findOrFail($id);

        if ($pipeline->is_default) {
            return response()->json([
                'status'  => false,
                'message' => trans('admin::app.settings.pipelines.default-delete-error'),
            ], 400);
        }

        try {
            Event::dispatch('settings.pipeline.delete.before', $id);

            $this->pipelineRepository->delete($id);

            Event::dispatch('settings.pipeline.delete.after', $id);

            return response()->json([
                'status'  => true,
                'message' => trans('admin::app.settings.pipelines.delete-success'),
            ], 200);
        } catch(\Exception $exception) {
            return response()->json([
                'status'  => false,
                'message' => trans('admin::app.settings.pipelines.delete-failed'),
            ], 400);
        }

        return response()->json([
            'status'    => false,
            'message'   => trans('admin::app.settings.pipelines.delete-failed'),
        ], 400);
    }
}

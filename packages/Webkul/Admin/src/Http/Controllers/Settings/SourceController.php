<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\SourceDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Lead\Repositories\SourceRepository;

class SourceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected SourceRepository $sourceRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(SourceDataGrid::class)->process();
        }

        return view('admin::settings.sources.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): JsonResponse
    {
        $this->validate(request(), [
            'name' => ['required', 'unique:lead_sources,name'],
        ]);

        Event::dispatch('settings.source.create.before');

        $source = $this->sourceRepository->create(request()->only(['name']));

        Event::dispatch('settings.source.create.after', $source);

        return new JsonResponse([
            'data'    => $source,
            'message' => trans('admin::app.settings.sources.index.create-success'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View|JsonResponse
    {
        $source = $this->sourceRepository->findOrFail($id);

        return new JsonResponse([
            'data' => $source,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id): JsonResponse
    {
        $this->validate(request(), [
            'name' => 'required|unique:lead_sources,name,'.$id,
        ]);

        Event::dispatch('settings.source.update.before', $id);

        $source = $this->sourceRepository->update(request()->only(['name']), $id);

        Event::dispatch('settings.source.update.after', $source);

        return new JsonResponse([
            'data'    => $source,
            'message' => trans('admin::app.settings.sources.index.update-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $source = $this->sourceRepository->findOrFail($id);

        try {
            Event::dispatch('settings.source.delete.before', $id);

            $source->delete($id);

            Event::dispatch('settings.source.delete.after', $id);

            return new JsonResponse([
                'message' => trans('admin::app.settings.sources.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => trans('admin::app.settings.sources.index.delete-failed'),
            ], 400);
        }
    }
}

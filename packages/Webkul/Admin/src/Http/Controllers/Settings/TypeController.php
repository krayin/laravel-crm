<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\TypeDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Lead\Repositories\TypeRepository;

class TypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected TypeRepository $typeRepository) {}

    /**
     * Display a listing of the type.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(TypeDataGrid::class)->process();
        }

        return view('admin::settings.types.index');
    }

    /**
     * Store a newly created type in storage.
     */
    public function store(): JsonResponse
    {
        $this->validate(request(), [
            'name' => ['required', 'unique:lead_types,name'],
        ]);

        Event::dispatch('settings.type.create.before');

        $type = $this->typeRepository->create(request()->only(['name']));

        Event::dispatch('settings.type.create.after', $type);

        return new JsonResponse([
            'data'    => $type,
            'message' => trans('admin::app.settings.types.index.create-success'),
        ]);
    }

    /**
     * Show the form for editing the specified type.
     */
    public function edit(int $id): View|JsonResponse
    {
        $type = $this->typeRepository->findOrFail($id);

        return new JsonResponse([
            'data' => $type,
        ]);
    }

    /**
     * Update the specified type in storage.
     */
    public function update(int $id): JsonResponse
    {
        $this->validate(request(), [
            'name' => 'required|unique:lead_types,name,'.$id,
        ]);

        Event::dispatch('settings.type.update.before', $id);

        $type = $this->typeRepository->update(request()->only(['name']), $id);

        Event::dispatch('settings.type.update.after', $type);

        return new JsonResponse([
            'data'    => $type,
            'message' => trans('admin::app.settings.types.index.update-success'),
        ]);
    }

    /**
     * Remove the specified type from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $type = $this->typeRepository->findOrFail($id);

        try {
            Event::dispatch('settings.type.delete.before', $id);

            $type->delete($id);

            Event::dispatch('settings.type.delete.after', $id);

            return new JsonResponse([
                'message' => trans('admin::app.settings.types.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => trans('admin::app.settings.types.index.delete-failed'),
            ], 400);
        }
    }
}

<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Prettus\Repository\Criteria\RequestCriteria;
use Webkul\Admin\DataGrids\Settings\TagDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Resources\TagResource;
use Webkul\Tag\Repositories\TagRepository;

class TagController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected TagRepository $tagRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(TagDataGrid::class)->process();
        }

        return view('admin::settings.tags.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): JsonResponse
    {
        $this->validate(request(), [
            'name' => ['required', 'unique:tags,name', 'max:50'],
        ]);

        Event::dispatch('settings.tag.create.before');

        $tag = $this->tagRepository->create(array_merge(request()->only([
            'name',
            'color',
        ]), [
            'user_id' => auth()->guard('user')->user()->id,
        ]));

        Event::dispatch('settings.tag.create.after', $tag);

        return new JsonResponse([
            'data'    => new TagResource($tag),
            'message' => trans('admin::app.settings.tags.index.create-success'),
        ]);
    }

    /**
     * Show the form for editing the specified tag.
     */
    public function edit(int $id): View|JsonResponse
    {
        $tag = $this->tagRepository->findOrFail($id);

        return new JsonResponse([
            'data' => $tag,
        ]);
    }

    /**
     * Update the specified tag in storage.
     */
    public function update(int $id): JsonResponse
    {
        $this->validate(request(), [
            'name' => 'required|max:50|unique:tags,name,'.$id,
        ]);

        Event::dispatch('settings.tag.update.before', $id);

        $tag = $this->tagRepository->update(request()->only([
            'name',
            'color',
        ]), $id);

        Event::dispatch('settings.tag.update.after', $tag);

        return new JsonResponse([
            'data'    => new TagResource($tag),
            'message' => trans('admin::app.settings.tags.index.update-success'),
        ]);
    }

    /**
     * Remove the specified type from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $tag = $this->tagRepository->findOrFail($id);

        try {
            Event::dispatch('settings.tag.delete.before', $id);

            $tag->delete($id);

            Event::dispatch('settings.tag.delete.after', $id);

            return new JsonResponse([
                'message' => trans('admin::app.settings.tags.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => trans('admin::app.settings.tags.index.delete-failed'),
            ], 400);
        }
    }

    /**
     * Search tag results
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        $tags = $this->tagRepository
            ->pushCriteria(app(RequestCriteria::class))
            ->all();

        return TagResource::collection($tags);
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $indices = $massDestroyRequest->input('indices');

        try {
            foreach ($indices as $index) {
                Event::dispatch('settings.tag.delete.before', $index);

                $this->tagRepository->delete($index);

                Event::dispatch('settings.tag.delete.after', $index);
            }

            return new JsonResponse([
                'message' => trans('admin::app.customers.reviews.index.datagrid.mass-delete-success'),
            ], 200);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

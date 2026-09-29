<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\AttributeDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Core\Contracts\Validations\Code;

class AttributeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected AttributeValueRepository $attributeValueRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(AttributeDataGrid::class)->process();
        }

        return view('admin::settings.attributes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin::settings.attributes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): RedirectResponse|JsonResponse
    {
        $this->validate(request(), [
            'code' => ['required', 'unique:attributes,code,NULL,NULL,entity_type,'.request('entity_type'), new Code],
            'name' => 'required',
            'type' => 'required',
            'validation' => 'nullable|in:numeric,email,decimal,url',
        ]);

        Event::dispatch('settings.attribute.create.before');

        $attribute = $this->attributeRepository->create(request()->all());

        Event::dispatch('settings.attribute.create.after', $attribute);

        if (request()->ajax()) {
            return response()->json([
                'data' => $attribute,
                'message' => trans('admin::app.settings.attributes.index.create-success'),
            ]);
        }

        session()->flash('success', trans('admin::app.settings.attributes.index.create-success'));

        return redirect()->route('admin.settings.attributes.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $attribute = $this->attributeRepository->findOrFail($id);

        return view('admin::settings.attributes.edit', compact('attribute'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id): RedirectResponse
    {
        $this->validate(request(), [
            'code' => ['required', 'unique:attributes,code,NULL,NULL,entity_type,'.$id, new Code],
            'name' => 'required',
            'type' => 'required',
            'validation' => 'nullable|in:numeric,email,decimal,url',
        ]);

        Event::dispatch('settings.attribute.update.before', $id);

        $quickAdd = request()->has('quick_add') ? 1 : 0;

        request()->merge(['quick_add' => $quickAdd]);

        $attribute = $this->attributeRepository->update(request()->all(), $id);

        Event::dispatch('settings.attribute.update.after', $attribute);

        session()->flash('success', trans('admin::app.settings.attributes.index.update-success'));

        return redirect()->route('admin.settings.attributes.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $attribute = $this->attributeRepository->findOrFail($id);

        if (! $attribute->is_user_defined) {
            return response()->json([
                'message' => trans('admin::app.settings.attributes.index.user-define-error'),
            ], 400);
        }

        try {
            Event::dispatch('settings.attribute.delete.before', $id);

            $this->attributeRepository->delete($id);

            Event::dispatch('settings.attribute.delete.after', $id);

            return response()->json([
                'status' => true,
                'message' => trans('admin::app.settings.attributes.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.settings.attributes.index.delete-failed'),
            ], 400);
        }
    }

    /**
     * Check unique validation.
     *
     * @return void
     */
    public function checkUniqueValidation()
    {
        $attribute = $this->attributeRepository->findOneWhere([
            'code' => request('attribute_code'),
        ]);

        return response()->json([
            'validated' => $this->attributeValueRepository->isValueUnique(
                request('entity_id'),
                request('entity_type'),
                $attribute,
                request('attribute_value'),
            ),
        ]);
    }

    /**
     * Search attribute lookup results
     */
    public function lookup($lookup): JsonResponse
    {
        $results = $this->attributeRepository->getLookUpOptions($lookup, request()->input('query'));

        return response()->json($results);
    }

    /**
     * Search attribute lookup results
     */
    public function lookupEntity(string $lookup): JsonResponse
    {
        $result = $this->attributeRepository->getLookUpEntity($lookup, request()->input('query'));

        return response()->json($result);
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $count = 0;

        $attributes = $this->attributeRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

        foreach ($attributes as $attribute) {
            $attribute = $this->attributeRepository->find($attribute->id);

            if (! $attribute->is_user_defined) {
                continue;
            }

            Event::dispatch('settings.attribute.delete.before', $attribute->id);

            $this->attributeRepository->delete($attribute->id);

            Event::dispatch('settings.attribute.delete.after', $attribute->id);

            $count++;
        }

        if (! $count) {
            return response()->json([
                'message' => trans('admin::app.settings.attributes.index.mass-delete-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('admin::app.settings.attributes.index.delete-success'),
        ]);
    }

    /**
     * Get attribute options associated with attribute.
     *
     * @return View
     */
    public function getAttributeOptions(int $id)
    {
        $attribute = $this->attributeRepository->findOrFail($id);

        return $attribute->options()->orderBy('sort_order')->get();
    }

    /**
     * Download image or file
     */
    public function download()
    {
        $path = request('path');

        /**
         * The path arrives from the query string, so it is resolved against the attribute values
         * that actually reference a stored file before anything is served. Handing it straight to
         * the disk let any path on the shared storage disk be downloaded through this endpoint —
         * import artefacts, activity attachments and configuration uploads all live alongside
         * attribute files. The type check matters because `text_value` is shared with the text,
         * textarea, multiselect and checkbox types, which hold no file reference.
         */
        if (! is_string($path) || $path === '') {
            abort(404);
        }

        $attributeValue = $this->attributeValueRepository
            ->findWhere(['text_value' => $path])
            ->first(fn ($value) => in_array($value->attribute?->type, ['file', 'image'], true));

        if (! $attributeValue) {
            abort(404);
        }

        return Storage::download($attributeValue->text_value);
    }
}

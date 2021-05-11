<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Event;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Core\Contracts\Validations\Code;

class AttributeController extends Controller
{
    /**
     * AttributeRepository object
     *
     * @var \Webkul\Attribute\Repositories\AttributeRepository
     */
    protected $attributeRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Attribute\Repositories\AttributeRepository  $attributeRepository
     * @return void
     */
    public function __construct(AttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin::settings.attributes.index', [
            'tableClass' => '\Webkul\Admin\DataGrids\Setting\AttributeDataGrid'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::settings.attributes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'code' => ['required', 'unique:attributes,code', new Code],
            'name' => 'required',
            'type' => 'required',
        ]);

        Event::dispatch('settings.attribute.create.before');

        $attribute = $this->attributeRepository->create(request()->all());

        Event::dispatch('settings.attribute.create.after', $attribute);

        session()->flash('success', trans('admin::app.settings.attributes.create-success'));

        return redirect()->route('admin.settings.attributes.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $attribute = $this->attributeRepository->findOrFail($id);

        return view('admin::settings.attributes.edit', compact('attribute'));
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
            'code' => ['required', 'unique:attributes,code,' . $id, new Code],
            'name' => 'required',
            'type' => 'required',
        ]);

        Event::dispatch('settings.attribute.update.before', $id);

        $attribute = $this->attributeRepository->update(request()->all(), $id);

        Event::dispatch('settings.attribute.update.after', $attribute);

        session()->flash('success', trans('admin::app.settings.attributes.update-success'));

        return redirect()->route('admin.settings.attributes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $attribute = $this->attributeRepository->findOrFail($id);

        if (! $attribute->is_user_defined) {
            session()->flash('error', trans('admin::app.settings.attributes.user-define-error'));
        } else {
            try {
                Event::dispatch('settings.attribute.delete.before', $id);

                $this->attributeRepository->delete($id);

                Event::dispatch('settings.attribute.delete.after', $id);

                return response()->json([
                    'status'    => true,
                    'message'   => trans('admin::app.datagrid.destroy-success', ['resource' => trans('admin::app.settings.attributes.attribute')]),
                ], 200);
            } catch(\Exception $e) {
                return response()->json([
                    'status'    => false,
                    'message'   => trans('admin::app.settings.attributes.delete-failed'),
                ], 400);
            }
        }

        return response()->json([
            'status'    => false,
            'message'   => trans('admin::app.settings.attributes.delete-failed'),
        ], 400);
    }

    /**
     * Search attribute lookup results
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function search($id)
    {
        $results = $this->attributeRepository->getAttributeLookUpOptions($id, request()->input('query'));

        return response()->json($results);
    }

    /**
     * Mass Delete the specified resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        $data = request()->all();

        $this->attributeRepository->destroy($data['rows']);

        return response()->json([
            'status'    => true,
            'message'   => trans('admin::app.datagrid.destroy-success', ['resource' => trans('admin::app.settings.attributes.title')]),
        ]);
    }
}
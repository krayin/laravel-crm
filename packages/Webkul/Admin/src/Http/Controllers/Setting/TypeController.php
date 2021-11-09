<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Lead\Repositories\TypeRepository;

class TypeController extends Controller
{
    /**
     * TypeRepository object
     *
     * @var \Webkul\User\Repositories\TypeRepository
     */
    protected $typeRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Lead\Repositories\TypeRepository  $typeRepository
     * @return void
     */
    public function __construct(TypeRepository $typeRepository)
    {
        $this->typeRepository = $typeRepository;
    }

    /**
     * Display a listing of the type.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(\Webkul\Admin\DataGrids\Setting\TypeDataGrid::class)->toJson();
        }

        return view('admin::settings.types.index');
    }

    /**
     * Store a newly created type in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required|unique:lead_types,name'
        ]);
        
        if ($validator->fails()) {
            session()->flash('error', trans('admin::app.settings.types.name-exists'));

            return redirect()->back();
        }

        Event::dispatch('settings.type.create.before');

        $type = $this->typeRepository->create(request()->all());

        Event::dispatch('settings.type.create.after', $type);

        session()->flash('success', trans('admin::app.settings.types.create-success'));

        return redirect()->route('admin.settings.types.index');
    }

    /**
     * Show the form for editing the specified type.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $type = $this->typeRepository->findOrFail($id);

        return view('admin::settings.types.edit', compact('type'));
    }

    /**
     * Update the specified type in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'name' => 'required|unique:lead_types,name,' . $id,
        ]);

        Event::dispatch('settings.type.update.before', $id);

        $type = $this->typeRepository->update(request()->all(), $id);

        Event::dispatch('settings.type.update.after', $type);

        session()->flash('success', trans('admin::app.settings.types.update-success'));

        return redirect()->route('admin.settings.types.index');
    }

    /**
     * Remove the specified type from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $type = $this->typeRepository->findOrFail($id);

        try {
            Event::dispatch('settings.type.delete.before', $id);

            $this->typeRepository->delete($id);

            Event::dispatch('settings.type.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.settings.types.delete-success'),
            ], 200);
        } catch(\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.settings.types.delete-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('admin::app.settings.types.delete-failed'),
        ], 400);
    }
}

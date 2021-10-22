<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Webkul\Tag\Repositories\TagRepository;
use Webkul\Admin\Http\Controllers\Controller;

class TagController extends Controller
{
    /**
     * TagRepository object
     *
     * @var \Webkul\User\Repositories\TagRepository
     */
    protected $tagRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Tag\Repositories\TagRepository  $tagRepository
     * @return void
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(\Webkul\Admin\DataGrids\Setting\TagDataGrid::class)->toJson();
        }

        return view('admin::settings.tags.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        if (request()->ajax()) {
            $this->validate(request(), [
                'name' => 'required|unique:tags,name',
            ]);
        } else {
            $validator = Validator::make(request()->all(), [
                'name' => 'required|unique:tags,name',
            ]);

            if ($validator->fails()) {
                session()->flash('error', $validator->errors()->first('name'));

                return redirect()->back();
            }
        }

        Event::dispatch('settings.tag.create.before');

        $tag = $this->tagRepository->create(array_merge([
            'user_id' => auth()->guard('user')->user()->id,
        ], request()->all()));

        Event::dispatch('settings.tag.create.after', $tag);

        if (request()->ajax()) {
            return response()->json([
                'tag'     => $tag,
                'status'  => true,
                'message' => trans('admin::app.settings.tags.create-success'),
            ]);
        } else {
            session()->flash('success', trans('admin::app.settings.tags.create-success'));

            return redirect()->route('admin.settings.tags.index');
        }
    }

    /**
     * Show the form for editing the specified tag.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $tag = $this->tagRepository->findOrFail($id);

        return view('admin::settings.tags.edit', compact('tag'));
    }

    /**
     * Update the specified tag in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required|unique:tags,name,' . $id,
        ]);

        if ($validator->fails()) {
            session()->flash('error', $validator->errors()->first('name'));

            return redirect()->back();
        }
        
        Event::dispatch('settings.tag.update.before', $id);

        $tag = $this->tagRepository->update(request()->all(), $id);

        Event::dispatch('settings.tag.update.after', $tag);

        session()->flash('success', trans('admin::app.settings.tags.update-success'));

        return redirect()->route('admin.settings.tags.index');
    }

    /**
     * Remove the specified type from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $type = $this->tagRepository->findOrFail($id);

        try {
            Event::dispatch('settings.tag.delete.before', $id);

            $this->tagRepository->delete($id);

            Event::dispatch('settings.tag.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.settings.tags.delete-success'),
            ], 200);
        } catch(\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.settings.tags.delete-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('admin::app.settings.tags.delete-failed'),
        ], 400);
    }

    /**
     * Search tag results
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        $results = $this->tagRepository->findWhere([
            ['name', 'like', '%' . urldecode(request()->input('query')) . '%']
        ]);

        return response()->json($results);
    }

    /**
     * Mass Delete the specified resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        foreach (request('rows') as $tagId) {
            Event::dispatch('settings.tag.delete.before', $tagId);

            $this->tagRepository->delete($tagId);

            Event::dispatch('settings.tag.delete.after', $tagId);
        }

        return response()->json([
            'message' => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.settings.tags.title')]),
        ]);
    }
}
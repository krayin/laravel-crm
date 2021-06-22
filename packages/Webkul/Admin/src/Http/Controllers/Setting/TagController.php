<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Event;
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
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'name' => 'required',
        ]);

        Event::dispatch('settings.tag.create.before');

        $tag = $this->tagRepository->create(array_merge([
            'user_id' => auth()->guard('user')->user()->id,
        ], request()->all()));

        Event::dispatch('settings.tag.create.after', $tag);

        if (request()->ajax()) {
            return response()->json([
                'tag'     => $tag,
                'status'  => true,
                'message' => trans('admin::app.tags.create-success'),
            ]);
        } else {
            session()->flash('success', trans('admin::app.tags.create-success'));

            return redirect()->route('admin.settings.tags.index');
        }
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
}
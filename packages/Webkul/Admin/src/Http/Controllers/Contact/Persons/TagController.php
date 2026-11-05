<?php

namespace Webkul\Admin\Http\Controllers\Contact\Persons;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Contact\Repositories\PersonRepository;

class TagController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected PersonRepository $personRepository) {}

    /**
     * Store a newly created resource in storage.
     */
    public function attach(int $id): JsonResponse
    {
        $person = $this->personRepository->findOrFail($id);

        $this->preventUnauthorizedAccess($person->user_id);

        Event::dispatch('persons.tag.create.before', $id);

        if (! $person->tags->contains(request()->input('tag_id'))) {
            $person->tags()->attach(request()->input('tag_id'));
        }

        Event::dispatch('persons.tag.create.after', $person);

        return response()->json([
            'message' => trans('admin::app.contacts.persons.view.tags.create-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function detach(int $personId): JsonResponse
    {
        $person = $this->personRepository->findOrFail($personId);

        $this->preventUnauthorizedAccess($person->user_id);

        Event::dispatch('persons.tag.delete.before', $personId);

        $person->tags()->detach(request()->input('tag_id'));

        Event::dispatch('persons.tag.delete.after', $person);

        return response()->json([
            'message' => trans('admin::app.contacts.persons.view.tags.destroy-success'),
        ]);
    }
}

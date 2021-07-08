<?php

namespace Webkul\Admin\Http\Controllers\Contact;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;

use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Notifications\Person\Create;
use Webkul\Attribute\Http\Requests\AttributeForm;
use Webkul\Contact\Repositories\PersonRepository;

class PersonController extends Controller
{
    /**
     * PersonRepository object
     *
     * @var \Webkul\Product\Repositories\PersonRepository
     */
    protected $personRepository;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Product\Repositories\PersonRepository  $personRepository
     *
     * @return void
     */
    public function __construct(PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;

        request()->request->add(['entity_type' => 'persons']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin::contacts.persons.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Webkul\Attribute\Http\Requests\AttributeForm $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        Event::dispatch('contacts.person.create.before');

        $person = $this->personRepository->create(request()->all());

        try {
            // Mail::queue(new Create($person));
        } catch (\Exception $e) {
            report($e);
        }

        Event::dispatch('contacts.person.create.after', $person);
        
        session()->flash('success', trans('admin::app.contacts.persons.create-success'));

        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $person = $this->personRepository->findOrFail($id);

        return view('admin::contacts.persons.edit', compact('person'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Webkul\Attribute\Http\Requests\AttributeForm $request
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeForm $request, $id)
    {
        Event::dispatch('contacts.person.update.before');

        $person = $this->personRepository->update(request()->all(), $id);

        Event::dispatch('contacts.person.update.after', $person);
        
        session()->flash('success', trans('admin::app.contacts.persons.update-success'));

        return redirect()->route('admin.contacts.persons.index');
    }

    /**
     * Search person results
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        $results = $this->personRepository->findWhere([
            ['name', 'like', '%' . urldecode(request()->input('query')) . '%']
        ]);

        return response()->json($results);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->personRepository->findOrFail($id);
        
        try {
            Event::dispatch('contact.person.delete.before', $id);

            $this->personRepository->delete($id);

            Event::dispatch('contact.person.delete.after', $id);

            return response()->json([
                'status'    => true,
                'message'   => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.contacts.persons.person')]),
            ], 200);
        } catch(\Exception $exception) {
            return response()->json([
                'status'    => false,
                'message'   => trans('admin::app.response.destroy-failed', ['name' => trans('admin::app.contacts.persons.person')]),
            ], 400);
        }
    }

    /**
     * Mass Delete the specified resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        $data = request()->all();

        $this->personRepository->destroy($data['rows']);

        return response()->json([
            'status'    => true,
            'message'   => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.contacts.persons.title')])
        ]);
    }
}
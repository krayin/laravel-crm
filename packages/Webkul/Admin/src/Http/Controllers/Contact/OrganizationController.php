<?php

namespace Webkul\Admin\Http\Controllers\Contact;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Contact\Repositories\OrganizationRepository;

class OrganizationController extends Controller
{
    /**
     * OrganizationRepository object
     *
     * @var \Webkul\Product\Repositories\OrganizationRepository
     */
    protected $organizationRepository;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Product\Repositories\OrganizationRepository  $organizationRepository
     *
     * @return void
     */
    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;

        request()->request->add(['entity_type' => 'Webkul\Contact\Models\Organization']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin::contacts.organizations.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Webkul\Attribute\Http\Requests\AttributeForm $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        Event::dispatch('contacts.organization.create.before');

        $organization = $this->organizationRepository->create(request()->all());

        Event::dispatch('contacts.organization.create.after', $organization);
        
        session()->flash('success', trans('admin::app.contacts.organizations.create-success'));

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
        $organization = $this->organizationRepository->findOrFail($id);

        return view('admin::contacts.organizations.edit', compact('organization'));
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
        Event::dispatch('contacts.organization.update.before');

        $organization = $this->organizationRepository->update(request()->all(), $id);

        Event::dispatch('contacts.organization.update.after', $organization);
        
        session()->flash('success', trans('admin::app.contacts.organizations.update-success'));

        return redirect()->route('admin.contacts.organizations.index');
    }
}
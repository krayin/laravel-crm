<?php

namespace Webkul\Admin\Http\Controllers\Contact;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Contact\OrganizationDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Contact\Repositories\OrganizationRepository;

class OrganizationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected OrganizationRepository $organizationRepository)
    {
        request()->request->add(['entity_type' => 'organizations']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(OrganizationDataGrid::class)->process();
        }

        return view('admin::contacts.organizations.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin::contacts.organizations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeForm $request): RedirectResponse
    {
        Event::dispatch('contacts.organization.create.before');

        $organization = $this->organizationRepository->create(request()->all());

        Event::dispatch('contacts.organization.create.after', $organization);

        session()->flash('success', trans('admin::app.contacts.organizations.index.create-success'));

        return redirect()->route('admin.contacts.organizations.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $organization = $this->organizationRepository->findOrFail($id);

        return view('admin::contacts.organizations.edit', compact('organization'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttributeForm $request, int $id): RedirectResponse
    {
        Event::dispatch('contacts.organization.update.before', $id);

        $organization = $this->organizationRepository->update(request()->all(), $id);

        Event::dispatch('contacts.organization.update.after', $organization);

        session()->flash('success', trans('admin::app.contacts.organizations.index.update-success'));

        return redirect()->route('admin.contacts.organizations.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            Event::dispatch('contact.organization.delete.before', $id);

            $this->organizationRepository->delete($id);

            Event::dispatch('contact.organization.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.contacts.organizations.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.contacts.organizations.index.delete-failed'),
            ], 400);
        }
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $organizations = $this->organizationRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

        foreach ($organizations as $organization) {
            Event::dispatch('contact.organization.delete.before', $organization);

            $this->organizationRepository->delete($organization->id);

            Event::dispatch('contact.organization.delete.after', $organization);
        }

        return response()->json([
            'message' => trans('admin::app.contacts.organizations.index.delete-success'),
        ]);
    }
}

<?php

namespace Webkul\Admin\Http\Controllers\Settings\Marketing;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\Marketing\EventDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Marketing\Repositories\EventRepository;

class EventController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected EventRepository $eventRepository) {}

    /**
     * Display a listing of the marketing events.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(EventDataGrid::class)->process();
        }

        return view('admin::settings.marketing.events.index');
    }

    /**
     * Store a newly created marketing event in storage.
     */
    public function store(): JsonResponse
    {
        $validatedData = $this->validate(request(), [
            'name'        => 'required|max:60',
            'description' => 'required',
            'date'        => 'required|date|after_or_equal:today',
        ]);

        Event::dispatch('settings.marketing.events.create.before');

        $marketingEvent = $this->eventRepository->create($validatedData);

        Event::dispatch('settings.marketing.events.create.after', $marketingEvent);

        return response()->json([
            'message' => trans('admin::app.settings.marketing.events.index.create-success'),
            'data'    => $marketingEvent,
        ]);
    }

    /**
     * Update the specified marketing event in storage.
     */
    public function update(int $id): JsonResponse
    {
        $validatedData = $this->validate(request(), [
            'name'        => 'required|max:60',
            'description' => 'required',
            'date'        => 'required|date|after_or_equal:today',
        ]);

        Event::dispatch('settings.marketing.events.update.before', $id);

        $marketingEvent = $this->eventRepository->update($validatedData, $id);

        Event::dispatch('settings.marketing.events.update.after', $marketingEvent);

        return response()->json([
            'message' => trans('admin::app.settings.marketing.events.index.update-success'),
            'data'    => $marketingEvent,
        ]);
    }

    /**
     * Remove the specified marketing event from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        Event::dispatch('settings.marketing.events.delete.before', $id);

        $this->eventRepository->delete($id);

        Event::dispatch('settings.marketing.events.delete.after', $id);

        return response()->json([
            'message' => trans('admin::app.settings.marketing.events.index.delete-success'),
        ]);
    }

    /**
     * Remove the specified marketing events from storage.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $marketingEvents = $this->eventRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

        foreach ($marketingEvents as $marketingEvent) {
            Event::dispatch('settings.marketing.events.delete.before', $marketingEvent);

            $this->eventRepository->delete($marketingEvent->id);

            Event::dispatch('settings.marketing.events.delete.after', $marketingEvent);
        }

        return response()->json([
            'message' => trans('admin::app.settings.marketing.events.index.mass-delete-success'),
        ]);
    }
}

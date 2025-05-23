<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting\Marketing;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Webkul\Marketing\Repositories\EventRepository;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Request\MassDestroyRequest;
use Webkul\RestApi\Http\Resources\V1\Setting\EventResource;

class EventController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected EventRepository $eventRepository) {}

    /**
     * Display a listing of the marketing events.
     */
    public function index(): JsonResource
    {
        $events = $this->allResources($this->eventRepository);

        return EventResource::collection($events);
    }

    /**
     * Show resource.
     */
    public function show(int $id): EventResource
    {
        $resource = $this->eventRepository->find($id);

        return new EventResource($resource);
    }

    /**
     * Store a newly created marketing event in storage.
     */
    public function store(): JsonResource
    {
        $validatedData = $this->validate(request(), [
            'name'        => 'required|max:60',
            'description' => 'required',
            'date'        => 'required|date|after_or_equal:today',
        ]);

        Event::dispatch('settings.marketing.events.create.before');

        $marketingEvent = $this->eventRepository->create($validatedData);

        Event::dispatch('settings.marketing.events.create.after', $marketingEvent);

        return new JsonResource([
            'data'    => new EventResource($marketingEvent),
            'message' => trans('rest-api::app.settings.marketing.events.create-success'),
        ]);
    }

    /**
     * Update the specified marketing event in storage.
     */
    public function update(int $id): JsonResource
    {
        $validatedData = $this->validate(request(), [
            'name'        => 'required|max:60',
            'description' => 'required',
            'date'        => 'required|date|after_or_equal:today',
        ]);

        Event::dispatch('settings.marketing.events.update.before', $id);

        $marketingEvent = $this->eventRepository->update($validatedData, $id);

        Event::dispatch('settings.marketing.events.update.after', $marketingEvent);

        return new JsonResource([
            'data'    => new EventResource($marketingEvent),
            'message' => trans('rest-api::app.settings.marketing.events.update-success'),
        ]);
    }

    /**
     * Remove the specified marketing event from storage.
     */
    public function destroy(int $id): JsonResource
    {
        Event::dispatch('settings.marketing.events.delete.before', $id);

        $this->eventRepository->delete($id);

        Event::dispatch('settings.marketing.events.delete.after', $id);

        return new JsonResource([
            'message' => trans('rest-api::app.settings.marketing.events.destroy-success'),
        ]);
    }

    /**
     * Remove the specified marketing events from storage.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResource
    {
        $marketingEventIds = $massDestroyRequest->input('indices');

        foreach ($marketingEventIds as $marketingEventId) {
            $marketingEvent = $this->eventRepository->find($marketingEventId);

            if (! $marketingEvent) {
                continue;
            }

            Event::dispatch('settings.marketing.events.delete.before', $marketingEventId);

            $marketingEvent?->delete();

            Event::dispatch('settings.marketing.events.delete.after', $marketingEventId);
        }

        return new JsonResource([
            'message' => trans('rest-api::app.settings.marketing.events.destroy-success'),
        ]);
    }
}

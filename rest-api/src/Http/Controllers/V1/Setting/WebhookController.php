<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Requests\WebhookRequest;
use Webkul\Automation\Repositories\WebhookRepository;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Resources\V1\Setting\WebhookResource;

class WebhookController extends Controller
{
    public function __construct(protected WebhookRepository $webhookRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        $users = $this->allResources($this->webhookRepository);

        return WebhookResource::collection($users);
    }

    /**
     * Store the newly created resource in storage.
     */
    public function store(WebhookRequest $webhookRequest): JsonResponse
    {
        Event::dispatch('settings.webhook.create.before');

        $webhook = $this->webhookRepository->create($webhookRequest->validated());

        Event::dispatch('settings.webhook.create.after', $webhook);

        return response()->json([
            'data'    => new WebhookResource($webhook),
            'message' => trans('rest-api::app.settings.webhooks.create-success'),
        ]);
    }

    /**
     * Show resource.
     */
    public function show(int $id): WebhookResource
    {
        $webhook = $this->webhookRepository->findOrFail($id);

        return new WebhookResource($webhook);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WebhookRequest $webhookRequest, int $id): JsonResponse
    {
        Event::dispatch('settings.webhook.update.before', $id);

        $webhook = $this->webhookRepository->update($webhookRequest->validated(), $id);

        Event::dispatch('settings.webhook.update.after', $webhook);

        return response()->json([
            'data'    => new WebhookResource($webhook),
            'message' => trans('rest-api::app.settings.webhooks.update-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $webhook = $this->webhookRepository->findOrFail($id);

        Event::dispatch('settings.webhook.delete.before', $id);

        $webhook?->delete();

        Event::dispatch('settings.webhook.delete.after', $id);

        return response()->json([
            'message' => trans('rest-api::app.settings.webhooks.delete-success'),
        ]);
    }
}

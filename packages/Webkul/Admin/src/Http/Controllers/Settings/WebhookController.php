<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\WebhookDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Requests\WebhookRequest;
use Webkul\Automation\Repositories\WebhookRepository;

class WebhookController extends Controller
{
    public function __construct(protected WebhookRepository $webhookRepository) {}

    /**
     * Display the listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(WebhookDataGrid::class)->process();
        }

        return view('admin::settings.webhook.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin::settings.webhook.create');
    }

    /**
     * Store the newly created resource in storage.
     */
    public function store(WebhookRequest $webhookRequest): RedirectResponse
    {
        Event::dispatch('settings.webhook.create.before');

        $webhook = $this->webhookRepository->create($webhookRequest->validated());

        Event::dispatch('settings.webhook.create.after', $webhook);

        session()->flash('success', trans('admin::app.settings.webhooks.index.create-success'));

        return redirect()->route('admin.settings.webhooks.index');
    }

    /**
     * Store the newly created resource in storage.
     */
    public function edit(int $id): View
    {
        $webhook = $this->webhookRepository->findOrFail($id);

        return view('admin::settings.webhook.edit', compact('webhook'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WebhookRequest $webhookRequest, int $id): RedirectResponse
    {
        Event::dispatch('settings.webhook.update.before', $id);

        $webhook = $this->webhookRepository->update($webhookRequest->validated(), $id);

        Event::dispatch('settings.webhook.update.after', $webhook);

        session()->flash('success', trans('admin::app.settings.webhooks.index.update-success'));

        return redirect()->route('admin.settings.webhooks.index');
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
            'message' => trans('admin::app.settings.webhooks.index.delete-success'),
        ]);
    }
}

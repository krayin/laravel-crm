<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Webkul\Automation\Helpers\Entity;
use Webkul\EmailTemplate\Repositories\EmailTemplateRepository;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Resources\V1\Setting\EmailTemplateResource;

class EmailTemplateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected EmailTemplateRepository $emailTemplateRepository,
        protected Entity $workflowEntityHelper
    ) {}

    /**
     * Display a listing of the email template.
     */
    public function index(): JsonResource
    {
        $emailTemplates = $this->allResources($this->emailTemplateRepository);

        return EmailTemplateResource::collection($emailTemplates);
    }

    /**
     * Show resource.
     */
    public function show(int $id): EmailTemplateResource
    {
        $resource = $this->emailTemplateRepository->find($id);

        return new EmailTemplateResource($resource);
    }

    /**
     * Store a newly created email templates in storage.
     */
    public function store(): JsonResponse
    {
        $this->validate(request(), [
            'name'    => 'required',
            'subject' => 'required',
            'content' => 'required',
        ]);

        Event::dispatch('settings.email_templates.create.before');

        $emailTemplate = $this->emailTemplateRepository->create(request()->all());

        Event::dispatch('settings.email_templates.create.after', $emailTemplate);

        return response()->json([
            'data'    => new EmailTemplateResource($emailTemplate),
            'message' => trans('rest-api::app.settings.email-templates.create-success'),
        ]);
    }

    /**
     * Update the specified email template in storage.
     */
    public function update(int $id): JsonResponse
    {
        $this->validate(request(), [
            'name'    => 'required',
            'subject' => 'required',
            'content' => 'required',
        ]);

        Event::dispatch('settings.email_templates.update.before', $id);

        $emailTemplate = $this->emailTemplateRepository->update(request()->all(), $id);

        Event::dispatch('settings.email_templates.update.after', $emailTemplate);

        return response()->json([
            'data'    => new EmailTemplateResource($emailTemplate),
            'message' => trans('rest-api::app.settings.email-templates.update-success'),
        ]);
    }

    /**
     * Remove the specified email template from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            Event::dispatch('settings.email_templates.delete.before', $id);

            $this->emailTemplateRepository->delete($id);

            Event::dispatch('settings.email_templates.delete.after', $id);

            return response()->json([
                'message' => trans('rest-api::app.settings.email-templates.delete-success'),
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('rest-api::app.settings.email-templates.delete-failed'),
            ], 500);
        }
    }
}

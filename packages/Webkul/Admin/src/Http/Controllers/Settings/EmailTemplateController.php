<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\EmailTemplateDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Automation\Helpers\Entity;
use Webkul\EmailTemplate\Repositories\EmailTemplateRepository;

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
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(EmailTemplateDataGrid::class)->process();
        }

        return view('admin::settings.email-templates.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $placeholders = $this->workflowEntityHelper->getEmailTemplatePlaceholders();

        return view('admin::settings.email-templates.create', compact('placeholders'));
    }

    /**
     * Store a newly created email templates in storage.
     */
    public function store(): RedirectResponse
    {
        $this->validate(request(), [
            'name'    => 'required|unique:email_templates,name',
            'subject' => 'required',
            'content' => 'required',
        ]);

        Event::dispatch('settings.email_templates.create.before');

        $emailTemplate = $this->emailTemplateRepository->create(request()->all());

        Event::dispatch('settings.email_templates.create.after', $emailTemplate);

        session()->flash('success', trans('admin::app.settings.email-template.index.create-success'));

        return redirect()->route('admin.settings.email_templates.index');
    }

    /**
     * Show the form for editing the specified email template.
     */
    public function edit(int $id): View
    {
        $emailTemplate = $this->emailTemplateRepository->findOrFail($id);

        $placeholders = $this->workflowEntityHelper->getEmailTemplatePlaceholders();

        return view('admin::settings.email-templates.edit', compact('emailTemplate', 'placeholders'));
    }

    /**
     * Update the specified email template in storage.
     */
    public function update(int $id): RedirectResponse
    {
        $this->validate(request(), [
            'name'    => 'required|unique:email_templates,name,'.$id,
            'subject' => 'required',
            'content' => 'required',
        ]);

        Event::dispatch('settings.email_templates.update.before', $id);

        $emailTemplate = $this->emailTemplateRepository->update(request()->all(), $id);

        Event::dispatch('settings.email_templates.update.after', $emailTemplate);

        session()->flash('success', trans('admin::app.settings.email-template.index.update-success'));

        return redirect()->route('admin.settings.email_templates.index');
    }

    /**
     * Remove the specified email template from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $emailTemplate = $this->emailTemplateRepository->findOrFail($id);

        try {
            Event::dispatch('settings.email_templates.delete.before', $id);

            $emailTemplate->delete($id);

            Event::dispatch('settings.email_templates.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.settings.email-template.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.settings.email-template.index.delete-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('admin::app.settings.email-template.index.delete-failed'),
        ], 400);
    }
}

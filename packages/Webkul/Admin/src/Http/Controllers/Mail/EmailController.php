<?php

namespace Webkul\Admin\Http\Controllers\Mail;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Mail\EmailDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Requests\MassUpdateRequest;
use Webkul\Admin\Http\Resources\EmailResource;
use Webkul\Email\Enums\SupportedFolderEnum;
use Webkul\Email\InboundEmailProcessor\Contracts\InboundEmailProcessor;
use Webkul\Email\Mails\Email;
use Webkul\Email\Repositories\AttachmentRepository;
use Webkul\Email\Repositories\EmailRepository;
use Webkul\Lead\Repositories\LeadRepository;

class EmailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected LeadRepository $leadRepository,
        protected EmailRepository $emailRepository,
        protected AttachmentRepository $attachmentRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse|RedirectResponse
    {
        $route = request('route');

        if (! $route) {
            return redirect()->route('admin.mail.index', ['route' => SupportedFolderEnum::INBOX->value]);
        }

        if (! bouncer()->hasPermission('mail.'.$route)) {
            abort(401, trans('admin::app.mail.unauthorized'));
        }

        if (request()->ajax()) {
            return datagrid(EmailDataGrid::class)->process();
        }

        return view('admin::mail.index', compact('route'));
    }

    /**
     * Display a resource.
     *
     * @return \Illuminate\View\View
     */
    public function view()
    {
        $route = request('route');

        $email = $this->emailRepository
            ->with([
                'emails',
                'attachments',
                'emails.attachments',
                'lead',
                'lead.person',
                'lead.tags',
                'lead.source',
                'lead.type',
                'person',
            ])
            ->findOrFail(request('id'));

        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $results = $this->leadRepository->findWhere([
                ['id', '=', $email->lead_id],
                ['user_id', 'IN', $userIds],
            ]);
        } else {
            $results = $this->leadRepository->findWhere([
                ['id', '=', $email->lead_id],
            ]);
        }

        if (empty($results->toArray())) {
            unset($email->lead_id);
        }

        if ($route == SupportedFolderEnum::DRAFT->value) {
            return response()->json([
                'data' => new EmailResource($email),
            ]);
        }

        return view('admin::mail.view', compact('email', 'route'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'reply_to'   => 'required|array|min:1',
            'reply_to.*' => 'email',
            'reply'      => 'required',
        ]);

        Event::dispatch('email.create.before');

        $email = $this->emailRepository->create(request()->all());

        if (! request('is_draft')) {
            try {
                Mail::send(new Email($email));

                $this->emailRepository->update([
                    'folders' => [SupportedFolderEnum::SENT->value],
                ], $email->id);
            } catch (\Exception $e) {
            }
        }

        Event::dispatch('email.create.after', $email);

        if (request()->ajax()) {
            return response()->json([
                'data'    => new EmailResource($email),
                'message' => trans('admin::app.mail.create-success'),
            ]);
        }

        if (request('is_draft')) {
            session()->flash('success', trans('admin::app.mail.saved-to-draft'));

            return redirect()->route('admin.mail.index', ['route' => SupportedFolderEnum::DRAFT->value]);
        }

        session()->flash('success', trans('admin::app.mail.create-success'));

        return redirect()->route('admin.mail.index', ['route'   => SupportedFolderEnum::SENT->value]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        Event::dispatch('email.update.before', $id);

        $data = request()->all();

        if (! is_null(request('is_draft'))) {
            $data['folders'] = request('is_draft') ? [SupportedFolderEnum::DRAFT->value] : [SupportedFolderEnum::OUTBOX->value];
        }

        $email = $this->emailRepository->update($data, request('id') ?? $id);

        Event::dispatch('email.update.after', $email);

        if (! is_null(request('is_draft')) && ! request('is_draft')) {
            try {
                Mail::send(new Email($email));

                $this->emailRepository->update([
                    'folders' => [SupportedFolderEnum::INBOX->value, SupportedFolderEnum::SENT->value],
                ], $email->id);
            } catch (\Exception $e) {
            }
        }

        if (! is_null(request('is_draft'))) {
            if (request('is_draft')) {
                session()->flash('success', trans('admin::app.mail.saved-to-draft'));

                return redirect()->route('admin.mail.index', ['route' => SupportedFolderEnum::DRAFT->value]);
            } else {
                session()->flash('success', trans('admin::app.mail.create-success'));

                return redirect()->route('admin.mail.index', ['route' => SupportedFolderEnum::INBOX->value]);
            }
        }

        if (request()->ajax()) {
            return response()->json([
                'data'    => new EmailResource($email->refresh()),
                'message' => trans('admin::app.mail.update-success'),
            ]);
        }

        session()->flash('success', trans('admin::app.mail.update-success'));

        return redirect()->back();
    }

    /**
     * Run process inbound parse email.
     *
     * @return \Illuminate\Http\Response
     */
    public function inboundParse(InboundEmailProcessor $inboundEmailProcessor)
    {
        $inboundEmailProcessor->processMessage(request('email'));

        return response()->json([], 200);
    }

    /**
     * Download file from storage
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function download($id)
    {
        $attachment = $this->attachmentRepository->findOrFail($id);

        try {
            return Storage::download($attachment->path);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());

            return redirect()->back();
        }
    }

    /**
     * Mass Update the specified resources.
     */
    public function massUpdate(MassUpdateRequest $massUpdateRequest): JsonResponse
    {
        $emails = $this->emailRepository->findWhereIn('id', $massUpdateRequest->input('indices'));

        try {
            foreach ($emails as $email) {
                Event::dispatch('email.update.before', $email->id);

                $this->emailRepository->update([
                    'folders' => request('folders'),
                ], $email->id);

                Event::dispatch('email.update.after', $email->id);
            }

            return response()->json([
                'message' => trans('admin::app.mail.mass-update-success'),
            ]);
        } catch (Exception) {
            return response()->json([
                'message' => trans('admin::app.mail.mass-update-success'),
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse|RedirectResponse
    {
        $email = $this->emailRepository->findOrFail($id);

        try {
            Event::dispatch('email.'.request('type').'.before', $id);

            $parentId = $email->parent_id;

            if (request('type') == SupportedFolderEnum::TRASH->value) {
                $this->emailRepository->update([
                    'folders' => [SupportedFolderEnum::TRASH->value],
                ], $id);
            } else {
                $this->emailRepository->delete($id);
            }

            Event::dispatch('email.'.request('type').'.after', $id);

            if (request()->ajax()) {
                return response()->json([
                    'message' => trans('admin::app.mail.delete-success'),
                ], 200);
            }

            session()->flash('success', trans('admin::app.mail.delete-success'));

            if ($parentId) {
                return redirect()->back();
            }

            return redirect()->route('admin.mail.index', ['route' => SupportedFolderEnum::INBOX->value]);
        } catch (\Exception $exception) {
            if (request()->ajax()) {
                return response()->json([
                    'message' => trans('admin::app.mail.delete-failed'),
                ], 400);
            }

            session()->flash('error', trans('admin::app.mail.delete-failed'));

            return redirect()->back();
        }
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $mails = $this->emailRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

        try {
            foreach ($mails as $email) {
                Event::dispatch('email.'.$massDestroyRequest->input('type').'.before', $email->id);

                if ($massDestroyRequest->input('type') == SupportedFolderEnum::TRASH->value) {
                    $this->emailRepository->update(['folders' => [SupportedFolderEnum::TRASH->value]], $email->id);
                } else {
                    $this->emailRepository->delete($email->id);
                }

                Event::dispatch('email.'.$massDestroyRequest->input('type').'.after', $email->id);
            }

            return response()->json([
                'message' => trans('admin::app.mail.delete-success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('admin::app.mail.delete-success'),
            ]);
        }
    }
}

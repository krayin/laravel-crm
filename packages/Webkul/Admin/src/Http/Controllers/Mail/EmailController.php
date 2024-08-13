<?php

namespace Webkul\Admin\Http\Controllers\Mail;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Mail\EmailDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Resources\EmailResource;
use Webkul\Email\Mails\Email;
use Webkul\Email\Repositories\AttachmentRepository;
use Webkul\Email\Repositories\EmailRepository;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\User\Repositories\UserRepository;

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
        if (! request('route')) {
            return redirect()->route('admin.mail.index', ['route' => 'inbox']);
        }

        if (! bouncer()->hasPermission('mail.'.request('route'))) {
            abort(401, 'This action is unauthorized');
        }

        switch (request('route')) {
            case 'compose':
                return view('admin::mail.compose');

            default:
                if (request()->ajax()) {
                    return datagrid(EmailDataGrid::class)->process();
                }

                return view('admin::mail.index');
        }
    }

    /**
     * Display a resource.
     *
     * @return \Illuminate\View\View
     */
    public function view()
    {
        $email = $this->emailRepository
            ->with(['emails', 'attachments', 'emails.attachments', 'lead', 'lead.tags', 'lead.source', 'lead.type', 'person'])
            ->findOrFail(request('id'));

        $currentUser = auth()->guard('user')->user();

        if ($currentUser->view_permission == 'individual') {
            $results = $this->leadRepository->findWhere([
                ['id', '=', $email->lead_id],
                ['user_id', '=', $currentUser->id],
            ]);
        } elseif ($currentUser->view_permission == 'group') {
            $userIds = app(UserRepository::class)->getCurrentUserGroupsUserIds();

            $results = $this->leadRepository->findWhere([
                ['id', '=', $email->lead_id],
                ['user_id', 'IN', $userIds],
            ]);
        } elseif ($currentUser->view_permission == 'global') {
            $results = $this->leadRepository->findWhere([
                ['id', '=', $email->lead_id],
            ]);
        }

        if (empty($results->toArray())) {
            unset($email->lead_id);
        }

        if (request('route') == 'draft') {
            return response()->json([
                'data' => new EmailResource($email),
            ]);
        }

        return view('admin::mail.view', compact('email'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'reply_to' => 'required|array|min:1',
            'reply'    => 'required',
        ]);

        Event::dispatch('email.create.before');

        $email = $this->emailRepository->create(request()->all());

        if (! request('is_draft')) {
            try {
                Mail::send(new Email($email));

                $this->emailRepository->update([
                    'folders' => ['sent'],
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

            return redirect()->route('admin.mail.index', ['route' => 'draft']);
        }

        session()->flash('success', trans('admin::app.mail.create-success'));

        return redirect()->route('admin.mail.index', ['route'   => 'sent']);
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
            $data['folders'] = request('is_draft') ? ['draft'] : ['outbox'];
        }

        $email = $this->emailRepository->update($data, request('id') ?? $id);

        Event::dispatch('email.update.after', $email);

        if (! is_null(request('is_draft')) && ! request('is_draft')) {
            try {
                Mail::send(new Email($email));

                $this->emailRepository->update([
                    'folders' => ['inbox', 'sent'],
                ], $email->id);
            } catch (\Exception $e) {
            }
        }

        if (! is_null(request('is_draft'))) {
            if (request('is_draft')) {
                session()->flash('success', trans('admin::app.mail.saved-to-draft'));

                return redirect()->route('admin.mail.index', ['route' => 'draft']);
            } else {
                session()->flash('success', trans('admin::app.mail.create-success'));

                return redirect()->route('admin.mail.index', ['route' => 'inbox']);
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
     * Run process inbound parse email
     *
     * @return \Illuminate\Http\Response
     */
    public function inboundParse()
    {
        $this->emailRepository->processInboundParseMail(request('email'));

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
     *
     * @return \Illuminate\Http\Response
     */
    public function massUpdate()
    {
        foreach (request('rows') as $emailId) {
            Event::dispatch('email.update.before', $emailId);

            $this->emailRepository->update([
                'folders' => request('folders'),
            ], $emailId);

            Event::dispatch('email.update.after', $emailId);
        }

        return response()->json([
            'message' => trans('admin::app.mail.mass-update-success'),
        ]);
    }

    /*
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $email = $this->emailRepository->findOrFail($id);

        try {
            Event::dispatch('email.'.request('type').'.before', $id);

            $parentId = $email->parent_id;

            if (request('type') == 'trash') {
                $data = $this->emailRepository->update([
                    'folders' => ['trash'],
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

            return redirect()->route('admin.mail.index', ['route' => 'inbox']);
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
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        foreach (request('rows') as $emailId) {
            Event::dispatch('email.'.request('type').'.before', $emailId);

            if (request('type') == 'trash') {
                $this->emailRepository->update([
                    'folders' => ['trash'],
                ], $emailId);
            } else {
                $this->emailRepository->delete($emailId);
            }

            Event::dispatch('email.'.request('type').'.after', $emailId);
        }

        return response()->json([
            'message' => trans('admin::app.mail.delete-success'),
        ]);
    }
}

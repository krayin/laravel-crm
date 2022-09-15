<?php

namespace Webkul\Admin\Http\Controllers\Mail;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Webkul\Email\Mails\Email;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Email\Repositories\EmailRepository;
use Webkul\Email\Repositories\AttachmentRepository;

class EmailController extends Controller
{
    /**
     * LeadRepository object
     *
     * @var \Webkul\Email\Repositories\LeadRepository
     */
    protected $leadRepository;

    /**
     * EmailRepository object
     *
     * @var \Webkul\Email\Repositories\EmailRepository
     */
    protected $emailRepository;

    /**
     * AttachmentRepository object
     *
     * @var \Webkul\Email\Repositories\AttachmentRepository
     */
    protected $attachmentRepository;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Lead\Repositories\LeadRepository  $leadRepository
     * @param \Webkul\Email\Repositories\EmailRepository  $emailRepository
     * @param \Webkul\Email\Repositories\AttachmentRepository  $attachmentRepository
     *
     * @return void
     */
    public function __construct(
        LeadRepository $leadRepository,
        EmailRepository $emailRepository,
        AttachmentRepository $attachmentRepository
    )
    {
        $this->leadRepository = $leadRepository;

        $this->emailRepository = $emailRepository;

        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (! request('route')) {
            return redirect()->route('admin.mail.index', ['route' => 'inbox']);
        }

        if (! bouncer()->hasPermission('mail.' . request('route'))) {
            abort(401, 'This action is unauthorized');
        }

        switch (request('route')) {
            case 'compose':
                return view('admin::mail.compose');

            default:
                if (request()->ajax()) {
                    return app(\Webkul\Admin\DataGrids\Mail\EmailDataGrid::class)->toJson();
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
            ->with(['emails', 'attachments', 'emails.attachments', 'lead', 'person'])
            ->findOrFail(request('id'));

        $currentUser = auth()->guard('user')->user();
        
        if ($currentUser->view_permission == 'individual') {            
            $results = $this->leadRepository->findWhere([
                ['id', '=', $email->lead_id],
                ['user_id', '=', $currentUser->id],
            ]);
        } elseif ($currentUser->view_permission == 'group') {
            $userIds = app('\Webkul\User\Repositories\UserRepository')->getCurrentUserGroupsUserIds();

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
            return view('admin::mail.compose', compact('email'));
        } else {
            return view('admin::mail.view', compact('email'));
        }
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

        $uniqueId = time() . '@' . config('mail.domain');

        $referenceIds = [];

        if ($parentId = request('parent_id')) {
            $parent = $this->emailRepository->findOrFail($parentId);

            $referenceIds = $parent->reference_ids ?? [];
        }

        $email = $this->emailRepository->create(array_merge(request()->all(), [
            'source'        => 'web',
            'from'          => env('MAIL_FROM_ADDRESS', 'admin@example.com'),
            'user_type'     => 'admin',
            'folders'       => request('is_draft') ? ['draft'] : ['outbox'],
            'name'          => auth()->guard('user')->user()->name,
            'unique_id'     => $uniqueId,
            'message_id'    => $uniqueId,
            'reference_ids' => array_merge($referenceIds, [$uniqueId]),
            'user_id'       => auth()->guard('user')->user()->id,
        ]));

        if (! request('is_draft')) {
            try {
                Mail::send(new Email($email));

                $this->emailRepository->update([
                    'folders' => ['inbox', 'sent']
                ], $email->id);
            } catch (\Exception $e) {}
        }

        Event::dispatch('email.create.after', $email);

        if (request('is_draft')) {
            session()->flash('success', trans('admin::app.mail.saved-to-draft'));

            return redirect()->route('admin.mail.index', ['route' => 'draft']);
        }

        session()->flash('success', trans('admin::app.mail.create-success'));

        return redirect()->route('admin.mail.index', ['route'   => 'inbox']);
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
                    'folders' => ['inbox', 'sent']
                ], $email->id);
            } catch (\Exception $e) {}
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
            $response = [
                'message' => trans('admin::app.mail.update-success'),
            ];

            if (request('lead_id')) {
                $response['html'] = view('admin::common.custom-attributes.view', [
                    'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        'entity_type' => 'leads',
                    ]),
                    'entity'           => $this->leadRepository->find(request('lead_id')),
                ])->render();
            }

            return response()->json($response);
        } else {
            session()->flash('success', trans('admin::app.mail.update-success'));

            return redirect()->back();

        }
    }

    /**
     * Run process inbound parse email
     *
     * @return \Illuminate\Http\Response
     */
    public function inboundParse()
    {
        $emailContent = file_get_contents(base_path('email.txt'));

        $this->emailRepository->processInboundParseMail($emailContent);

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

        return Storage::download($attachment->path);
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
            Event::dispatch('email.' . request('type') . '.before', $id);

            $parentId = $email->parent_id;

            if (request('type') == 'trash') {
                $this->emailRepository->update([
                    'folders' => ['trash'],
                ], $id);
            } else {
                $this->emailRepository->delete($id);
            }

            Event::dispatch('email.' . request('type') . '.after', $id);

            if (request()->ajax()) {
                return response()->json([
                    'message' => trans('admin::app.mail.delete-success'),
                ], 200);
            } else {
                session()->flash('success', trans('admin::app.mail.delete-success'));

                if ($parentId) {
                    return redirect()->back();
                } else {
                    return redirect()->route('admin.mail.index', ['route' => 'inbox']);
                }
            }
        } catch(\Exception $exception) {
            if (request()->ajax()) {
                return response()->json([
                    'message' => trans('admin::app.mail.delete-failed'),
                ], 400);
            } else {
                session()->flash('error', trans('admin::app.mail.delete-failed'));

                return redirect()->back();
            }
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
            Event::dispatch('email.' . request('type') . '.before', $emailId);

            if (request('type') == 'trash') {
                $this->emailRepository->update([
                    'folders' => ['trash'],
                ], $emailId);
            } else {
                $this->emailRepository->delete($emailId);
            }

            Event::dispatch('email.' . request('type') . '.after', $emailId);
        }

        return response()->json([
            'message' => trans('admin::app.mail.destroy-success'),
        ]);
    }
}

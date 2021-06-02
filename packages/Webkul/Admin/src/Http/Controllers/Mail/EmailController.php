<?php

namespace Webkul\Admin\Http\Controllers\Mail;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Webkul\Email\Mails\Email;
use Webkul\Email\Repositories\EmailRepository;

class EmailController extends Controller
{
    /**
     * EmailRepository object
     *
     * @var \Webkul\Email\Repositories\EmailRepository
     */
    protected $emailRepository;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Email\Repositories\EmailRepository  $emailRepository
     *
     * @return void
     */
    public function __construct(EmailRepository $emailRepository)
    {
        $this->emailRepository = $emailRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        switch (request('route')) {
            case 'compose':
                return view('admin::mail.compose');
            
            default:
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
        $email = $this->emailRepository->findOrFail(request('id'));

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
            'subject'  => 'required',
            'reply'    => 'required',
        ]);

        Event::dispatch('email.create.before');

        $email = $this->emailRepository->create(array_merge(request()->all(), [
            'source'        => 'web',
            'from'          => 'admin@example.com',
            'user_type'     => 'admin',
            'folders'       => request('is_draft') ? ['draft'] : ['outbox'],
            'name'          => auth()->guard('user')->user()->name,
            'unique_id'     => $uniqueId = time() . '@example.com',
            'message_id'    => $uniqueId,
            'reference_ids' => [$uniqueId],
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

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        Event::dispatch('email.update.before');

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
        $emailContent = file_get_contents(base_path('email.txt'));

        $this->emailRepository->processInboundParseMail($emailContent);

        return response()->json([], 200);
    }

    /*
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->emailRepository->findOrFail($id);
        
        try {
            Event::dispatch('email.delete.before', $id);

            $this->emailRepository->delete($id);

            Event::dispatch('email.delete.after', $id);

            return response()->json([
                'status'    => true,
                'message'   => trans('admin::app.mail.destroy-success'),
            ], 200);
        } catch(\Exception $exception) {
            return response()->json([
                'status'  => false,
                'message' => trans('admin::app.mail.destroy-failed'),
            ], 400);
        }
    }
}
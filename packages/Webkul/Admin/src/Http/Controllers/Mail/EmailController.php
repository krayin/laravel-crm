<?php

namespace Webkul\Admin\Http\Controllers\Mail;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
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
            'source'    => 'web',
            'user_type' => 'admin',
            'folders'   => request('is_draft') ? ['draft'] : ['outbox'],
            'name'      => auth()->guard('user')->user()->name,
            'user_id'   => auth()->guard('user')->user()->id,
        ]));

        Event::dispatch('email.create.after', $email);

        if (request('is_draft')) {
            session()->flash('success', trans('admin::app.mail.saved-to-draft'));

            return redirect()->route('admin.mail.index', ['route' => 'draft']);
        }

        session()->flash('success', trans('admin::app.mail.create-success'));

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
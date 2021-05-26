<?php

namespace Webkul\Admin\Http\Controllers\Email;

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
        return view('admin::emails.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store($id)
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
            'name'      => auth()->guard('user')->user()->name,
            'user_id'   => auth()->guard('user')->user()->id,
        ]));

        Event::dispatch('email.create.after', $email);

        session()->flash('success', trans('admin::app.emails.create-success'));

        return redirect()->back();
    }

    /**
     * Run process inbound parse email
     *
     * @return \Illuminate\Http\Response
     */
    public function inboundParse()
    {
        $this->emailRepository->parseEmailAddress();

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
                'message'   => trans('admin::app.emails.destroy-success'),
            ], 200);
        } catch(\Exception $exception) {
            return response()->json([
                'status'  => false,
                'message' => trans('admin::app.emails.destroy-failed'),
            ], 400);
        }
    }
}
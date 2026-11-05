<?php

namespace Webkul\Admin\Http\Controllers\Mail;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Traits\ScopesMailAccess;
use Webkul\Email\Repositories\EmailRepository;

class TagController extends Controller
{
    use ScopesMailAccess;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected EmailRepository $emailRepository) {}

    /**
     * Store a newly created resource in storage.
     */
    public function attach(int $id): JsonResponse
    {
        $mail = $this->emailRepository->findOrFail($id);

        /**
         * Tagging writes to the mail, so it takes the same check as updating it.
         */
        $this->preventUnauthorizedMailAccess($mail);

        Event::dispatch('mails.tag.create.before', $id);

        if (! $mail->tags->contains(request()->input('tag_id'))) {
            $mail->tags()->attach(request()->input('tag_id'));
        }

        Event::dispatch('mails.tag.create.after', $mail);

        return response()->json([
            'message' => trans('admin::app.mail.view.tags.create-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function detach(int $mailId): JsonResponse
    {
        $mail = $this->emailRepository->findOrFail($mailId);

        $this->preventUnauthorizedMailAccess($mail);

        Event::dispatch('mails.tag.delete.before', $mailId);

        $mail->tags()->detach(request()->input('tag_id'));

        Event::dispatch('mails.tag.delete.after', $mail);

        return response()->json([
            'message' => trans('admin::app.mail.view.tags.destroy-success'),
        ]);
    }
}

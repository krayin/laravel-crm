<?php

namespace Webkul\RestApi\Http\Controllers\V1\Lead;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Webkul\RestApi\Http\Controllers\V1\Mail\EmailController as BaseEmailController;
use Webkul\RestApi\Http\Resources\V1\Activity\ActivityResource;

class EmailController extends BaseEmailController
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(): JsonResponse
    {
        $response = json_decode(parent::store()->getContent(), true);

        return response()->json([
            'data'    => $this->transformToActivity($response['data']),
            'message' => $response['message'],
        ]);

        return $response;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function detach(int $id): JsonResponse
    {
        Event::dispatch('email.update.before', request()->input('email_id'));

        $email = $this->emailRepository->update([
            'lead_id' => null,
        ], request()->input('email_id'));

        Event::dispatch('email.update.after', $email);

        return response()->json([
            'message' => trans('rest-api::app.mail.update-success'),
        ]);
    }

    /**
     * Transform the email data to activity resource.
     */
    public function transformToActivity(array $data): ActivityResource
    {
        return new ActivityResource((object) [
            'id'            => $data['id'],
            'parent_id'     => $data['parent_id'],
            'title'         => $data['subject'],
            'type'          => 'email',
            'is_done'       => 1,
            'comment'       => $data['reply'],
            'schedule_from' => null,
            'schedule_to'   => null,
            'user'          => auth()->user(),
            'participants'  => [],
            'location'      => null,
            'additional'    => json_encode([
                'folders' => $data['folders'],
                'from'    => $data['from'],
                'to'      => $data['reply_to'],
                'cc'      => $data['cc'],
                'bcc'     => $data['bcc'],
            ]),
            'files'         => array_map(function ($attachment) {
                return (object) $attachment;
            }, $data['attachments']),
            'created_at'    => $data['created_at'],
            'updated_at'    => $data['updated_at'],
        ]);
    }
}
